<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_marathonmanager
 *
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\MarathonManager\Site\Helper;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use NXD\Component\MarathonManager\Site\Model\EventContentModel;

\defined('_JEXEC') or die;

class EventRenderHelper {

    /**
     * @description Create the menu options for the event page
     * @param $event
     *
     * @return array
     *
     * @since 1.0.0
     * @throws \Exception
     */
    public static function createMenuOptions($event):array {

        $currentView = Factory::getApplication()->getInput()->get('view', 'event', 'cmd');

        if($currentView === 'registration') {
            $currentLayout = 'registration';
        }else{
            $currentLayout = Factory::getApplication()->getInput()->get('layout', 'default', 'cmd');
        }

        $user = Factory::getApplication()->getIdentity();
        $canCreateRegistration = $user->authorise('registration.create', 'com_marathonmanager');
        $isAdmin = $user->authorise('core.admin');
        $isLoggedIn = !empty($user->id);
        $today = date('Y-m-d H:i:s');

        $registrationOpen = ($event->registration_start_date < $today) && ($event->registration_end_date > $today);

        // Create the required menu options
        $menuOptions = array();
        $menuOptions[] = new EventContentModel(Text::_('COM_MARATHONMANAGER_BACK_TO_EVENTS'), 'back', 'thumbnails', Route::_('index.php?option=com_marathonmanager&view=events'), $currentLayout);
        $menuOptions[] = new EventContentModel(Text::_("COM_MARATHONMANAGER_DESCRIPTION_SUBMENU_LABEL"), 'default', 'file-text', Route::_('index.php?option=com_marathonmanager&view=event&id='.$event->id), $currentLayout);
        if ($registrationOpen) {
            $menuOptions[] = new EventContentModel(Text::_("COM_MARATHONMANAGER_REGISTRATION_SUBMENU_LABEL"), 'registration', 'file-edit', Route::_('index.php?option=com_marathonmanager&view=registration&event_id=' . $event->id), $currentLayout);
        }
        $menuOptions[] = new EventContentModel(Text::_("COM_MARATHONMANAGER_RESULTS_SUBMENU_LABEL"), 'results', 'list', Route::_('index.php?option=com_marathonmanager&view=event&id='.$event->id.'&layout=results'), $currentLayout);
        $menuOptions[] = new EventContentModel(Text::_("COM_MARATHONMANAGER_GALLERY_SUBMENU_LABEL"), 'gallery', 'image', Route::_('index.php?option=com_marathonmanager&view=event&id='.$event->id.'&layout=gallery'), $currentLayout);

        return $menuOptions;

    }
}