<?php

/**
 * @param array $displaydata containing the data to display
 *
 * @subpackage  com_marathonmanager
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 *              All rights reserved
 *
 * @package     Joomla.Site
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;
$text = key_exists('text', $displayData) ? $displayData['text'] : '';
$type = key_exists('type', $displayData) ? $displayData['type'] : 'default';
$size = key_exists('size', $displayData) ? $displayData['size'] : 'default';

$class = 'uk-alert-' . $type . ' uk-text-' . $size . ' uk-text-center';
?>

<div class="<?php echo $class; ?>" uk-alert>
    <p><?php echo $text ?></p>
</div>