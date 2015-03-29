<?php

namespace DumbDump\Task;

use Robo\Task\Base\ParallelExec;

/**
 * Created by PhpStorm.
 * User: Radek
 * Date: 04/12/14
 * Time: 13:41
 */
class RestoreTask extends ParallelExec
{
	protected $destinationUser;
	protected $destinationPass;
	protected $destinationHost;

	protected $dumps = [];

	function __construct($destinationHost, $destinationUser, $destinationPass)
	{
		$this->destinationHost = $destinationHost;
		$this->destinationUser = $destinationUser;
		$this->destinationPass = $destinationPass;
	}


	public function restore(array $dumpFiles)
	{
		foreach ($dumpFiles as $file)
		{
			$cmd = 'gunzip -c %s | mysql -u%s --password="%s" -h%s';
			$cmd = sprintf($cmd, $file, $this->destinationUser, $this->destinationPass, $this->destinationHost);
			$this->process($cmd);
		}

		return $this->run();
	}

	public function restoreRemote(array $urls)
	{

	}
}
