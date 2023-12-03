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
use Joomla\CMS\Router\Route;
use NXD\Component\MarathonManager\Site\Model\RegistrationModel;

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

        if ($this->_item === null) {
            $this->_item = array();
        }

        if (!isset($this->_item[$pk])) {
            try {
                $db = $this->getDatabase();
                $query = $db->getQuery(true);

                $query->select('*')
                    ->from($db->quoteName('#__com_marathonmanager_events', 'a'))
                    ->where($db->quoteName('a.id') . ' = ' . $db->quote($pk));

                $db->setQuery($query);
                $data = $db->loadObject();

                if (empty($data)) {
                    throw new \Exception(Text::_('COM_MARATHONMANAGER_EVENT_NOT_FOUND'), 404);
                }

                // Decode the result files
                try {
                    $data->result_files = json_decode($data->result_files, true);
                } catch (\Exception $e) {
                    $data->result_files = array();
                }

                $this->_item[$pk] = $data;
            } catch (\Exception $e) {
                $this->setError($e->getMessage());
                $this->_item[$pk] = false;
            }
        }

        return $this->_item[$pk];
    }

    /**
     * @throws \Exception
     */
    public function getResults()
    {
        $resultsModel = new ResultsModel();
        $eventId = $this->getItem()->id;
        return $resultsModel->getResults($eventId);
    }

}