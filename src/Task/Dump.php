<?php
/**
 * Author: Radek
 * Date: 27/03/15 11:49
 */

namespace DumbDump\Task;

use Robo\Common\ExecOneCommand;
use Robo\Common\TaskIO;
use Robo\Result;
use Robo\Task\BaseTask;
use DumbDump\Task\Base;

class Dump extends BaseTask
{
	use TaskIO, ExecOneCommand;

	protected $outDir;
	protected $oneFile;

	protected $user, $pass, $host;

	/** @var Base[] */
	protected $tasks = [];

	function __construct($outDir, $sourceHost, $sourceUser, $sourcePass)
	{
		$this->outDir = $outDir;
		$this->user   = $sourceUser;
		$this->pass   = $sourcePass;
		$this->host   = $sourceHost;
	}

	public function data($dbName, $includeTables = [], $excludeTables = [])
	{
		$outFile = $this->outDir . '/data_' . $dbName . '.sql';

		$task = new DumpData($outFile, $this->host, $this->user, $this->pass);
		$task->database($dbName)
			->includeTables($includeTables)
			->excludeTables($excludeTables);

		$this->tasks[] = $task;

		return $task;
	}

	public function schema($dbName)
	{
		$outFile = $this->outDir . '/schema_' . $dbName . '.sql';

		$task = new DumpSchema($outFile, $this->host, $this->user, $this->pass);
		$task->database($dbName);

		$this->tasks[] = $task;

		return $task;
	}

	public function outputOneFile($file)
	{
		$this->oneFile = $file;
	}

	public function run()
	{
		if (!file_exists($this->outDir))
		{
			mkdir($this->outDir, 0770);
		}

		$this->printTaskInfo(sprintf('Running %d dump tasks', count($this->tasks)));

		$dumpFiles = [];

		foreach ($this->tasks as $task)
		{
			$res = $task->run();

			if (!$res->wasSuccessful())
			{
				return Result::error($this, 'Unable to dump');
			}

			$dumpFiles[] = $task->getOutFile();
		}

		if ($this->oneFile)
		{
			$this->printTaskInfo('Dumping files to one output file', $this);
			$cat = sprintf('cat %s > %s', implode(' ', $dumpFiles), $this->oneFile);
			$res = $this->executeCommand($cat);

			if ($res->wasSuccessful())
			{
				$this->printTaskInfo(sprintf('Data dumped into one file <info>%s</info>', $this->oneFile), $this);

				$rm = sprintf('rm %s', implode(' ', $dumpFiles));
				$this->executeCommand($rm);
			}
		}
		else
		{
			$this->printTaskInfo(sprintf('Dumped to <info>%s</info> directory', $this->outDir), $this);
		}

		return Result::success($this, 'Success!');
	}

	/**
	 * @return mixed
	 */
	public function getOutDir()
	{
		return $this->outDir;
	}
}