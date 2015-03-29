<?php
use Symfony\Component\Finder\Finder;

/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks
{
	public function pharBuild()
	{
		$packer = $this->taskPackPhar('dumbdump.phar');

		$files = Finder::create()->ignoreVCS(true)
			->files()
			->name('*.php')
			->path('src')
			->path('vendor')
			->in($root = realpath(__DIR__ . '/../'));

		foreach ($files as $file)
		{
			$packer->addFile($file->getRelativePathname(), $file->getRealPath());
		}

		$packer->addFile('RoboFile.php', $root . '/RoboFile.php');
		$packer->addFile('dumbdump', $root . '/dumbdump');

		$packer
			->executable('stub.php')
			->run();

	}
}