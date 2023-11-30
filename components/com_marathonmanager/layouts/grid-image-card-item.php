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
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Date\Date;

\defined('_JEXEC') or die;
$item = $displayData['item'];
$params = $displayData['params'];
$cardClasses = $params->get('element_cls', '') ?: '';

$previewAspectRatio = explode(':', $params->get('preview_aspect_ratio', '16:9'));
$previewAspectRatio[0] = $previewAspectRatio[0] * 10000;
$previewAspectRatio[1] = $previewAspectRatio[1] * 10000;

$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->addInlineStyle(
<<<CSS
.nxd-event-card-container {
    position: relative;
    overflow: hidden;
}
.nxd-event-card img {
   scale:1;
     transition: scale .4s ease-in-out;
}
.nxd-event-card:hover img {
    scale: 1.02;
    transition: scale .4s ease-in-out;
}
.nxd-event-card:hover .nxd-event-item-overlay {
    opacity: 0;
    transition: opacity .4s ease-in-out;
}
.nxd-event-item-overlay {
    background-color: rgba(0,0,0, 1);
    opacity: .5;
    transition: opacity .4s ease-in-out;
}
.nxd-event-card:hover .nxd-event-details-background {
    background: linear-gradient(0deg, rgba(0,0,0,0.8) 30%, rgba(0,0,0,0) 100%);
    transition: all .2s ease-in-out;
    bottom: 0;
}
.nxd-event-details-background {
    transition: all .4s ease-in-out;
    z-index: 1;
    bottom: -100%;
}
h3.nxd-event-title {
    color: #fff;
    margin-bottom: 4px;
    z-index: 2;
    position: relative;
}
.nxd-event-date {
    color: #fff;
    z-index: 2;
    position: relative;
}
.nxd-item-link {
    z-index: 9;
}
CSS
)
?>
<div class="nxd-event-card-container">
    <div class="uk-card uk-card-hover uk-position-relative <?php echo $cardClasses; ?> nxd-event-card">
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

        <div class="uk-position-cover nxd-event-item-overlay"></div>
        <div class="uk-position-bottom uk-padding-small">
            <div class="uk-position-cover nxd-event-details-background"></div>
            <h3 class="nxd-event-title"><?php echo $item->title; ?></h3>
            <div class="uk-text-small uk-text-truncate">
                <?php if($item->event_duration > 1):
                    $languageConstant = ($item->event_duration > 2) ? "COM_MARATHONMANAGER_EVENT_DATE_FROM_TO_MULTIPLE" : "COM_MARATHONMANAGER_EVENT_DATE_FROM_TO";
                    $startDate = new Date($item->event_date);
                    $endDate =  new Date($startDate . '+' . $item->event_duration-1 . ' day');
                    $dateString = Text::sprintf($languageConstant, HTMLHelper::date($startDate, 'd.'), HTMLHelper::date($endDate, 'DATE_FORMAT_LC3'));
                    ?>
                    <span class="nxd-event-date nxd-multiple-days-event"><?php echo $dateString ?></span>
                <?php else:?>
                <span class="nxd-event-date"><?php
                    echo Text::sprintf("COM_MARATHONMANAGER_EVENT_DATE_AT" , HTMLHelper::date($item->event_date, 'DATE_FORMAT_LC3'));
                    ?>
                </span>
                <?php endif; ?>
            </div>
        </div>

        <?php
        $now = new DateTime();
        if($item->registration_start_date > $now && $item->registration_end_date < $now): ?>
        <div class="uk-position-top uk-text-center uk-animation-slide-top" style="background:rgba(255,0,0,0.7); color:#fff;">
            <span class="nxd-text-register-now"><?php echo Text::_("COM_MARATHONMANAGER_REGISTER_NOW_TEXT");?></span>
        </div>
        <?php endif; ?>

        <?php if ($item->url): ?>
            <a href="<?php echo $item->url; ?>" class="uk-position-cover nxd-item-link"></a>
        <?php endif; ?>
    </div>
</div>

