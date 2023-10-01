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

class RegistrationTable extends Table
{
	public function __construct(DatabaseDriver $db)
	{
		$this->typeAlias = 'com_marathonmanager.registration';
		parent::__construct('#__com_marathonmanager_registrations', 'id', $db);
	}

	public function generateAlias(): string
	{
		$this->alias = ApplicationHelper::stringURLSafe($this->team_name);

		return $this->alias;
	}

    public function generateReference($data): string
    {
        if(empty($data['event_id'])) {
            $data['event_id'] = 0;
        }
        if(empty($data['created_by'])) {
            $data['created_by'] = Factory::getApplication()->getIdentity()->id;
        }
        $params = ComponentHelper::getParams('com_marathonmanager');
        $prefix = $params->get('registration_reference_prefix', 'REG');
        $eventYearDay = Date::getInstance($data['event_date'])->format('yd');
        $this->reference = strtoupper(ApplicationHelper::stringURLSafe($prefix . '-' . $eventYearDay . $data['event_id'] . $data['created_by']));

        return $this->reference;
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