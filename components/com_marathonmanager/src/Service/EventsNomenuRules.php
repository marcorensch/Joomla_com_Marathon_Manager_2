<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\MarathonManager\Site\Service;

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\Rules\RulesInterface;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;
use RuntimeException;

/**
 * Rule to process URLs without a menu item
 *
 * @since  3.4
 */
class EventsNomenuRules implements RulesInterface
{
    /**
     * Router this rule belongs to
     *
     * @var RouterView
     * @since 3.4
     */
    protected $router;

    /**
     * Class constructor.
     *
     * @param   RouterView  $router  Router this rule belongs to
     *
     * @since   3.4
     */
    public function __construct(RouterView $router)
    {
        $this->router = $router;
    }

    /**
     * Dummymethod to fullfill the interface requirements
     *
     * @param   array  &$query  The query array to process
     *
     * @return  void
     *
     * @since   3.4
     * @codeCoverageIgnore
     */
    public function preprocess(&$query)
    {
        $test = 'Test';
    }

    /**
     * Parse a menu-less URL
     *
     * @param   array  &$segments  The URL segments to parse
     * @param   array  &$vars      The vars that result from the segments
     *
     * @return  void
     *
     * @since   3.4
     */
    public function parse(&$segments, &$vars)
    {
        // with this url: http://localhost/events/event-n/event-title.html
        // segments: [[0] => event-n, [1] => event-title]
        // vars: [[option] => com_marathonmanager, [view] => events, [id] => 0]

        $vars['view'] = 'event';
        $vars['id'] = substr($segments[0], strpos($segments[0], '-') + 1);
        array_shift($segments);
        array_shift($segments);
        return;
    }

    /**
     * Build a menu-less URL
     *
     * @param   array  &$query     The vars that should be converted
     * @param   array  &$segments  The URL segments to create
     *
     * @return  void
     *
     * @since   3.4
     */
    public function build(&$query, &$segments)
    {
        // content of $query ($segments is empty or [[0] => mywalk-3])
        // when called by the menu: [[option] => com_mywalks, [Itemid] => 126]
        // when called by the component: [[option] => com_mywalks, [view] => mywalk, [id] => 1, [Itemid] => 126]
        // when called from a module: [[option] => com_mywalks, [view] => mywalks, [format] => html, [Itemid] => 126]
        // when called from breadcrumbs: [[option] => com_mywalks, [view] => mywalks, [Itemid] => 126]

        // the url should look like this: /site-root/mywalks/walk-n/walk-title.html



        // if the view is not mywalk - the single walk view
        if (!isset($query['view']) || (isset($query['view']) && $query['view'] !== 'event') || isset($query['format']))
        {
            return;
        }
        $alias = $this->getAliasForEvent($query['id']);
        error_log('alias: ' . var_export($alias, true));
        if($alias){

            $segments[] = $alias;
        }else {
            $segments[] = $query['view'] . '-' . $query['id'];
        }
        // the last part of the url may be missing
        if (isset($query['slug'])) {
            $segments[] = $query['slug'];
            unset($query['slug']);
        }
        unset($query['view']);
        unset($query['id']);
    }

    private function getAliasForEvent($id){
        if(!is_numeric($id)){
            return false;
        }
        $app = Factory::getApplication();
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        try
        {
            $query->select($db->quoteName('alias'))
                ->from($db->quoteName('#__com_marathonmanager_events'))
                ->where($db->quoteName('id') . ' = :id')
                ->bind(':id', $id, ParameterType::INTEGER);
            $db->setQuery($query);
            $app->enqueueMessage('NICE', 'default');
            return $db->loadResult();
        }
        catch (RuntimeException $e)
        {
            $app->enqueueMessage($e->getMessage(), 'error');
            return false;
        }
    }
}