<?php
/**
 * @package     Joomla.Administrator
 *              Joomla.Site
 * @subpackage  com_footballmanager
 * @author      Marco Rensch
 * @since        1.0.0
 *
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @copyright   Copyright (C) 2022 nx-designs NXD
 *
 */

namespace NXD\Component\MarathonManager\Administrator\Field;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;
use Joomla\CMS\Form\FormHelper;

FormHelper::loadFieldClass('list');

defined('_JEXEC') or die;

class AcyMailingListSelectionField extends ListField
{

    protected $type = 'AcyMailingListSelection';

    protected function getOptions(): array
    {
        $options = [];
        $options[] = HTMLHelper::_('select.option', '', Text::_('COM_MARATHONMANAGER_FIELD_DEFAULT_SELECT_ACYMAILING_LIST'));

        $db = Factory::getContainer()->get(DatabaseInterface::class);
        try {
            $db->transactionStart();
            $query = $db->getQuery(true);
            $query->select('id, name');
            $query->from('#__acym_list');
            $query->order('creation_date DESC, name ASC');
            $db->setQuery($query);
            $dbValues = $db->loadObjectList();

            $db->transactionCommit();

        } catch (\Exception $e) {
            $app = Factory::getApplication();
            if($e->getCode() == 1146){
                $msg = "AcyMailing is not installed.";
            }else{
                $msg = $e->getMessage();
            }
            $app->enqueueMessage($msg, 'warning');
        }

        if (!empty($dbValues)) {
            foreach ($dbValues as $option) {
                $options[] = HTMLHelper::_('select.option', $option->id, $option->name);
            }
        }

        return array_merge(parent::getOptions(), $options);

    }

}