<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_marathonmanager
 *
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\MarathonManager\Administrator\View\Courses;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;

// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Helper\ContentHelper;
use NXD\Component\MarathonManager\Administrator\Model\CourseModel;
use NXD\Component\MarathonManager\Administrator\Model\CoursesModel;

class HtmlView extends BaseHtmlView
{
	protected $items;

	public function display($tpl = null): void
	{
		/* @var CoursesModel $model */
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
		$user = Factory::getApplication()->getIdentity();
		ToolbarHelper::title(Text::_('COM_MARATHONMANAGER_COURSES_TITLE'), 'fas fa-folder-open');
		$toolbar = $this->getDocument()->getToolbar();

		// Add New Button if user has permissions to create
		if ($user->authorise('core.create', 'com_marathonmanager'))
		{
			ToolbarHelper::addNew('course.add');
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
			$childBar->publish('courses.publish')->listCheck(true);
			$childBar->unpublish('courses.unpublish')->listCheck(true);
			$childBar->archive('courses.archive')->listCheck(true);


			if ($user->authorise('core.admin', 'com_marathonmanager'))
			{
				$childBar->checkin('courses.checkin')->listCheck(true);
			}

			if ($this->state->get('filter.published') != -2)
			{
				$childBar->trash('courses.trash')->listCheck(true);
			}
		}

		if ($this->state->get('filter.published') == -2 && $user->authorise('core.delete', 'com_marathonmanager'))
		{
			$toolbar->delete('courses.delete')
				->text('JTOOLBAR_EMPTY_TRASH')
				->message('JGLOBAL_CONFIRM_DELETE')
				->listCheck(true);
		}

		$toolbar->appendButton('Link', 'map-signs', 'COM_MARATHONMANAGER_BTN_LABEL_SWITCH_TO_GROUPS', '/administrator/index.php?option=com_marathonmanager&view=groups');

		// Add Options Button if user has permissions to edit
		if ($user->authorise('core.admin', 'com_marathonmanager') || $user->authorise('core.options', 'com_marathonmanager'))
		{
			$toolbar->preferences('com_marathonmanager');
		}
	}
}