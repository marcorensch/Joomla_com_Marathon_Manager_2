<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_marathonmanager
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 *              All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;

\defined('_JEXEC') or die;
$item = $displayData['item'];
$params = $displayData['params'];
$cardClasses = $params->get('element_cls', '') ?: '';

$previewAspectRatio = explode(':', $params->get('preview_aspect_ratio', '16:9'));
$previewAspectRatio[0] = $previewAspectRatio[0] * 10000;
$previewAspectRatio[1] = $previewAspectRatio[1] * 10000;
?>

<div class="nxd-event-card-container">
    <div class="uk-card uk-card-default uk-card-hover uk-card-small uk-position-relative <?php echo $cardClasses; ?> nxd-event-card">
        <div class="uk-cover-container nxd-event-card-image-container">
            <canvas width="<?php echo $previewAspectRatio[0];?>" height="<?php echo $previewAspectRatio[1];?>" class="uk-background-secondary"></canvas>
            <?php
            if ($item->image) {
                echo LayoutHelper::render('joomla.html.image', ['src' => $item->image, 'alt' => $item->title, 'uk-cover' => 'true']);
            }else{
                echo '<span class="uk-position-center" uk-icon="icon: image; ratio: 4" style="opacity: .1"></span>';
            }
            ?>
        </div>
        <div class="uk-card-body nxd-event-card-content-container">
            <h3 class="uk-card-title"><?php echo $item->title; ?></h3>
        </div>
        <div class="uk-card-footer nxd-event-card-footer-container">
            <div class="uk-text-small uk-text-truncate">
                <span><?php
                    echo HTMLHelper::date($item->event_date, 'DATE_FORMAT_LC3');
                    ?>
                </span>
            </div>
        </div>
        <?php if ($item->url): ?>
            <a href="<?php echo $item->url; ?>" class="uk-position-cover"></a>
        <?php endif; ?>
    </div>
</div>

