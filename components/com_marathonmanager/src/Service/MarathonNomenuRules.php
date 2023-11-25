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
class MarathonNomenuRules implements RulesInterface
{
    /**
     * Router this rule belongs to
     *
     * @var RouterView
     * @since 3.4
     */
    protected $router;
    protected $itemId;

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
        if(isset($segments[2])){
            $vars['layout'] = $segments[2];
        }
        $vars['view'] = $segments[0];
        $vars['id'] = $vars['event_id'] = $this->getItemIdForAlias($segments[1]);
        array_shift($segments);
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

        // if the view is not event / registration, or the format is set, return
        $views = ['event', 'registration'];
        if ( !isset($query['view']) || (isset($query['view']) && (!in_array($query['view'], $views))) || isset($query['format']))
        {
            return;
        }

        $eventId = $query['id'] ?? $query['event_id'];
        $alias = $this->getAliasForEvent($eventId);
        if($alias){
            $segments[] = $query['view'];
            $segments[] = $alias;
        }else {
            return;
        }
        if(isset($query['layout'])){
            $segments[] = $query['layout'];
            unset($query['layout']);
        }
        // the last part of the url may be missing
        if (isset($query['slug'])) {
            $segments[] = $query['slug'];
            unset($query['slug']);
        }
        unset($query['view']);
        unset($query['id']);
        unset($query['event_id']);
    }

    private function getItemIdForAlias($alias){
        if(empty($alias)){
            return false;
        }
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        try
        {
            $query->select($db->quoteName('id'))
                ->from($db->quoteName('#__com_marathonmanager_events'))
                ->where($db->quoteName('alias') . ' = :alias')
                ->bind(':alias', $alias, ParameterType::STRING);
            $db->setQuery($query);
            return $db->loadResult();
        }
        catch (RuntimeException $e)
        {
            return false;
        }
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
            return $db->loadResult();
        }
        catch (RuntimeException $e)
        {
            $app->enqueueMessage($e->getMessage(), 'error');
            return false;
        }
    }
}