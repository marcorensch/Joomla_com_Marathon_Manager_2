<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_marathonmanager
 *
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\MarathonManager\Administrator\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportModel extends \Joomla\CMS\MVC\Model\AdminModel
{

    public $typeAlias = 'com_marathonmanager.export';

    private $countries;

    private $numOfParticipants = 5;

    private $app;

    /**
     * Method for getting a form.
     *
     * @param array $data Data for the form.
     * @param boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return  mixed  A \JForm object on success, false on failure
     *
     * @throws \Exception
     * @since   4.0.0
     *
     */
    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm($this->typeAlias, 'export', ['control' => 'jform', 'load_data' => $loadData]);

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    protected function getCountries(): array
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $query->select($db->quoteName(['id', 'title']))
            ->from($db->quoteName('#__com_marathonmanager_countries'));
        $db->setQuery($query);
        return $db->loadAssocList('id');
    }

    protected function loadFormData()
    {
        $app = Factory::getApplication();

        $data = $app->getUserState('com_marathonmanager.edit.export.data', []);

        $this->preprocessData($this->typeAlias, $data);

        return $data;
    }

    /**
     * @throws Exception
     * @since 1.0.0
     */
    public function export($settings):bool
    {
        // Required gets
        $this->countries = $this->getCountries();
        $this->app = Factory::getApplication();

        $fileName = 'export.xlsx';
        if(!empty($settings['filename'])){
            $fileName = $settings['filename'] . '.xlsx';
        }

        switch ($settings['export_type']) {
            case 'startlist':
            default:
                $arrayData = $this->exportStartList($settings);
                break;
        }

        if (empty($arrayData)) {
            $app = Factory::getApplication();
            $app->enqueueMessage(Text::_('COM_MARATHONMANAGER_EXPORT_NO_DATA'), 'warning');
            return false;
        }

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->fromArray($arrayData);
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');

        $writer->save('php://output');
        Factory::getApplication()->close();
        return true;
    }

    private function exportStartList($exportConfiguration): array
    {
        $arrayData = $this->getRegistrations($exportConfiguration);
        return $this->buildExportLayout($exportConfiguration, $arrayData);
    }

    private function getRegistrations($configuration): array
    {
        // We load ID as first column and replace it later on with the real start number
        $columns = array('r.id', 'r.created', 'r.team_name', 'r.event_id', 'r.id', 'c.course_id', 'g.group_id', 'c.title', 'g.title', 'l.title', 'at.title', 'ad.date', 'r.contact_first_name', 'r.contact_last_name', 'r.contact_email', 'r.contact_phone', 'r.participants', 'r.payment_status');
        $alias = array('registration_id', 'created', 'team_name', 'event_id', 'id', 'course_id', 'group_id', 'parcours_title', 'group_title', 'language', 'arrival_type', 'arrival_date', 'contact_first_name', 'contact_last_name', 'contact_email', 'contact_phone', 'participants', 'payment_status');
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);

        $query->select($db->quoteName($columns, $alias));
        $query->from($db->quoteName('#__com_marathonmanager_registrations', 'r'));
        $query->join('LEFT', $db->quoteName('#__com_marathonmanager_courses', 'c') . ' ON ' . $db->quoteName('r.course_id') . ' = ' . $db->quoteName('c.id'));
        $query->join('LEFT', $db->quoteName('#__com_marathonmanager_groups', 'g') . ' ON ' . $db->quoteName('r.group_id') . ' = ' . $db->quoteName('g.id'));
        $query->join('LEFT', $db->quoteName('#__com_marathonmanager_languages', 'l') . ' ON ' . $db->quoteName('r.team_language_id') . ' = ' . $db->quoteName('l.id'));
        $query->join('LEFT', $db->quoteName('#__com_marathonmanager_arrival_options', 'at') . ' ON ' . $db->quoteName('r.arrival_option_id') . ' = ' . $db->quoteName('at.id'));
        $query->join('LEFT', $db->quoteName('#__com_marathonmanager_arrival_dates', 'ad') . ' ON ' . $db->quoteName('r.arrival_date_id') . ' = ' . $db->quoteName('ad.id'));

        $query->where('r.published = 1');

        if ($configuration['only_paid']) {
            $query->where('r.payment_status = 1');
        }
        $db->setQuery($query);
        return $db->loadAssocList();
    }


    /**
     * @description Build the Export Layout
     * @param $exportConfiguration
     * @param $rows
     * @return array
     * @throws \Exception
     * @since 1.0.0
     */
    private function buildExportLayout($exportConfiguration, $rows): array
    {
        $exportArray = array();
        $registrations = $this->prepareRows($exportConfiguration, $rows);

        $labelsCreated = false;
        $labels = array();
        foreach ($registrations as $registration) {

            $unFilteredParticipantsArray = json_decode($registration['participants'], true);
            $unFilteredParticipantsArray = $this->getCountryNames($unFilteredParticipantsArray);
            $participantEmailsArray = $this->getParticipantEmails($unFilteredParticipantsArray);

            $registrationData = array();
            $registrationData['id'] = $registration['registration_id'];
            $registrationData['event_id'] = $registration['event_id'];
            $registrationData['registration_date'] = HTMLHelper::date($registration['created'], Text::_('DATE_FORMAT_LC5'));
            $registrationData['team_name'] = $registration['team_name'];
            if (array_key_exists('start_number', $registration)) {
                $registrationData['start_number'] = $registration['start_number'];
            }
            $registrationData['category'] = $registration['course_id'] . "." . $registration['group_id'];
            $registrationData['parcours'] = $registration['parcours_title'] . " " . $registration['group_title'];
            $registrationData['language'] = $registration['language'];
            $registrationData['contact'] = $registration['contact_first_name'] . " " . $registration['contact_last_name'];
            $registrationData['contact_email'] = $this->combineEmails($registration['contact_email'], $participantEmailsArray);
            $registrationData['contact_phone'] = $registration['contact_phone'];
            $registrationData['arrival_type'] = $registration['arrival_type'];
            $registrationData['arrival_date'] = $registration['arrival_date'] ? HTMLHelper::date($registration['arrival_date'], Text::_('DATE_FORMAT_LC5')) : '';
            $registrationData['payment_status'] = $registration['payment_status'];

            $additionalDataArray = array();
            $additionalDataArray['si_card_1'] = '';
            $additionalDataArray['si_card_2'] = '';
            $additionalDataArray['runner_country_1'] = $unFilteredParticipantsArray['participants0']['country'] ?? '';
            $additionalDataArray['runner_country_2'] = $unFilteredParticipantsArray['participants1']['country'] ?? '';

            if (!$labelsCreated) {
                // While we are in the first loop, we add the participants labels
                $translatedLabels = $this->translateLabels(array_keys($registrationData));
                $labels = $this->addParticipantsLabels($translatedLabels);
                $labels = $this->addAdditionalLabels($labels);
                $labelsCreated = true;
            }

            // Add Participants
            // not all participant data goes into the export here, but we need to strip out emails before
            $participantsArray = $this->prepareParticipantsData($unFilteredParticipantsArray);
            $rowData = array_merge($registrationData, $this->buildParticipants($registration['registration_id'], $participantsArray), $additionalDataArray);

            // Add Row to Export Array
            $exportArray[] = $rowData;
        }

        // Add column titles to export array
        array_unshift($exportArray, $labels);
        return $exportArray;
    }

    private function translateLabels($labels): array
    {
        $translatedLabels = array();
        foreach ($labels as $label) {
            $translatedLabels[] = Text::_("COM_MARATHONMANAGER_EXPORT_" . strtoupper($label));
        }
        return $translatedLabels;
    }

    private function addParticipantsLabels($labels): array
    {
        // Add Participant Labels
        for ($r = 1; $r <= $this->numOfParticipants; $r++) {
            $labels[] = Text::sprintf('COM_MARATHONMANAGER_EXPORT_RUNNER_N_FIRSTNAME', $r);
            $labels[] = Text::_('COM_MARATHONMANAGER_EXPORT_RUNNER_LASTNAME');
//            $labels[] = Text::_('COM_MARATHONMANAGER_EXPORT_RUNNER_GENDER');
            $labels[] = Text::_('COM_MARATHONMANAGER_EXPORT_RUNNER_AGE');
            $labels[] = Text::_('COM_MARATHONMANAGER_EXPORT_RUNNER_TR');
            $labels[] = Text::_('COM_MARATHONMANAGER_EXPORT_RUNNER_RESIDENCE');
//            $labels[] = Text::_('COM_MARATHONMANAGER_EXPORT_RUNNER_COUNTRY');
//            $labels[] = Text::_('COM_MARATHONMANAGER_EXPORT_RUNNER_EMAIL');
        }

        return $labels;
    }

    /**
     * @throws \Exception
     */
    private function buildParticipants($regId, $participants): array
    {
        if (empty($participants)) {
            return array();
        }
        foreach ($participants as &$participant) {
            foreach ($participant as $key => $value) {
                // Set Gender Text in Export
                if ($key === 'gender') {
                    switch ($value) {
                        case 'm':
                            $participant[$key] = Text::_("COM_MARATHONMANAGER_FIELD_GENDER_OPT_MEN");
                            break;
                        case "w":
                            $participant[$key] = Text::_("COM_MARATHONMANAGER_FIELD_GENDER_OPT_WOMEN");
                            break;
                        case "d":
                            $participant[$key] = Text::_("COM_MARATHONMANAGER_FIELD_GENDER_OPT_DIVERS");
                            break;
                        default:
                            // Do nothing
                    }
                }
                // Set Public Transport Price Reduction Text in Export
                if ($key === 'public_transport_reduction') {
                    switch ($value) {
                        case 'no':
                            $participant[$key] = Text::_("COM_MARATHONMANAGER_FIELD_PTRED_OPT_NO");
                            break;
                        case "ga":
                            $participant[$key] = Text::_("COM_MARATHONMANAGER_FIELD_PTRED_OPT_GA");
                            break;
                        case "ht":
                            $participant[$key] = Text::_("COM_MARATHONMANAGER_FIELD_PTRED_OPT_HT");
                            break;
                        default:
                            // Do nothing
                    }
                }

                if ($key === 'country') {
                    if (!empty($value)) {
                        if (array_key_exists($value, $this->countries)) {
                            $participant[$key] = $this->countries[$value]['title'];
                        } else {
                            $participant[$key] = Text::_("COM_MARATHONMANAGER_EXPORT_COUNTRY_OTHER");
                            if($value !== 'other'){
                                Factory::getApplication()->enqueueMessage(Text::sprintf('COM_MARATHONMANAGER_EXPORT_COUNTRY_NOT_FOUND', $regId, $participant['first_name'], $participant['last_name']), 'warning');
                                error_log('Registration ID ' . $regId . ' Runner '.$participant['first_name'].' '.$participant['last_name'].': Country not found while export: ' . $value);
                            }
                        }
                    } else {
                        $participant[$key] = Text::_("COM_MARATHONMANAGER_EXPORT_COUNTRY_UNDEFINED");
                        Factory::getApplication()->enqueueMessage(Text::sprintf('COM_MARATHONMANAGER_EXPORT_COUNTRY_NOT_SET', $regId, $participant['first_name'], $participant['last_name']), 'warning');
                        error_log('Registration ID ' . $regId . ' Runner '.$participant['first_name'].' '.$participant['last_name'].': Country not set while export');
                    }
                }
            }
        }

        $container = array();
        // Flat the Array (https://stackoverflow.com/a/1319903)
        // participants is an array of arrays
        array_walk_recursive($participants, function ($a) use (&$container) {
            $container[] = $a;
        });
        return $container;
    }

    private function prepareRows($exportConfiguration, $rows): array
    {
        // Create Arrays for each Course
        $courses = array();
        foreach ($rows as $row) {
            if (!isset($courses[$row['course_id']])) {
                $courses[$row['course_id']] = array();
            }
        }

        // Move Registrations to the effective Course Array
        foreach ($rows as $row) {
            $courses[$row['course_id']][] = $row;
        }

        // Create Start Numbers for each Course
        if ($exportConfiguration['create_team_numbers']) {
            foreach ($courses as &$course) {
                $startNumber = 1;
                foreach ($course as &$registration) {
                    $spacer = $startNumber < 10 ? "0" : "";
                    $registration['start_number'] = $registration['course_id'] . $spacer . $startNumber;
                    $startNumber++;
                }
                unset($registration); // break the reference with the last element (https://www.php.net/manual/en/control-structures.foreach.php)
            }
            unset($course); // break the reference with the last element (https://www.php.net/manual/en/control-structures.foreach.php)
        }

        // Flatten the Array
        $registrations = array();
        foreach ($courses as $course) {
            foreach ($course as $registration) {
                $registrations[] = $registration;
            }
        }

        return $registrations;
    }

    private function prepareParticipantsData(array $participants) : array
    {
        $keys = array('first_name', 'last_name','age','public_transport_reduction','residence');

        // Remove unwanted data from participants
        foreach ($participants as &$participant) {
            $participant = array_intersect_key($participant, array_flip($keys));
            // Make sure the array has the correct sorting
            $participant = array_replace(array_flip($keys), $participant);
        }

        // Make sure the array has the correct length
        $participants = array_pad($participants, $this->numOfParticipants, array('first_name' => '', 'last_name' => '', 'age' => '', 'public_transport_reduction' => '', 'residence' => ''));

        return $participants;

    }

    private function getParticipantEmails(array $unFilteredParticipantsArray): array
    {
        $participantEmails = array();
        foreach ($unFilteredParticipantsArray as $participant) {
            if (!empty($participant['email'])) {
                $participantEmails[] = $participant['email'];
            }
        }
        return $participantEmails;
    }

    private function combineEmails(string $contact_email, array $participantEmailsArray):string
    {
        // Remove duplicates
        $participantEmailsArray = array_unique($participantEmailsArray);
        // Remove empty values
        $participantEmailsArray = array_filter($participantEmailsArray);

        // Generates Semicolon separated list of emails
        if (!empty($participantEmailsArray)) {
            $contact_email .= "; " . implode("; ", $participantEmailsArray);
        }
        return $contact_email;
    }

    private function addAdditionalLabels(array $labels):array
    {
        $labels[] = Text::_('COM_MARATHONMANAGER_EXPORT_SI_CARD_1');
        $labels[] = Text::_('COM_MARATHONMANAGER_EXPORT_SI_CARD_2');
        $labels[] = Text::_('COM_MARATHONMANAGER_EXPORT_RUNNER_COUNTRY_1');
        $labels[] = Text::_('COM_MARATHONMANAGER_EXPORT_RUNNER_COUNTRY_2');
        return $labels;
    }

    private function getCountryNames(array $unFilteredParticipantsArray): array
    {
        if (is_array($unFilteredParticipantsArray)) {
            foreach ($unFilteredParticipantsArray as $key => $participant) {
                if (array_key_exists('country', $participant)) {
                    if (array_key_exists($participant['country'], $this->countries)) {
                        $unFilteredParticipantsArray[$key]['country'] = $this->countries[$participant['country']]['title'];
                    } else {
                        $unFilteredParticipantsArray[$key]['country'] = Text::_("COM_MARATHONMANAGER_EXPORT_COUNTRY_OTHER");
                    }
                }
            }
        }
        return $unFilteredParticipantsArray;
    }
}