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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\Database\DatabaseDriver;

class ImportTable extends Table
{
	public function __construct(DatabaseDriver $db)
	{
		$this->typeAlias = 'com_marathonmanager.import';
//		parent::__construct('#__com_marathonmanager_countries', 'id', $db);
	}
}