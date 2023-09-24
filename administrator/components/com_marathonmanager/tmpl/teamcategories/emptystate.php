<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_marathonmanager
 *
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Layout\LayoutHelper;

$displayData = [
    'textPrefix' => 'COM_MARATHONMANAGER_TEAMCATEGORIES',
    'formURL' => 'index.php?option=com_marathonmanager',
    'helpURL' => 'https://manuals.nx-designs.com/',
    'icon' => 'icon-copy',
];

$user = Factory::getApplication()->getIdentity();

if ($user->authorise('core.create', 'com_marathonmanager') || count($user->getAuthorisedCategories('com_marathonmanager', 'core.create')) > 0) {
    $displayData['createURL'] = 'index.php?option=com_marathonmanager&task=teamcategory.add';
}

echo LayoutHelper::render('joomla.content.emptystate', $displayData);