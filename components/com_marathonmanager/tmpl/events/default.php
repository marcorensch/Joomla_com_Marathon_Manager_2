<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_marathonmanager
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 *              All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Layout\FileLayout;
use Joomla\Registry\Registry;

\defined('_JEXEC') or die;

$params = new Registry($this->params);
$items = $this->items;

// Grid Columns
$gridColumnsClassString = 'uk-child-width-1-' . $this->params->get('events_cols', 1) . ' ';
$gridColumnsClassString .= 'uk-child-width-1-' . $this->params->get('events_cols_s', 2) . '@s ';
$gridColumnsClassString .= 'uk-child-width-1-' . $this->params->get('events_cols_m', 3) . '@m ';
$gridColumnsClassString .= 'uk-child-width-1-' . $this->params->get('events_cols_l', 4) . '@l ';
$gridColumnsClassString .= 'uk-child-width-1-' . $this->params->get('events_cols_xl', 5) . '@xl ';

?>

<?php
if($params->get('debug',0))
{
    $debugLayout = new FileLayout('nxd-debug', $basePath = JPATH_ROOT . '/components/com_marathonmanager/layouts');
    echo $debugLayout->render(compact('items', 'params'));
}
?>

<section class="uk-section uk-padding-remove">
    <div class="uk-card uk-card-small uk-card-body">
        <h1>Events</h1>
    </div>
</section>

<section class="uk-section">
    <div class="uk-container">
        <div class="uk-position-relative">
            <div class="uk-flex-center <?php echo $gridColumnsClassString; ?> " uk-grid>
                <?php foreach ($this->items as $event): ?>
                    <?php
                    $item = $event;
                    $layout = new FileLayout('grid-card-item', $basePath = JPATH_ROOT . '/components/com_marathonmanager/layouts');
                    echo $layout->render(compact('item', 'params'));
                    ?>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</section>
