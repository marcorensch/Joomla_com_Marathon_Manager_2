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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Router\Route;
use NXD\Component\MarathonManager\Site\Model\RegistrationModel;

/**
 * (DB) Courses Model
 *
 * @since  1.0.0
 */
class CoursesModel extends BaseDatabaseModel
{
    public function getCourses()
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select($db->quoteName(['a.id', 'a.title']))
            ->from($db->quoteName('#__com_marathonmanager_courses', 'a'))
            ->where($db->quoteName('a.published') . ' = 1');

        $db->setQuery($query);
        $items = $db->loadObjectList('id');

        return $items;
    }

}