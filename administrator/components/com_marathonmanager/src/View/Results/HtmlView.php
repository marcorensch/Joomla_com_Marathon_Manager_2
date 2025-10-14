<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_marathonmanager
 *
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\MarathonManager\Administrator\View\Results;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Button\CustomButton;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Helper\ContentHelper;

class HtmlView extends BaseHtmlView
{
    protected $items;


    public function display($tpl = null): void
    {
		/* @var \NXD\Component\MarathonManager\Administrator\Model\ResultsModel $model */
	    $model = $this->getModel();
        $this->items = $model->getItems();
        $this->pagination = $model->getPagination();
        $this->filterForm = $model->getFilterForm();
        $this->activeFilters = $model->getActiveFilters();
        $this->state = $model->getState();

        if (!count($this->items) && $model->getIsEmptyState()) {
            $this->setLayout('emptystate');
        }

        $this->addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar(): void
    {
        $user = Factory::getApplication()->getIdentity();


        ToolbarHelper::title(Text::_('COM_MARATHONMANAGER_RESULTS_TITLE'), 'fas fa-table');
	    $toolbar = $this->getDocument()->getToolbar();

	    // Add New Button if user has permissions to create
        if ($user->authorise('core.create', 'com_marathonmanager')) {
            ToolbarHelper::addNew('result.add');
        }

        // Show the batch buttons only if the layout is not import_map_fields
        if ($user->authorise('core.edit.state', 'com_marathonmanager') && $this->getLayout() !== 'import_map_fields')
        {
            $dropdown = $toolbar->dropdownButton('status-group')
                ->text('JTOOLBAR_CHANGE_STATUS')
                ->toggleSplit(false)
                ->icon('fa fa-ellipsis-h')
                ->buttonClass('btn btn-action')
                ->listCheck(true);

            $childBar = $dropdown->getChildToolbar();
            $childBar->publish('results.publish')->listCheck(true);
            $childBar->unpublish('results.unpublish')->listCheck(true);
            $childBar->archive('results.archive')->listCheck(true);


            if ($user->authorise('core.admin'))
            {
                $childBar->checkin('results.checkin')->listCheck(true);
            }

            if ($this->state->get('filter.published') != -2)
            {
                $childBar->trash('results.trash')->listCheck(true);
            }
        }

        if ($this->state->get('filter.published') == -2 && $user->authorise('core.delete', 'com_marathonmanager'))
        {
            $toolbar->delete('results.delete')
                ->text('JTOOLBAR_EMPTY_TRASH')
                ->message('JGLOBAL_CONFIRM_DELETE')
                ->listCheck(true);
        }

        if($user->authorise('core.edit', 'com_marathonmanager'))
        {
            ToolbarHelper::custom('results.showImport', 'upload', '', 'COM_MARATHONMANAGER_IMPORT', false);
        }

        // Add Options Button if user has permissions to edit
        if ($user->authorise('core.admin', 'com_marathonmanager') || $user->authorise('core.options', 'com_marathonmanager'))
        {
            $toolbar->preferences('com_marathonmanager');
        }
    }
}