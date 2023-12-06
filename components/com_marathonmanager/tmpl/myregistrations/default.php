<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_marathonmanager
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 *              All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

\defined('_JEXEC') or die;

$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->useStyle('com_marathonmanager.my-registrations-css');
$wa->useScript('com_marathonmanager.my-registrations-js');
?>
<div class="uk-container" uk-margin uk-grid uk-scrollspy="target:>div; cls: uk-animation-slide-bottom; delay: 50;">
    <?php
    for ($i = 0; $i < 4; $i++):
        ?>
        <div class="uk-width-1-1">
            <div class="uk-card uk-card-default registration-card">
                <div class="uk-card-header nxd-registration-header">
                    <div class="uk-grid-small uk-flex uk-flex-middle">
                        <div class="uk-width-auto">
                            <img class="uk-border-circle nxd-event-registration-image" src="https://picsum.photos/60/60"
                                 alt="">
                        </div>
                        <div class="uk-width-expand">
                            <h3 class="uk-margin-remove-bottom">Event Title</h3>
                            <span class="uk-text-meta">12. - 14.12.2023</span>
                        </div>
                        <div class="uk-width-auto">
                            <?php if ($i % 2 === 0): ?>
                            <span uk-icon="icon: check; ratio:2" class="uk-text-success" uk-tooltip="<?php echo Text::_("COM_MARATHONMANAGER_INFO_PAYMENT_REGISTERED");?>"></span>
                            <?php else: ?>
                            <span class="uk-text-warning pulsing" uk-icon="icon: clock; ratio:2" uk-tooltip="<?php echo Text::_("COM_MARATHONMANAGER_INFO_PAYMENT_NOT_REGISTERED");?>"></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="nxd-registration-details uk-overflow-hidden">
                    <div class="uk-card-body ">
                        Foo
                    </div>
                </div>
            </div>
        </div>
    <?php endfor; ?>
</div>
