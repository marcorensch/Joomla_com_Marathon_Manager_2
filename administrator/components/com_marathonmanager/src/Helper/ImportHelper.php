<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_footballmanager
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\MarathonManager\Administrator\Helper;

\defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;

class ImportHelper extends ComponentHelper
{
    protected $file = null;
    protected $data = [];
    protected $params = null;

    public function __construct($file, $params)
    {
        $this->file = $file;
        $this->params = $params;
    }

    public function import(): mixed
    {

        if (empty($this->file)) {
            Factory::getApplication()->enqueueMessage(Text::_("COM_MARATHONMANAGER_FILE_NOT_GIVEN_ERROR"), 'error');
            return false;
        }

        // Switch method based on file type
        $type = $this->file['type'];


        switch ($type) {
            case 'text/csv':
                $this->data = self::readDataFromUTF8CsvFile($this->file['tmp_name']);
                break;
            default:
                return false;
        }

        return $this->data;

    }

    /**
     * @description Read data from UTF8 CSV file and return array if the CSV is not UTF8 encoded it will return an empty array
     * @param $file
     * @return array
     * @since 1.0.0
     */
    private function readDataFromUTF8CsvFile($file): array
    {
        $data = [];
        $rowCounter = 0;
        $divider = $this->params['divider'] ?: ';';
        if (!mb_check_encoding(file_get_contents($file), 'UTF-8')){
            Factory::getApplication()->enqueueMessage(Text::_("COM_MARATHONMANAGER_FILE_NOT_UTF8_ERROR"), 'error');
            return $data;
        }
        if (($handle = fopen($file, "r")) !== FALSE) {
            while (($data[] = fgetcsv($handle, 0, $divider)) !== FALSE) {
                $rowCounter++;
            }
            fclose($handle);
        }
        // Remove complete empty rows
        foreach($data as $key => $row){
            if(!$row) unset($data[$key]);
        }
        return $data;
    }

    public static function getPublicAccessLevel(): int
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $query->select($db->quoteName('id'))
            ->from($db->quoteName('#__viewlevels'))
            ->where($db->quoteName('title') . ' = ' . $db->quote('Public'));
        $db->setQuery($query);
        return $db->loadResult();
    }

    public static function getGroupIds(): array
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $query->select($db->quoteName(['shortcode','id']))
            ->from($db->quoteName('#__com_marathonmanager_groups'));
        $db->setQuery($query);
        return $db->loadObjectList('shortcode');
    }

    public static function getParcourIds(): array
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $query->select($db->quoteName(['course_id','id']))
            ->from($db->quoteName('#__com_marathonmanager_courses'));
        $db->setQuery($query);
        return $db->loadObjectList('course_id');
    }

}