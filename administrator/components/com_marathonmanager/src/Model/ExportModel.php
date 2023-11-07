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

        if(empty($form)){
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
    public function export($settings, $fileName = 'data.xlsx'){
        error_log(print_r($settings, true));
        error_log('Exporting in MODEL');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Hello World !');

        $writer = new Xlsx($spreadsheet);
        $writer->save('hello world.xlsx');

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        $writer->save('php://output');
        jexit();
    }
}