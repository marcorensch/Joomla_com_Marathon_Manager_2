<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_marathonmanager
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\MarathonManager\Site\Service;

\defined('_JEXEC') or die;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\Database\DatabaseInterface;

/**
 * Routing class from com_marathonmanager
 *
 * @since  __BUMP_VERSION__
 */
class Router extends RouterView
{
    /**
     * Flag to remove IDs
     *
     * @var    boolean
     */
    protected $noIDs = false;

    /**
     * The category factory
     *
     * @var CategoryFactoryInterface
     *
     * @since  __BUMP_VERSION__
     */
    private $categoryFactory;

    /**
     * The category cache
     *
     * @var  array
     *
     * @since  __BUMP_VERSION__
     */
    private $categoryCache = [];

    /**
     * The db
     *
     * @var DatabaseInterface
     *
     * @since  __BUMP_VERSION__
     */
    private $db;

    /**
     * Content Component router constructor
     *
     * @param   SiteApplication           $app              The application object
     * @param   AbstractMenu              $menu             The menu object to work with
     * @param   CategoryFactoryInterface  $categoryFactory  The category object
     * @param   DatabaseInterface         $db               The database object
     */
    public function __construct(SiteApplication $app, AbstractMenu $menu, CategoryFactoryInterface $categoryFactory, DatabaseInterface $db)
    {
        $this->categoryFactory = $categoryFactory;
        $this->db              = $db;

        $params = ComponentHelper::getParams('com_marathonmanager');
        $this->noIDs = (bool) $params->get('sef_ids');
        $categories = new RouterViewConfiguration('categories');
        $categories->setKey('id');
        $this->registerView($categories);
        $category = new RouterViewConfiguration('category');
        $category->setKey('id')->setParent($categories, 'catid')->setNestable();
        $this->registerView($category);

        $events = new RouterViewConfiguration('events');
        $this->registerView($events);

        $event = new RouterViewConfiguration('event');
        $event->setKey('id');
        $this->registerView($event);

        $registration = new RouterViewConfiguration('registration');
        $registration->setKey('event_id');
        $this->registerView($registration);

	    $registrations = new RouterViewConfiguration('registrations');
	    $registrations->setKey('event_id');
	    $this->registerView($registrations);

		// IDK if it's necessary to register this view / if it is in use:
        $myRegistrations = new RouterViewConfiguration('myregistrations');
        $this->registerView($myRegistrations);

//        $this->registerView(new RouterViewConfiguration('featured'));
        $form = new RouterViewConfiguration('form');
        $form->setKey('id');
        $this->registerView($form);

        parent::__construct($app, $menu);

        $this->attachRule(new MenuRules($this));
        $this->attachRule(new StandardRules($this));
        $this->attachRule(new MarathonNomenuRules($this));
    }

}
