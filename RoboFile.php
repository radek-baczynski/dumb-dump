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

		$config = $this->getConfig($options['config']);

		$source = $config['config']['source'];
		$task   = $this->taskDump($output, $source['host'], $source['user'], $source['password']);

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
		$config     = $this->getConfig($options['config']);

		$dir  = $this->getOutputDir($definitionName);
		$file = $this->getOutputOneFile($dir, $definitionName);

		$destination = $config['config']['destination'];

		$task = $this->taskDumpRestore($destination['host'], $destination['user'], $destination['password']);
		$task->restore($file);
		$task->run();
	}

	protected function getConfig($file)
	{
		if(!file_exists($file))
		{
			throw new \Exception('Given  `'.$file.'` project file does not exists, create new one');
		}

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
		$dir = sys_get_temp_dir() . '/dumbdump/' . $definitionName;

		if(!is_dir($dir))
		{
			mkdir($dir, 0770, true);
		}

		return $dir;
	}

	protected function getOutputOneFile($dir, $definitionName)
	{
		return $dir . '/' . $definitionName . '.sql.gz';
	}
}
