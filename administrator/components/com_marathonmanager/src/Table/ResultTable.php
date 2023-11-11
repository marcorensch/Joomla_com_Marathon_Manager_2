<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_marathonmanager
 *
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\MarathonManager\Administrator\Table;

\defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\Database\DatabaseDriver;

class ResultTable extends Table
{
	public function __construct(DatabaseDriver $db)
	{
		$this->typeAlias = 'com_marathonmanager.result';
		parent::__construct('#__com_marathonmanager_results', 'id', $db);
	}

	public function generateAlias(): string
	{
		$this->alias = ApplicationHelper::stringURLSafe($this->team_name);

		return $this->alias;
	}

	/**
	 * @throws \Exception
	 */
	public function check()
	{
		try {
			return parent::check();
		}catch (\Exception $e) {
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			return false;
		}
	}

	public function store($updateNulls = true): bool
	{
		return parent::store($updateNulls);
	}
}