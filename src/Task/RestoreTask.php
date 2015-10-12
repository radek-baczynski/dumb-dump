<?php

namespace DumbDump\Task;

use Robo\Common\ExecOneCommand;
use Robo\Common\TaskIO;
use Robo\Task\Base\ParallelExec;
use Robo\Task\BaseTask;

/**
 * Created by PhpStorm.
 * User: Radek
 * Date: 04/12/14
 * Time: 13:41
 */
class RestoreTask extends BaseTask
{
	use ExecOneCommand, TaskIO;

	protected $destinationUser;
	protected $destinationPass;
	protected $destinationHost;

	protected $file;

	function __construct($destinationHost, $destinationUser, $destinationPass)
	{
		$this->destinationHost = $destinationHost;
		$this->destinationUser = $destinationUser;
		$this->destinationPass = $destinationPass;
	}


	public function restore($file)
	{
		$this->file = $file;

		return $this;
	}

	/**
	 * @return \Robo\Result
	 */
	function run()
	{
		$this->option('-h', $this->destinationHost);

		if ($this->destinationPass)
		{
			$this->arg('--password="'.$this->destinationPass.'"');
		}

		$this->option('-u', $this->destinationUser);

		$this->printTaskInfo(sprintf('Restoring from file %s', $this->file), $this);

		$cmd = sprintf('gunzip -c %s | mysql %s', $this->file, $this->arguments);
		$res = $this->executeCommand($cmd);

		return $res;
	}
}
