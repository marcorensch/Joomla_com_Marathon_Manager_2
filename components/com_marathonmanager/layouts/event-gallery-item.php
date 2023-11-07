<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_marathonmanager
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 *              All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\HTML\HTMLHelper;

\defined('_JEXEC') or die;
$item = $displayData['picture_item'];

?>

<div>
    <div class="uk-cover-container image-container">

        <?php
        echo HTMLHelper::_('image', $item->image, $item->title, array('lazy' => 'true', 'uk-cover' => 'true', 'data-src' => $item->image, 'title' => $item->title, 'aria-label' => $item->title ));
        ?>
        <a href="<?php echo $item->image;?>" class="uk-position-cover"></a>
    </div>
</div>
