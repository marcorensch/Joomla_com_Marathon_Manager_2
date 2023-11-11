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
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportModel extends \Joomla\CMS\MVC\Model\AdminModel
{

    public $typeAlias = 'com_marathonmanager.export';

    /**
     * Method for getting a form.
     *
     * @param array $data Data for the form.
     * @param boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return  Form
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

    protected function loadFormData()
    {
        $app = Factory::getApplication();

        $data = $app->getUserState('com_marathonmanager.edit.export.data', []);

        $this->preprocessData($this->typeAlias, $data);

        return $data;
    }

    /**
     * @throws Exception
     */
    public function export($settings, $fileName = 'data.xlsx')
    {
        switch ($settings['export_type']) {
            case 'startlist':
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

        jexit();
    }

    private function exportStartList($settings): array
    {
        $arrayData = $this->getRegistrations($settings);

        return $arrayData;
    }

    private function getRegistrations($configuration): array
    {
        $columns = array('r.id', 'c.title', 'g.title', 'r.team_name', 'r.arrival_date', 'r.contact_email', 'r.contact_phone', 'r.participants', 'r.payment_status');
        $labels = array('ID', 'Parcours', 'Group', 'Team Name', 'Arrival Date', 'Contact Email', 'Contact Phone', 'Participants', 'Payment Status');
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);

        $query->select($db->quoteName($columns, $labels));
        $query->from($db->quoteName('#__com_marathonmanager_registrations','r'));
        $query->join('LEFT', $db->quoteName('#__com_marathonmanager_courses', 'c') . ' ON ' . $db->quoteName('r.course_id') . ' = ' . $db->quoteName('c.id'));
        $query->join('LEFT', $db->quoteName('#__com_marathonmanager_groups', 'g') . ' ON ' . $db->quoteName('r.group_id') . ' = ' . $db->quoteName('g.id'));
        if ($configuration['only_paid']) {
            $query->where('r.payment_status = 1');
        }
        $db->setQuery($query);

        $rows = $db->loadAssocList();
        error_log(print_r($rows, true));
        foreach ($rows as &$row) {
            $row = $this->buildParticipants($row);
        }

        $this->buildStartNumbers($rows);

        // Add column titles
        array_unshift($rows, $labels);
        return $rows;
    }

    private function buildParticipants(array $row): array
    {
        $participants = json_decode($row['Participants'], true);
        if(empty($participants)) {
            return $row;
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
            }
        }
        $return = array();
        // Flat the Array (https://stackoverflow.com/a/1319903)
        // participants is an array of arrays
        array_walk_recursive($participants, function ($a) use (&$return) {
            $return[] = $a;
        });
        return array_merge($row, $return);
    }

    private function buildStartNumbers(&$rows)
    {

    }
}