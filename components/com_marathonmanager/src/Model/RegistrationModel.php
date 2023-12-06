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
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\FormModel;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Router\Route;
use Joomla\Registry\Registry;

use NXD\Component\MarathonManager\Site\Helper\RegistrationHelper;
use NXD\Component\MarathonManager\Administrator\Model\NewsletterModel;

class RegistrationModel extends FormModel
{

    public $typeAlias = 'com_marathonmanager.registration';
    private $eventMapOptionId = null;

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

    /**
     * @throws Exception
     */
    public function getEvent($eventId = null): object
    {
        $app = Factory::getApplication();
        if(empty($eventId)) {
            $eventId = $app->input->getInt('event_id');
        }
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

        // Set the map option id for this event
        $this->eventMapOptionId = $event->map_option_id;

        //Check if current user is already registered for this event
        $user = Factory::getApplication()->getIdentity();
        $event->alreadyRegistered = $this->isRegistered($event->id, $user->id);

        return $event;
    }

    public function getMapOption(): object|null
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        $query->select('a.*')
            ->from($db->quoteName('#__com_marathonmanager_maps', 'a'))
            ->where($db->quoteName('a.id') . ' = ' . $db->quote($this->eventMapOptionId));
        $db->setQuery($query);
        $mapoption = $db->loadObject();

        if (empty($mapoption)) {
            $app = Factory::getApplication();
            $app->enqueueMessage('Map Option not found', 'error');
            return null;
        }

