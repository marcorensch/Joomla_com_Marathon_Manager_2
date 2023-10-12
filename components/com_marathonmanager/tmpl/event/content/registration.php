<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_marathonmanager
 * @var         $event object the current event object
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 *              All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

\defined('_JEXEC') or die;
$user = Factory::getApplication()->getIdentity();
$isAdmin = $user->authorise('core.admin');
$isLoggedIn = !empty($user->id);
$alreadyRegistered = false;
if($isLoggedIn){
    $alreadyRegistered = $event->alreadyRegistered;
}

?>

<section class="uk-section uk-padding-remove-top">

    <?php if($alreadyRegistered) echo LayoutHelper::render('uk-alert',['text'=> Text::_('COM_MARATHONMANAGER_EVENT_ALREADY_REGISTERED'), 'type' =>'success']); ?>
    <?php if($isAdmin) echo LayoutHelper::render('uk-alert',['text'=> Text::_('COM_MARATHONMANAGER_EVENT_ALREADY_REGISTERED_ADMIN_NOTE'), 'type' =>'danger', 'size' => 'large']); ?>

    <?php if(!$isAdmin):?>
    <div class="uk-container">

    </div>
    <?php endif; ?>
</section>
