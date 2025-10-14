<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_marathonmanager
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 *              All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\MarathonManager\Site\Model;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class EventContentModel
{
    public $title;
    public $alias;
    public $icon;
    public $active = false;
    public $url = '#';
    private $classList = [];

    public function __construct($title, $alias, $icon, $url, $activeAlias)
    {
        $this->title = $title;
        $this->alias = $alias;
        $this->icon = $icon;
        $this->active = $activeAlias === $alias;
        $this->url = $url;
        $this->classList = ['nxd-subnav-item-link', 'uk-border-rounded'];

    }

    public function renderMenuItem(): string
    {
        return '<li ' . $this->renderActiveState() . '>
                    <a href="' . $this->renderUrl() . '"  class="'.implode(' ', $this->classList).'">
                        <i uk-icon="icon:' . $this->icon . ';"></i> ' . $this->title . '
                    </a>
                </li>';
    }

    private function renderActiveState(): string
    {
        return $this->active ? 'class="uk-active"' : '';
    }

    private function renderUrl(): string
    {
        if (!in_array(trim($this->url), ['#', '']) ) {
            return $this->url;
        }
        return '#' . $this->alias;
    }
}