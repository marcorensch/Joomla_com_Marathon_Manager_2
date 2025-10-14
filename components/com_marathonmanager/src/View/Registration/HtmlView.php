<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_marathonmanager
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 *              All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\MarathonManager\Site\View\Registration;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Router\Route;
use NXD\Component\MarathonManager\Site\Model\EventModel;

class HtmlView extends BaseHtmlView
{
    /**
     * The Form object
     *
     * @var  Form
     */
    protected $form;

    /**
     * The event object for this registration
     *
     * @var  EventModel
     */
    protected $event;

    public function display($tpl = null): void
    {
        $this->event = $this->get('Event');

        $user = Factory::getApplication()->getIdentity();
        $isAdmin = $user->authorise('core.admin');
        $isLoggedIn = !empty($user->id);

        if ($this->event) {

            $eventHeader = new FileLayout('event-header', $basePath = JPATH_ROOT . '/components/com_marathonmanager/layouts');
            $event = $this->event;
            echo $eventHeader->render(compact('event'));

            // Set the Layout by default to "edit"
            $this->setLayout('edit');

            if ($isLoggedIn) {

                if ($isAdmin) {
                    $this->setLayout('admin');
                    parent::display($tpl);
                    return;
                }

                // switch template / layout if already registered
                if ($this->event->alreadyRegistered) {
                    $this->registration = $this->get('Registration');
                    $this->setLayout('view');
                } else {
                    $this->form = $this->get('Form');
                    $this->mapoption = $this->get('MapOption');
                    $this->return_page = $this->get('ReturnPage');
                    $this->setLayout('edit');
                }

            } else {
                $app = Factory::getApplication();
                $app->enqueueMessage(Text::_('COM_MARATHONMANAGER_REGISTRATION_LOGIN_REQUIRED'), 'warning');
                $app->redirect(Route::_('index.php?option=com_users&view=login&return=' . base64_encode("index.php?option=com_marathonmanager&view=registration&layout=edit&event_id=" . $this->event->id)));
                return;
            }

            parent::display($tpl);

        } else {
            throw new \Exception('Event not found', 404);
        }

    }
}