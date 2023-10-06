<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_marathonmanager
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 *              All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

\defined('_JEXEC') or die;
$item = $displayData['item'];
$params = $displayData['params'];
?>

<div>
    <div class="uk-card uk-card-default uk-card-hover uk-card-small uk-position-relative">
        <div class="uk-card-media-top uk-height-small uk-cover-container">
            <?php
            if ($item->image) {
                echo LayoutHelper::render('joomla.html.image', ['src' => $item->image, 'alt' => $item->title, 'class' => 'uk-cover']);
            } else {

            }
            ?>
        </div>
        <div class="uk-card-body">
            <?php echo $item->title; ?>
        </div>
        <div class="uk-card-footer">
            <div class="uk-text-small">
                <span><?php
                    echo Text::sprintf('COM_MARATHONMANAGER_DATE_IN_LOCATION', HTMLHelper::date($item->event_date,'DATE_FORMAT_LC3'), $item->city);
                    ?>
                </span>
            </div>
        </div>
    </div>
</div>

