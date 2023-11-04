<?php

namespace NXD\Component\MarathonManager\Site\Model;

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