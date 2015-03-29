<?php
/**
 * Author: Radek
 * Date: 27/03/15 10:59
 */

namespace DumbDump\Task;

use Robo\Task\BaseTask;

abstract class Base extends BaseTask
{
	use \Robo\Common\ExecOneCommand;

	protected $host;
	protected $pass;
	protected $user;

	protected $outFile;
	protected $gzip = true;

	protected $type;

	protected $database;

	function __construct($outFile, $sourceHost, $sourceUser, $sourcePass)
	{
		$this->outFile = $outFile;
		$this->source($sourceHost, $sourceUser, $sourcePass);
	}

	public function database($databaseName)
	{
		$this->database = $databaseName;

		return $this;
	}

	public function gzip($trueOrFalse)
	{
		$this->gzip = $trueOrFalse;

		return $this;
	}

	public function source($host, $user, $pass)
	{
		$this->host = $host;
		$this->pass = $pass;
		$this->user = $user;

		return $this;
	}

	public function run()
	{
		$this->printTaskInfo(sprintf("Dumping <info>%s</info> %s", $this->database, $this->type), $this);

		$res = $this->executeCommand($this->getCommand());

		return $res;
	}

	public function getOutFile()
	{
		return $this->outFile . ($this->gzip ? '.gz' : '');
	}

	protected function fillAuthArguments()
	{
		$this->option('-h', $this->host);

		if ($this->pass)
		{
			$this->arg('--password="'.$this->pass.'"');
		}

		$this->option('-u', $this->user);
	}

	abstract protected function getCommand();
}