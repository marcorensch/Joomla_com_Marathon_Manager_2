<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_marathonmanager
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 *              All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\MarathonManager\Site\View\Registration;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
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

            if ($isLoggedIn) {

                if ($isAdmin) {
                    $this->setLayout('registration_admin');
                    parent::display($tpl);
                    return;
                }

                // switch template / layout if already registered
                if ($this->event->alreadyRegistered) {
                    $this->registration = $this->get('Registration');
                    $this->setLayout('registration_view');
                } else {
                    $this->form = $this->get('Form');
                    $this->mapoption = $this->get('MapOption');
                    $this->return_page = $this->get('ReturnPage');
                }

            } else {
                $app = Factory::getApplication();
                $app->enqueueMessage(Text::_('COM_MARATHONMANAGER_REGISTRATION_LOGIN_REQUIRED'), 'warning');
                $app->redirect(Route::_('index.php?option=com_users&view=login&return=' . base64_encode("index.php?option=com_marathonmanager&view=registration&layout=edit&event_id=" . $this->event->id)));
                $this->setLayout('registration_login');
            }

            parent::display($tpl);

        } else {
            throw new \Exception('Event not found', 404);
        }

    }
}