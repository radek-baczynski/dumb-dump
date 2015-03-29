<?php
use DumbDump\Config\Configuration;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

/**
 * Created by PhpStorm.
 * User: Radek
 * Date: 03/12/14
 * Time: 17:35
 */
class RoboFile extends \Robo\Tasks
{
	use DumbDump\Task\DumpTrait;

	/**
	 * @param $configFile
	 * @param $definitionName
	 */
	function dbDump($definitionName, $options = ['config' => 'config.yml'])
	{
		$definition = $this->getDefinition($options['config'], $definitionName);
		$output     = $this->getOutputDir($definitionName);

		$task = $this->taskDump($output, 'localhost', 'root', '');

		$task->outputOneFile($this->getOutputOneFile($output, $definitionName));

		foreach ($definition['databases'] as $database => $def)
		{
			$task->schema($database)
				->gzip(true);

			$task->data($database, $def['includeData'], $def['excludeData'])
				->gzip(true);
		}

		$task->run();
	}

	/**
	 * @param $configFile
	 * @param $definitionName
	 */
	function dbRestore($definitionName, $options = ['config' => 'config.yml'])
	{
		$definition = $this->getDefinition($options['config'], $definitionName);

		$dir = $this->getOutputDir($definitionName);
		$source     = $this->getOutputOneFile($dir, $definitionName);

		$task = $this->taskRestore($source);

		$task->run();
	}

	protected function getConfig($file)
	{
		$fileContent = file_get_contents($file);
		$config      = Yaml::parse($fileContent);

		$processor              = new Processor();
		$configuration          = new Configuration();
		$processedConfiguration = $processor->processConfiguration(
			$configuration,
			[$config]
		);

		return $processedConfiguration;
	}

	public function pharBuild()
	{
		$this->taskComposerInstall()
			->printed(false)
			->noDev()
			->run();

		$packer = $this->taskPackPhar(self::PHAR_NAME);
		$files  = Finder::create()->ignoreVCS(true)
			->files()
			->name('*.php')
			->path('src')
			->path('vendor')
			->in(__DIR__);

		foreach ($files as $file)
		{
			$packer->addFile($file->getRelativePathname(), $file->getRealPath());
		}

		$packer->addFile('RoboFile.php', __FILE__);
		$packer->addFile('run', __DIR__ . '/runamb85');


		file_put_contents($file = tempnam(sys_get_temp_dir(), 'stub'), $this->getStub());
		$packer
			->stub($file)
			->run();

		unlink($file);

		$this->taskComposerInstall()
			->printed(false)
			->run();
	}

	protected function getDefinition($config, $definitionName)
	{
		$config = $this->getConfig($config);

		if (empty($config['definitions'][$definitionName]))
		{
			$str = sprintf('Definition for "%s" does not exists in your config file', $definitionName);

			throw new \Exception($str);
		}

		return $config['definitions'][$definitionName];
	}

	protected function getOutputDir($definitionName)
	{
		return sys_get_temp_dir() . '/dumbdump/' . $definitionName;
	}

	protected function getOutputOneFile($dir, $definitionName)
	{
		return $dir . '/' . $definitionName . '.sql.gz';
	}
}
