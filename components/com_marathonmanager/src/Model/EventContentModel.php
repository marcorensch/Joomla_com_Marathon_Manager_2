<?php

namespace NXD\Component\MarathonManager\Site\Model;

class EventContentModel
{
    public $title;
    public $alias;
    public $icon;
    public $active = false;
    public $url = '#';
    private $onclick = '';
    private $classList = [];

    public function __construct($title, $alias, $icon, $url = '#', $active = false)
    {
        $this->title = $title;
        $this->alias = $alias;
        $this->icon = $icon;
        $this->active = $active;
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
            $this->classList[] = 'nxd-subnav-item-link-external';
            return $this->url;
        }
        return '#' . $this->alias;
    }
}