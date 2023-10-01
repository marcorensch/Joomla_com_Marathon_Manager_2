<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_marathonmanager
 *
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\MarathonManager\Administrator\View\Event;

\defined('_JEXEC') or die;

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
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');

		$this->addToolbar();

        parent::display($tpl);
    }

	protected function addToolbar(): void
	{
		Factory::getApplication()->input->set('hidemainmenu', true);
		$isNew = (!$this->item->id);
        $user = Factory::getApplication()->getIdentity();
        $canDo = ContentHelper::getActions('com_marathonmanager', 'category', $this->item->catid);

        ToolbarHelper::title($isNew ? Text::_('COM_MARATHONMANAGER_EVENT_NEW') : Text::_('COM_MARATHONMANAGER_EVENT_EDIT'), 'fas fa-calendar');

        // Build the actions for new and existing records.
        if ($isNew) {
            if (count($user->getAuthorisedCategories('com_marathonmanager', 'core.create')) > 0) {
                ToolbarHelper::apply('event.apply');
                $toolbarButtons = [
                    ['save2new', 'event.save2new'],
                    ['save', 'event.save'],
                    ['save2copy', 'event.save2copy']
                ];
            }
        } else {
            $itemEditable = $canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_by == $user->id);

            if ($itemEditable)
            {
                ToolbarHelper::apply('event.apply');
                $toolbarButtons[] = ['save', 'event.save'];
                // We can save this record, but check the create permission to see if we can return to make a new one.
                if ($canDo->get('core.create'))
                {
                    $toolbarButtons[] = ['save2new', 'event.save2new'];
                }

                // If checked out, we can still save
                if ($canDo->get('core.create'))
                {
                    $toolbarButtons[] = ['save2copy', 'event.save2copy'];
                }

            }
        }

        ToolbarHelper::saveGroup($toolbarButtons);

		ToolbarHelper::cancel('event.cancel', 'JTOOLBAR_CLOSE');

	}
}