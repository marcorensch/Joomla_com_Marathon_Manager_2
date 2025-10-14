<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_marathonmanager
 *
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\MarathonManager\Administrator\View\Arrivaloptions;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;

// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarFactoryInterface;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Helper\ContentHelper;
use NXD\Component\MarathonManager\Administrator\Model\ArrivaloptionsModel;

class HtmlView extends BaseHtmlView
{
	protected $items;

	public function display($tpl = null): void
	{
		/* @var ArrivaloptionsModel $model */
		$model               = $this->getModel();
		$this->items         = $model->getItems();
		$this->pagination    = $model->getPagination();
		$this->filterForm    = $model->getFilterForm();
		$this->activeFilters = $model->getActiveFilters();
		$this->state         = $model->getState();

		if (!count($this->items) && $model->getIsEmptyState())
		{
			$this->setLayout('emptystate');
		}

		$this->addToolbar();

		parent::display($tpl);
	}

	protected function addToolbar(): void
	{
		$canDo = ContentHelper::getActions('com_marathonmanager');
		$user  = Factory::getApplication()->getIdentity();

		ToolbarHelper::title(Text::_('COM_MARATHONMANAGER_ARRIVAL_OPTIONS_TITLE'), 'fas fa-car');
		$toolbar = $this->getDocument()->getToolbar();

		// Add New Button if user has permissions to create
		if ($user->authorise('core.create', 'com_marathonmanager'))
		{
			ToolbarHelper::addNew('arrivaloption.add');
		}

		if ($user->authorise('core.edit.state', 'com_marathonmanager'))
		{
			$dropdown = $toolbar->dropdownButton('status-group')
				->text('JTOOLBAR_CHANGE_STATUS')
				->toggleSplit(false)
				->icon('fa fa-ellipsis-h')
				->buttonClass('btn btn-action')
				->listCheck(true);

			$childBar = $dropdown->getChildToolbar();
			$childBar->publish('arrivaloptions.publish')->listCheck(true);
			$childBar->unpublish('arrivaloptions.unpublish')->listCheck(true);
			$childBar->archive('arrivaloptions.archive')->listCheck(true);


			if ($user->authorise('core.admin', 'com_marathonmanager'))
			{
				$childBar->checkin('arrivaloptions.checkin')->listCheck(true);
			}

			if ($this->state->get('filter.published') != -2)
			{
				$childBar->trash('arrivaloptions.trash')->listCheck(true);
			}
		}

		if ($this->state->get('filter.published') == -2 && $user->authorise('core.delete', 'com_marathonmanager'))
		{
			$toolbar->delete('arrivaloptions.delete')
				->text('JTOOLBAR_EMPTY_TRASH')
				->message('JGLOBAL_CONFIRM_DELETE')
				->listCheck(true);
		}

		// Add Options Button if user has permissions to edit
		if ($user->authorise('core.options', 'com_marathonmanager') || $user->authorise('core.admin', 'com_marathonmanager'))
		{
			ToolbarHelper::preferences('com_marathonmanager');
		}

	}
}