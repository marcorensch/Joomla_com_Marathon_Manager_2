<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_marathonmanager
 *
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\MarathonManager\Administrator\Table;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\Database\DatabaseDriver;

class MapTable extends Table
{
	public function __construct(DatabaseDriver $db)
	{
		$this->typeAlias = 'com_marathonmanager.map';
		parent::__construct('#__com_marathonmanager_maps', 'id', $db);
	}

	public function generateAlias(): string
	{
		$this->alias = ApplicationHelper::stringURLSafe($this->title);

		return $this->alias;
	}

	/**
	 * @throws \Exception
	 */
	public function check()
	{
		try {
			parent::check();
		}catch (\Exception $e) {
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			return false;
		}

		// Check publish down date is not earlier than publish up
		if ($this->published_up && $this->published_down && $this->published_down < $this->published_up)
		{
			throw new \Exception(Text::_('JGLOBAL_FINISH_PUBLISH_AFTER_START'));
			return false;
		}

		// Set publish_up, publish_down to null if not set
		$this->publish_up = (!$this->publish_up) ? null : $this->publish_up;
		$this->publish_down = (!$this->publish_down) ? null : $this->publish_down;

		return true;
	}

	public function store($updateNulls = true): bool
	{
		return parent::store($updateNulls);
	}
}