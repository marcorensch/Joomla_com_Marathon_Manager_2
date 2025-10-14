<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_marathonmanager
 *
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\MarathonManager\Site\Helper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use NXD\Component\MarathonManager\Site\Model\EventContentModel;
use stdClass;

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

        // Check if the results object is empty
        $hasResultFiles = !empty($event->result_files) && count((array)$event->result_files) > 0;
        $hasResultData = !empty($event->result_data) && count((array)$event->result_data) > 0;
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

        // Check if the event date is in the future
        $eventDate = new \DateTime($event->event_date);
        $eventDate->setTime(0, 0, 0);
        $todayDate = new \DateTime($today);
        $todayDate->setTime(0, 0, 0);
        $eventDateInFuture = $eventDate > $todayDate;


        $registrationOpen = ($event->registration_start_date < $today) && ($event->registration_end_date > $today);

        // Create the required menu options
        $menuOptions = array();
        $menuOptions[] = new EventContentModel(Text::_('COM_MARATHONMANAGER_BACK_TO_EVENTS'), 'back', 'thumbnails', Route::_('index.php?option=com_marathonmanager&view=events'), $currentLayout);
        $menuOptions[] = new EventContentModel(Text::_("COM_MARATHONMANAGER_DESCRIPTION_SUBMENU_LABEL"), 'default', 'file-text', Route::_('index.php?option=com_marathonmanager&view=event&id='.$event->id), $currentLayout);
        if (isset($event->teams) && count($event->teams) && $eventDateInFuture) {
            $menuOptions[] = new EventContentModel(Text::_("COM_MARATHONMANAGER_REGISTERED_SUBMENU_LABEL"), 'teams', 'list', Route::_('index.php?option=com_marathonmanager&view=event&id=' . $event->id . '&layout=teams'), $currentLayout);
        }
        if ($registrationOpen) {
            $menuOptions[] = new EventContentModel(Text::_("COM_MARATHONMANAGER_REGISTRATION_SUBMENU_LABEL"), 'registration', 'file-edit', Route::_('index.php?option=com_marathonmanager&view=registration&event_id=' . $event->id), $currentLayout);
        }
        if($hasResultFiles) {
            $menuOptions[] = new EventContentModel(Text::_("COM_MARATHONMANAGER_RESULTS_SUBMENU_LABEL"), 'results', 'list', Route::_('index.php?option=com_marathonmanager&view=event&id=' . $event->id . '&layout=results'), $currentLayout);
        }
        if($event->gallery_content !== "-1") {
            $menuOptions[] = new EventContentModel(Text::_("COM_MARATHONMANAGER_GALLERY_SUBMENU_LABEL"), 'gallery', 'image', Route::_('index.php?option=com_marathonmanager&view=event&id=' . $event->id . '&layout=gallery'), $currentLayout);
        }

        return $menuOptions;

    }
}
