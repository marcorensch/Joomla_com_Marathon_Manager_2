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
use Joomla\Registry\Registry;

/**
 * (DB) Event Model
 *
 * @since  1.0.0
 */
class EventsModel extends BaseDatabaseModel
{
    protected $_items = null;

    public function getEvents()
    {
        $app = Factory::getApplication();

        if ($this->_items === null) {
            $this->_items = array();
        }

        $params = $this->getState('params');
        $sortingDir = $params->get('elements_dir','asc');

        try {
            $db = $this->getDatabase();
            $query = $db->getQuery(true);
            $nowDate = Factory::getDate()->toSql();

            $query->select(array('a.id','a.title','a.image','a.event_date','a.registration_start_date','a.registration_end_date','a.city'))
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

            // Ordering
            $query->order($db->quoteName('a.event_date') . ' ' . $sortingDir);
            $db->setQuery($query);
            $data = $db->loadObjectList();

            if(!empty($data->params)) {
                $registry = new Registry($data->params);

                $data->params = clone $this->getState('params');
                $data->params->merge($registry);
            }


            $this->_items = $data;
        } catch (\Exception $e) {
            $app->enqueueMessage($e->getMessage(), 'error');
            $this->_items = [];
        }

        return $this->_items;
    }

    protected function populateState()
    {
        $app = Factory::getApplication();
        $this->setState('params', $app->getParams());
    }
}