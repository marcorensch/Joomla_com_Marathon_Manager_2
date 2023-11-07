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
        $columns = array('id', 'team_name', 'arrival_date', 'contact_email', 'contact_phone', 'participants', 'payment_status');
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);

        $query->select($db->quoteName($columns));
        $query->from($db->quoteName('#__com_marathonmanager_registrations'));
        if ($configuration['only_paid'])
            $query->where('payment_status = 1');
        $db->setQuery($query);

        $columnTitles = $this->getColumnTitles($columns);
        $rows = $db->loadAssocList();
        foreach ($rows as &$row) {
            $row = $this->buildParticipants($row);
        }
        array_unshift($rows, $columnTitles);
        return $rows;
    }

    private function getColumnTitles($columns): array
    {
        $titles = array();
        foreach ($columns as $column) {
            $titles[] = Text::_($column);
        }
        return $titles;
    }

    private function buildParticipants(array $row): array
    {
        $participants = json_decode($row['participants'], true);
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
                    }
                }
            }
        }
        $return = array();
        array_walk_recursive($participants, function ($a) use (&$return) {
            $return[] = $a;
        });
        return array_merge($row, $return);
    }
}