        return $mapoption;

    }

    /**
     * @throws Exception
     */
    protected
    function loadFormData()
    {
        $app = Factory::getApplication();
        $currentEventId = Factory::getApplication()->input->getInt('id');
        $currentUserId = Factory::getApplication()->getIdentity()->id;
        // Check the session for previously entered form data.
        $data = Factory::getApplication()->getUserState(
            'com_marathonmanager.registration.data',    // a unique name to identify the data in the session
            array("event_id" => $currentEventId)    // prefill data if no data found in session
        );

        // Overwrite EventId from URL
        $data['event_id'] = $currentEventId;
        $data['created_by'] = $currentUserId;

        if (empty($data)) {
            // Prime some default values.
            if ($this->getState('registration.id') == 0) {
                $data->set('catid', $app->input->get('catid', $app->getUserState('com_marathonmanager.registrations.filter.category_id'), 'int'));
            }
        }

        return $data;
    }

    protected function prepareTable($table): void
    {
        $table->generateAlias();
    }

    /**
     * @throws Exception
     */
    public function getRegistration(): object
    {
        $userId = Factory::getApplication()->getIdentity()->id;
        $eventId = Factory::getApplication()->input->getInt('event_id');

        if (empty($userId) || empty($eventId)) {
            throw new Exception('Forbidden', 401);
        }
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        $query->select(array('a.*', 'ad.date as arrival_date'))
            ->from($db->quoteName('#__com_marathonmanager_registrations', 'a'))
            ->join('LEFT', $db->quoteName('#__com_marathonmanager_arrival_dates', 'ad') . ' ON (' . $db->quoteName('a.arrival_date_id') . ' = ' . $db->quoteName('ad.id') . ')')
            ->where($db->quoteName('a.created_by') . ' = ' . $db->quote($userId))
            ->where($db->quoteName('a.event_id') . ' = ' . $db->quote($eventId))
            ->where($db->quoteName('a.published') . ' = 1');
        $db->setQuery($query);
        $registration = $db->loadObject();

        if (empty($registration)) {
            throw new Exception('Registration not found', 404);
        }

        $registration->participants = json_decode($registration->participants);
        $registration->course = RegistrationHelper::getCourse($registration->course_id);
        $registration->group = RegistrationHelper::getGroup($registration->group_id);

        $registration->paymentInformation = RegistrationHelper::getPaymentInformation($registration->event_id, $registration->created);

        return $registration;
    }

    /**
     * @param $form
     * @param $data
     * @param $group
     * @return array|boolean
     */
    public
    function validate($form, $data, $group = null): bool|array
    {
        // Set user state for form data
        Factory::getApplication()->setUserState( 'com_marathonmanager.registration.data', $data );
        return parent::validate($form, $data, $group);
    }

    /**
     * @throws Exception
     */
    public
    function save($data): bool
    {
        $app   = Factory::getApplication();
        $user  = $app->getIdentity();
        $table = $this->getTable();



        // new element tasks
        if (!isset($data['id']) || (int) $data['id'] === 0)
        {
            $data['created_by'] = $user->id;
            $data['reference'] = $table->generateReference($data);
        }

        $data['registration_fee'] = RegistrationHelper::calculateRegistrationFee($data['event_id'], $data['maps_count']);
        $data['modified_by'] = $user->id;
        $data['access'] = 1;
        $data['alias'] = $this->generateAlias($data);

        // Participants
        $registry = new Registry;
        $registry->loadArray($data['participants']);
        $data['participants'] = (string) $registry;

        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        $columns = array(
            'team_name',
            'alias',
            'event_id',
            'course_id',
            'group_id',
            'arrival_option_id',
            'arrival_date_id',
            'contact_first_name',
            'contact_last_name',
            'contact_email',
            'newsletter_enlist',
            'contact_phone',
            'maps_count',
            'team_language_id',
            'participants',
            'privacy_policy',
            'reference',
            'payment_status',
            'created_by',
            'modified_by',
            'access',
            'insurance_notice',
            'registration_fee'
        );
        $values = array(
            $db->quote($data['team_name']),
            $db->quote($data['alias']),
            $db->quote($data['event_id']),
            $db->quote($data['course_id']),
            $db->quote($data['group_id']),
            $db->quote($data['arrival_option_id']),
            $db->quote($data['arrival_date_id']),
            $db->quote($data['contact_first_name']),
            $db->quote($data['contact_last_name']),
            $db->quote($data['contact_email']),
            $db->quote($data['newsletter_enlist']),
            $db->quote($data['contact_phone']),
            $db->quote($data['maps_count']),
            $db->quote($data['team_language_id']),
            $db->quote($data['participants']),
            $db->quote($data['privacy_policy']),
            $db->quote($data['reference']),
            $db->quote(0), // payment_status
            $db->quote($data['created_by']),
            $db->quote($data['modified_by']),
            $db->quote($data['access']),
            $db->quote($data['insurance_notice']),
            $db->quote($data['registration_fee'])
        );
        $query->insert($db->quoteName('#__com_marathonmanager_registrations'))
            ->columns($db->quoteName($columns))
            ->values(implode(',', $values));
        $db->setQuery($query);
        $status = $db->execute();

        if($status) {
            $this->handleNewsletterSubscriptions($data);
            // Clear the user state
            Factory::getApplication()->setUserState('com_marathonmanager.registration.data', null);
        }

        return $status;
    }

    private function handleNewsletterSubscriptions($data): void
    {
        $app = Factory::getApplication();
        $params = $app->getParams();

        $newsletterModel = new NewsletterModel();
        $newsletterModel->saveUser($data['contact_email'], $data['contact_first_name'], $data['contact_last_name']);

        // Enlist for newsletter if requested
        if($data['newsletter_enlist'] && $generalNewsletterListId = $params->get('newsletter_list_id', null)) {
            if($newsletterModel->subscribe($generalNewsletterListId)){
                $app->enqueueMessage(Text::_('COM_MARATHONMANAGER_SUCCESS_SUBSCRIBE_NEWSLETTER'), 'success');
            }else{
                $app->enqueueMessage(Text::sprintf('COM_MARATHONMANAGER_ERROR_SUBSCRIBE_NEWSLETTER', $newsletterModel->getUser()->email), 'warning');
            }
        }

        // Enlist for last info newsletter
        $event = $this->getEvent($data['event_id']);
        if($event && $event->lastinfos_newsletter_list_id)
        {
            if(!$newsletterModel->subscribe($event->lastinfos_newsletter_list_id)){
                error_log('Could not subscribe user to last info newsletter');
                error_log(print_r($newsletterModel->getUser(), true));
                error_log('Last info newsletter id: ' . $event->lastinfos_newsletter_list_id);
                $app->enqueueMessage(Text::sprintf('COM_MARATHONMANAGER_ERROR_SUBSCRIBE_LASTINFO_NEWSLETTER', $newsletterModel->getUser()->email), 'warning');
            }
        }

        // Last Info Newsletter for Runners?
        if($app->getParams()->get('enlist_runners_for_lastinfo_newsletter', 0)){
            $participants = json_decode($data['participants']);
            foreach ($participants as $participant) {
                if($participant->email){
                    $newsletterModel = new NewsletterModel();
                    $newsletterModel->saveUser($participant->email, $participant->first_name, $participant->last_name, $data['created_by']);
                    if($event && $event->lastinfos_newsletter_list_id)
                    {
                        if(!$newsletterModel->subscribe($event->lastinfos_newsletter_list_id)){
                            error_log('Could not subscribe user to last info newsletter');
                            error_log(print_r($newsletterModel->getUser(), true));
                            error_log('Last info newsletter id: ' . $event->lastinfos_newsletter_list_id);
                            $app->enqueueMessage(Text::sprintf('COM_MARATHONMANAGER_ERROR_SUBSCRIBE_LASTINFO_NEWSLETTER', $newsletterModel->getUser()->email), 'warning');
                        }
                    }
                }
            }
        }
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
            ->where($db->quoteName('a.created_by') . ' = ' . $db->quote($userId))
            ->where($db->quoteName('a.published') . ' = 1');
        $query->setLimit(1);
        $db->setQuery($query);

        return !empty($db->loadResult());
    }

    public
    function getReturnPage(): string
    {
        $currentEventId = Factory::getApplication()->input->getInt('event_id');
        $this->setState('return_page', Route::_('index.php?option=com_marathonmanager&view=registration&layout=edit&event_id=' . $currentEventId));
        $returnPage = base64_encode($this->getState('return_page', ''));
        return $returnPage;
    }

    /**
     * @throws Exception
     */
    protected function generateAlias($data): string
    {
        $app = Factory::getApplication();
        // Automatic handling of alias for empty fields
        if (!isset($data['alias']) || $data['alias'] == null)
        {
            if ($app->get('unicodeslugs') == 1)
            {
                return OutputFilter::stringUrlUnicodeSlug($data['team_name']);
            }
            else
            {
                return OutputFilter::stringURLSafe($data['team_name']);
            }
        }

        return $data['alias'];
    }
}