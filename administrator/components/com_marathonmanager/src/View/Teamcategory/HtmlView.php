<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_marathonmanager
 *
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\MarathonManager\Administrator\View\Teamcategory;

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
        $toolbarButtons = [];
        ToolbarHelper::title($isNew ? Text::_('COM_MARATHONMANAGER_TEAMCATEGORY_NEW') : Text::_('COM_MARATHONMANAGER_TEAMCATEGORY_EDIT'), 'fas fa-folder-open');

        // Build the actions for new and existing records.
        if ($isNew) {
            if ($canDo->get('core.create')) {
                ToolbarHelper::apply('teamcategory.apply');
                $toolbarButtons = [
                    ['save', 'teamcategory.save'],
                    ['save2new', 'teamcategory.save2new'],
                    ['save2copy', 'evteamcategoryent.save2copy']
                ];
            }
        } else {
            $itemEditable = $canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_by == $user->id);

            if ($itemEditable)
            {
                ToolbarHelper::apply('teamcategory.apply');
                $toolbarButtons[] = ['save', 'teamcategory.save'];
                // We can save this record, but check the create permission to see if we can return to make a new one.
                if ($canDo->get('core.create'))
                {
                    $toolbarButtons[] = ['save2new', 'teamcategory.save2new'];
                }

                // If checked out, we can still save
                if ($canDo->get('core.create'))
                {
                    $toolbarButtons[] = ['save2copy', 'teamcategory.save2copy'];
                }

            }
        }

        ToolbarHelper::saveGroup($toolbarButtons);

		ToolbarHelper::cancel('teamcategory.cancel', 'JTOOLBAR_CLOSE');

	}
}