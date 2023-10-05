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
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Language\Text;

/**
 * (DB) Event Model
 *
 * @since  1.0.0
 */

class EventsModel extends BaseDatabaseModel
{
	protected $_items = null;

	public function getEvents(){
        error_log('getEvents');
		$app = Factory::getApplication();

		if($this->_items === null)
		{
			$this->_items = array();
		}

		try{
			$db = $this->getDatabase();
			$query = $db->getQuery(true);
            $nowDate = Factory::getDate()->toSql();

			$query->select('*')
				->from($db->quoteName('#__com_marathonmanager_events', 'a'))
				->where($db->quoteName('a.published') . ' = 1');

            // Publish up/down
            $query->where(
                [
                    '(' . $db->quoteName('a.publish_up') . ' IS NULL OR ' . $db->quoteName('a.publish_up') . ' <= :publishUp)',
                    '(' . $db->quoteName('a.publish_down') . ' IS NULL OR ' . $db->quoteName('a.publish_down') . ' >= :publishDown)',
                ]
            )
                ->bind(':publishUp', $nowDate)
                ->bind(':publishDown', $nowDate);

			$db->setQuery($query);
			$data = $db->loadObjectList();

			if(empty($data))
			{
				throw new \Exception(Text::_('COM_MARATHONMANAGER_EVENTS_NOT_FOUND'), 404);
			}

            $query->clear();

			$this->_items = $data;
		}
		catch(\Exception $e)
		{
			$this->setError($e->getMessage());
			$this->_items = false;
		}

		return $this->_items;
	}

	protected function populateState()
	{
		$app = Factory::getApplication();
		$this->setState('params', $app->getParams());
	}
}