<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_marathonmanager
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 *              All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */


use Joomla\CMS\Layout\FileLayout;

\defined('_JEXEC') or die;
// Create the $event object for use in the template
$event = $this->event;

$eventHeader = new FileLayout('event-header', $basePath = JPATH_ROOT . '/components/com_marathonmanager/layouts');
echo $eventHeader->render(compact('event'));

?>

<h1>Registration Login required</h1>