<?php

namespace DumbDump\Task;

use Robo\Contract\CommandInterface;
use Robo\Task\Base\loadTasks;

class DumpData extends Base implements CommandInterface
{
	protected $includeTables = [];
	protected $excludeTables = [];

	protected $type = 'data';

	public function includeTables($tables = [])
	{
		$this->includeTables = array_merge($this->includeTables, $tables);

		return $this;
	}

	public function excludeTables($tables = [])
	{
		$this->excludeTables = array_merge($this->excludeTables, $tables);

		return $this;
	}

	public function getCommand()
	{
		$this->fillAuthArguments();
		$this->fillTablesArguments();

		if ($this->gzip)
		{
			return sprintf('mysqldump %s | gzip > %s', $this->arguments, $this->getOutFile());
		}
		else
		{
			return sprintf('mysqldump %s > %s', $this->arguments, $this->getOutFile());
		}
	}

	private function fillTablesArguments()
	{
		if ($this->excludeTables)
		{
			$this->option('--no-create-info');
			$this->option('--databases', $this->database);

			foreach ($this->excludeTables as $table)
			{
				$this->option('--ignore-table', $this->database . '.' . $table);
			}
		}
		elseif ($this->includeTables)
		{
			$this->arg($this->database);

			foreach ($this->includeTables as $table)
			{
				$this->arg($table);
			}
		}
		else
		{
			$this->option('--no-create-info');
			$this->option('--databases', $this->database);
		}
	}
}