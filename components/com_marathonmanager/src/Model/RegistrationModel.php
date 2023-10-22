<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_marathonmanager
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 *              All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\MarathonManager\Site\Model;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\FormModel;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Router\Route;

class RegistrationModel extends FormModel
{

    public $typeAlias = 'com_marathonmanager.registration';

    /**
     * Method for getting a form.
     *
     * @param array $data Data for the form.
     * @param boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return  \Joomla\CMS\Form\Form
     *
     * @throws \Exception
     * @since   4.0.0
     *
     */
    public function getForm($data = [], $loadData = true): Form
    {
        try {
            $form = $this->loadForm('com_marathonmanager.registration', 'registration', array('control' => 'jform', 'load_data' => $loadData));
        } catch (Exception $e) {
            error_log('Exception: ' . $e->getMessage());
            throw new Exception($e->getMessage(), 500);
        }

        return $form;
    }

    public function getEvent(): object
    {
        $app = Factory::getApplication();
        $eventId = $app->input->getInt('event_id');
        if (empty($eventId)) {
            throw new Exception('Event not defined', 404);
        }
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        $query->select('a.*')
            ->from($db->quoteName('#__com_marathonmanager_events', 'a'))
            ->where($db->quoteName('a.id') . ' = ' . $db->quote($eventId));
        $db->setQuery($query);
        $event = $db->loadObject();

        if (empty($event)) {
            throw new Exception('Event not found', 404);
        }

        //Check if current user is already registered for this event
        $user = Factory::getApplication()->getIdentity();
        $event->alreadyRegistered = $this->isRegistered($event->id, $user->id);

        return $event;
    }

    private function getParcours($ids): array
    {
        $parcours = array();




        return $parcours;
    }

    /**
     * @throws Exception
     */
    protected
    function loadFormData()
    {
        $currentEventId = Factory::getApplication()->input->getInt('id');
        $currentUserId = Factory::getApplication()->getIdentity()->id;
        // Check the session for previously entered form data.
        $data = Factory::getApplication()->getUserState(
            'com_marathonmanager.registration',    // a unique name to identify the data in the session
            array("event_id" => $currentEventId)    // prefill data if no data found in session
        );

        // Overwrite EventId from URL
        $data['event_id'] = $currentEventId;
        $data['created_by'] = $currentUserId;

        return $data;
    }

    public
    function validate($form, $data, $group = null): array
    {
        error_log('RegistrationModel::validate() called');
        error_log('Data: ' . print_r($data, true));
        return parent::validate($form, $data, $group);
    }

    public
    function store($data): bool
    {
        error_log('RegistrationModel::store() called');
        error_log('Data: ' . print_r($data, true));
        return true;
    }

    private
    function isRegistered($eventId, $userId): bool
    {
        if (empty($userId)) return false;
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

    public
    function getReturnPage(): string
    {
        $currentEventId = Factory::getApplication()->input->getInt('event_id');
        $this->setState('return_page', Route::_('index.php?option=com_marathonmanager&view=event&id=' . $currentEventId));
        $returnPage = base64_encode($this->getState('return_page', ''));
        return $returnPage;
    }
}