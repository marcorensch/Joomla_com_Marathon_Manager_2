<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_marathonmanager
 *
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\MarathonManager\Administrator\View\Events;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Helper\ContentHelper;

class HtmlView extends BaseHtmlView
{
	protected $items;
    public function display($tpl = null): void
    {
        $this->items            = $this->get('Items');
        $this->pagination       = $this->get('Pagination');
        $this->filterForm       = $this->get('FilterForm');
        $this->activeFilters    = $this->get('ActiveFilters');
        $this->state            = $this->get('State');

		if(!count($this->items) && $this->get('IsEmptyState'))
		{
			$this->setLayout('emptystate');
		}

		$this->addToolbar();

        parent::display($tpl);
    }

	protected function addToolbar(): void
	{
		$canDo = ContentHelper::getActions('com_marathonmanager');
        $user          = Factory::getApplication()->getIdentity();

		ToolbarHelper::title(Text::_('COM_MARATHONMANAGER_EVENTS_TITLE'), 'fas fa-calendar-alt');
        $toolbar = Toolbar::getInstance();


        // Show Buttons only if the user is allowed to do so
        if ($canDo->get('core.create') || count($user->getAuthorisedCategories('com_marathonmanager', 'core.create')) > 0)
        {
            $toolbar->addNew('event.add');
        }
        if ($canDo->get('core.edit.state'))
        {
            $dropdown = $toolbar->dropdownButton('status-group')
                ->text('JTOOLBAR_CHANGE_STATUS')
                ->toggleSplit(false)
                ->icon('fa fa-ellipsis-h')
                ->buttonClass('btn btn-action')
                ->listCheck(true);

            $childBar = $dropdown->getChildToolbar();
            $childBar->publish('events.publish')->listCheck(true);
            $childBar->unpublish('events.unpublish')->listCheck(true);
            $childBar->archive('events.archive')->listCheck(true);


            if ($user->authorise('core.admin'))
            {
                $childBar->checkin('events.checkin')->listCheck(true);
            }

            if ($this->state->get('filter.published') != -2)
            {
                $childBar->trash('events.trash')->listCheck(true);
            }
        }

        if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete'))
        {
            $toolbar->delete('events.delete')
                ->text('JTOOLBAR_EMPTY_TRASH')
                ->message('JGLOBAL_CONFIRM_DELETE')
                ->listCheck(true);
        }

	}
}