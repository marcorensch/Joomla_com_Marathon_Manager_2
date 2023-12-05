<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_marathonmanager
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 *              All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\MarathonManager\Site\View\Event;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView
{
    protected $item;

    /**
     * The Form object
     *
     * @var  Form
     */
    protected $form;

    public function display($tpl = null): void
    {
        $this->item = $this->get('Item');
        $this->parcours = $this->get('Courses');
        $this->categories = $this->get('Groups');
        $this->results = $this->get('Results');
        $this->params = Factory::getApplication()->getParams();


        if ($this->item) {
            parent::display($tpl);
        } else {
            throw new \Exception('Item not found', 404);
        }

    }
}