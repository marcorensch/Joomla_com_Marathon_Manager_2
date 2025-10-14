<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_marathonmanager
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 *              All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\MarathonManager\Site\View\Registrations;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Router\Route;
use NXD\Component\MarathonManager\Site\Model\EventModel;

class HtmlView extends BaseHtmlView
{

    protected $items;

    public function display($tpl = null): void
    {
        $this->items = $this->get('Registrations');
        if(empty($this->items)){
            $this->setLayout('empty');
        }
        parent::display($tpl);
    }
}