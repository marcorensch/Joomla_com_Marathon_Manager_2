<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_marathonmanager
 *
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\MarathonManager\Administrator\View\Result;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends BaseHtmlView
{
	protected $form;
	protected $item;

    public function display($tpl = null): void
    {

		/* @var \NXD\Component\MarathonManager\Administrator\Model\ResultModel $model */
	    $model = $this->getModel();

        // check if we are in import mode
        if($this->getLayout() === 'import')
        {
            $this->form = $model->getImportForm();
            $this->setLayout('import');
            $this->addImportToolbar();
            parent::display($tpl);
            return;
        }

        $this->form = $model->getForm();

        if($this->getLayout() === 'import_map_fields'){
            // Get Imported Data from Session if their any
            $this->importData = Factory::getApplication()->getUserState('com_marathonmanager.results.import.data', []);

            if (count($this->importData))
            {
                $this->setLayout('import_map_fields');
            }else{
                Factory::getApplication()->enqueueMessage(Text::_('COM_MARATHONMANAGER_RESULTS_IMPORT_NO_DATA'), 'warning');
                //Redirect to import view
                $this->setLayout('import');
                parent::display($tpl);
                return;
            }
            $this->addImportToolbar();

            parent::display($tpl);
            return;
        }



		$this->item = $model->getItem();

		$this->addDefaultToolbar();

        parent::display($tpl);
    }

    protected function addImportToolbar(): void
    {
        Factory::getApplication()->input->set('hidemainmenu', true);
        ToolbarHelper::title(Text::_('COM_MARATHONMANAGER_RESULTS_IMPORT'), 'fas fa-table');
        ToolbarHelper::cancel('result.cancel', 'JTOOLBAR_CANCEL');

        if($this->getLayout() === 'import_map_fields'){
            ToolbarHelper::custom('results.processdata', 'chevron-right','', 'COM_MARATHONMANAGER_RESULTS_PROCESS_DATA', false);
        }

    }

	protected function addDefaultToolbar(): void
	{
		Factory::getApplication()->input->set('hidemainmenu', true);
		$isNew = (!$this->item->id);
        $user = Factory::getApplication()->getIdentity();
        $toolbarButtons = [];
        ToolbarHelper::title($isNew ? Text::_('COM_MARATHONMANAGER_RESULT_NEW') : Text::_('COM_MARATHONMANAGER_RESULT_EDIT'), 'fas fa-table');

		// Build the actions for new and existing records.
        if ($isNew) {
            if ($user->authorise('core.create', 'com_marathonmanager')) {
                ToolbarHelper::apply('result.apply');
                $toolbarButtons = [
                    ['save', 'result.save'],
                    ['save2new', 'result.save2new'],
                    ['save2copy', 'result.save2copy']
                ];
            }
        } else {
            $itemEditable = $user->authorise('core.edit','com_marathonmanager') || ($user->authorise('core.edit.own','com_marathonmanager') && $this->item->created_by === $user->id);

            if ($itemEditable)
            {
                ToolbarHelper::apply('result.apply');
                $toolbarButtons[] = ['save', 'result.save'];
                // We can save this record, but check the create permission to see if we can return to make a new one.
                if ($user->authorise('core.create', 'com_marathonmanager'))
                {
                    $toolbarButtons[] = ['save2new', 'result.save2new'];
                }

                // If checked out, we can still save
                if ($user->authorise('core.create', 'com_marathonmanager'))
                {
                    $toolbarButtons[] = ['save2copy', 'result.save2copy'];
                }

            }
        }

        ToolbarHelper::saveGroup($toolbarButtons);

		ToolbarHelper::cancel('result.cancel', 'JTOOLBAR_CLOSE');

		ToolbarHelper::inlinehelp();

	}
}