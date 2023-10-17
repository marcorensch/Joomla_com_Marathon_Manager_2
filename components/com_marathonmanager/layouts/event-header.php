<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_marathonmanager
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 *              All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Layout\LayoutHelper;

\defined('_JEXEC') or die;
$event = $displayData['event'];
?>
<section class="nxd-section">
    <div class="uk-container uk-container-expand">
        <div class="uk-border-rounded uk-overflow-hidden">
            <header class="uk-cover-container uk-height-large">
                <?php echo LayoutHelper::render('joomla.html.image', ['src' => $event->image, 'alt' => $event->title, 'uk-cover' => 'true']); ?>
                <div class="uk-overlay uk-overlay-primary uk-position-bottom">
                    <h1 class="uk-h1"><?php echo $event->title; ?></h1>
                </div>
            </header>
        </div>
    </div>
</section>