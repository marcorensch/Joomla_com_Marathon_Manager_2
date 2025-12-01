<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_marathonmanager
 *
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\MarathonManager\Administrator\View\Registrations;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Helper\ContentHelper;
use NXD\Component\MarathonManager\Administrator\Model\RegistrationsModel;

class HtmlView extends BaseHtmlView
{
    protected $items;

    public function display($tpl = null): void
    {
		/* @var RegistrationsModel $model */
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

        ToolbarHelper::title(Text::_('COM_MARATHONMANAGER_REGISTRATIONS_TITLE'), 'fas fa-file-signature');
	    $toolbar = $this->getDocument()->getToolbar();

	    // Add New Button if user has permissions to create
        if ($user->authorise('registration.create', 'com_marathonmanager')) {
            ToolbarHelper::addNew('registration.add');
        }

        if ($user->authorise('registration.edit.state', 'com_marathonmanager'))
        {
            $dropdown = $toolbar->dropdownButton('status-group')
                ->text('JTOOLBAR_CHANGE_STATUS')
                ->toggleSplit(false)
                ->icon('fa fa-ellipsis-h')
                ->buttonClass('btn btn-action')
                ->listCheck(true);

            $childBar = $dropdown->getChildToolbar();
            $childBar->publish('registrations.publish')->listCheck(true);
            $childBar->unpublish('registrations.unpublish')->listCheck(true);
            $childBar->archive('registrations.archive')->listCheck(true);

            if ($user->authorise('registrations.admin', 'com_marathonmanager'))
            {
                $childBar->checkin('registrations.checkin')->listCheck(true);
            }

            if ($this->state->get('filter.published') != -2)
            {
                $childBar->trash('registrations.trash')->listCheck(true);
            }
        }

        if ($this->state->get('filter.published') == -2 && $user->authorise('registration.delete', 'com_marathonmanager'))
        {
            $toolbar->delete('registrations.delete')
                ->text('JTOOLBAR_EMPTY_TRASH')
                ->message('JGLOBAL_CONFIRM_DELETE')
                ->listCheck(true);
        }

        // Add Options Button if user has permissions to edit
        if ($user->authorise('core.admin', 'com_marathonmanager') || $user->authorise('core.options', 'com_marathonmanager'))
        {
            $toolbar->preferences('com_marathonmanager');
        }

    }
}