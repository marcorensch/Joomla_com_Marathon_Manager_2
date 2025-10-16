<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_marathonmanager
 *
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\MarathonManager\Administrator\View\Map;

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
		/* @var \NXD\Component\MarathonManager\Administrator\Model\MapModel $model */
	    $model = $this->getModel();
		$this->form = $model->getForm();
		$this->item = $model->getItem();

		$this->addToolbar();

        parent::display($tpl);
    }

	protected function addToolbar(): void
	{
		Factory::getApplication()->input->set('hidemainmenu', true);
		$isNew = (!$this->item->id);
        $user = Factory::getApplication()->getIdentity();
        $toolbarButtons = [];
        ToolbarHelper::title($isNew ? Text::_('COM_MARATHONMANAGER_MAP_NEW') : Text::_('COM_MARATHONMANAGER_MAP_EDIT'), 'fas fa-map');

        // Build the actions for new and existing records.
        if ($isNew) {
            if ($user->authorise('core.create', 'com_marathonmanager')) {
                ToolbarHelper::apply('map.apply');
                $toolbarButtons = [
                    ['save2new', 'map.save2new'],
                    ['save', 'map.save'],
                    ['save2copy', 'map.save2copy']
                ];
            }
        } else {
            $itemEditable = $user->authorise('core.edit', 'com_marathonmanager') || ($user->authorise('core.edit.own', 'com_marathonmanager') && $this->item->created_by == $user->id);

            if ($itemEditable)
            {
                ToolbarHelper::apply('map.apply');
                $toolbarButtons[] = ['save', 'map.save'];
                // We can save this record, but check the create permission to see if we can return to make a new one.
                if ($user->authorise('core.create', 'com_marathonmanager'))
                {
                    $toolbarButtons[] = ['save2new', 'map.save2new'];
                    $toolbarButtons[] = ['save2copy', 'map.save2copy'];
                }
            }
        }

        ToolbarHelper::saveGroup($toolbarButtons);

		ToolbarHelper::cancel('map.cancel', 'JTOOLBAR_CLOSE');
		ToolbarHelper::inlinehelp();

	}
}