<?php

namespace NXD\Component\MarathonManager\Site\Model;

class EventContentModel
{
    public $title;
    public $alias;
    public $icon;

    public function __construct($title, $alias, $icon)
    {
        $this->title = $title;
        $this->alias = $alias;
        $this->icon = $icon;
    }

    public function renderMenuItem()
    {
        return '<li>
                    <a href="#'.$this->alias.'" class="nxd-subnav-item-link uk-border-rounded">
                        <i uk-icon="icon:'.$this->icon.';"></i> '.$this->title.'
                    </a>
                </li>';
    }
}