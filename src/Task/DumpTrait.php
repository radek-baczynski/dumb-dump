<?php
/**
 * Author: Radek
 * Date: 27/03/15 10:56
 */

namespace DumbDump\Task;

trait DumpTrait
{
	protected function taskDumpData($outFile, $host, $user, $pass)
	{
		return new DumpData($outFile, $host, $user, $pass);
	}

	protected function taskDump($outDir, $host, $user, $pass)
	{
		return new Dump($outDir, $host, $user, $pass);
	}

	protected function taskDumpRestore($host, $user, $pass)
	{
		$task = new RestoreTask($host, $user, $pass);

		return $task;
	}
}