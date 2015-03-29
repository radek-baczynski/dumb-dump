<?php
/**
 * Author: Radek
 * Date: 27/03/15 14:57
 */

namespace DumbDump\Task;

use Robo\Contract\CommandInterface;

class DumpSchema extends Base implements CommandInterface
{
	protected $gzip = true;

	protected $database;

	protected $type = 'schema';

	public function getCommand()
	{
		$this->fillAuthArguments();

		$this->option('--no-data');
		$this->option('--add-drop-database');
		$this->option('--databases', $this->database);

		if ($this->gzip)
		{
			return sprintf('mysqldump %s | gzip > %s', $this->arguments, $this->getOutFile());
		}
		else
		{
			return sprintf('mysqldump %s > %s', $this->arguments, $this->getOutFile());
		}
	}
}