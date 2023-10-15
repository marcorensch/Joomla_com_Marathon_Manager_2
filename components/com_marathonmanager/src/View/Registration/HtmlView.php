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

use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
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
        $this->form = $this->get('Form');
        $this->return_page = $this->get('ReturnPage');

        if ($this->event) {
            parent::display($tpl);
        } else {
            throw new \Exception('Event not found', 404);
        }

    }
}