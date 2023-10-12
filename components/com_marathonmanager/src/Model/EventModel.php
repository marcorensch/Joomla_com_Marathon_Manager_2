<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_marathonmanager
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 *              All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\MarathonManager\Site\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

/**
 * (DB) Event Model
 *
 * @since  1.0.0
 */

class EventModel extends BaseDatabaseModel
{
	protected $_item = null;
    protected $params = null;

	public function getItem($pk = null): object|bool
	{
		$app = Factory::getApplication();
		$pk = $app->input->getInt('id');

		if($this->_item === null)
		{
			$this->_item = array();
		}

		if(!isset($this->_item[$pk]))
		{
			try{
				$db = $this->getDatabase();
				$query = $db->getQuery(true);

				$query->select('*')
					->from($db->quoteName('#__com_marathonmanager_events', 'a'))
					->where($db->quoteName('a.id') . ' = ' . $db->quote($pk));

				$db->setQuery($query);
				$data = $db->loadObject();

				if(empty($data))
				{
					throw new \Exception(Text::_('COM_MARATHONMANAGER_EVENT_NOT_FOUND'), 404);
				}

				$this->_item[$pk] = $data;
			}
			catch(\Exception $e)
			{
				$this->setError($e->getMessage());
				$this->_item[$pk] = false;
			}
		}

        //Check if current user is already registered for this event
        $user = Factory::getApplication()->getIdentity();
        $this->_item[$pk]->alreadyRegistered = $this->isRegistered($pk, $user->id);

		return $this->_item[$pk];
	}

    private function isRegistered($eventId, $userId): bool
    {
        if(empty($userId)) return false;
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        $query->select('a.id')
            ->from($db->quoteName('#__com_marathonmanager_registrations', 'a'))
            ->where($db->quoteName('a.event_id') . ' = ' . $db->quote($eventId))
            ->where($db->quoteName('a.created_by') . ' = ' . $db->quote($userId));
        $query->setLimit(1);
        $db->setQuery($query);

        return !empty($db->loadResult());
    }
}