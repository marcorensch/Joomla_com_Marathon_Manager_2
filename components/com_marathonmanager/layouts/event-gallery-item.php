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
$item = $displayData['picture_item'];

$originalFile = $item->image;
$thumbnailFile = '/cache/com_marathonmanager/' . $originalFile;
$classnames = 'event-gallery-image';
if (file_exists(JPATH_ROOT . $thumbnailFile)) {
    $previewUri = $thumbnailFile;
    $classnames .= ' event-gallery-image-thumbnail';
}else{
    $previewUri = $originalFile;
}

?>

<div>
    <div class="uk-cover-container image-container">

        <?php
        echo LayoutHelper::render('joomla.html.image', ['src' => $previewUri, 'alt' => $item->title, 'class' => $classnames, 'lazy' => 'true', 'uk-cover' => 'true', 'data-src' => $previewUri, 'title' => $item->title, 'aria-label' => $item->title ]);
        ?>
        <a href="<?php echo $originalFile;?>" class="uk-position-cover"></a>
    </div>
</div>
