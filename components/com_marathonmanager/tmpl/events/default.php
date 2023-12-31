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
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Router\Route;
use Joomla\Registry\Registry;

\defined('_JEXEC') or die;

$params = new Registry($this->params);
$items = $this->items;
// Grid Columns
$gridColumnsClassString = ' uk-child-width-1-' . $this->params->get('events_cols', 1) . ' ';
$gridColumnsClassString .= 'uk-child-width-1-' . $this->params->get('events_cols_s', 2) . '@s ';
$gridColumnsClassString .= 'uk-child-width-1-' . $this->params->get('events_cols_m', 3) . '@m ';
$gridColumnsClassString .= 'uk-child-width-1-' . $this->params->get('events_cols_l', 4) . '@l ';
$gridColumnsClassString .= 'uk-child-width-1-' . $this->params->get('events_cols_xl', 5) . '@xl ';

$gridGap = ' uk-grid-' . $this->params->get('events_grid_gap', 'medium');

$gridAlignement = ' uk-flex-' . $this->params->get('events_grid_alignement', 'center');
$gridClasses = $gridColumnsClassString . $gridGap . $gridAlignement;
?>

<?php
if ($params->get('debug', 0)) {
    $debugLayout = new FileLayout('nxd-debug', $basePath = JPATH_ROOT . '/components/com_marathonmanager/layouts');
    echo $debugLayout->render(compact('items', 'params'));
}
?>
<?php
$headerLayout = new FileLayout('marathon-header', $basePath = JPATH_ROOT . '/components/com_marathonmanager/layouts');
echo $headerLayout->render();

?>

<section class="uk-section">
    <div class="uk-container">
        <div class="uk-position-relative">
            <div class="<?php echo $gridClasses; ?> " uk-grid uk-height-match=".uk-card-body">
                <?php foreach ($this->items as $event): ?>
                    <?php
                    $item = $event;
                    $item->url = Route::_('index.php?option=com_marathonmanager&view=event&id=' . $item->id);
                    $configuratedLayout = $params->get('elements_layout', 'grid-card-item');
                    $layout = new FileLayout($configuratedLayout, $basePath = JPATH_ROOT . '/components/com_marathonmanager/layouts');
                    echo $layout->render(compact('item', 'params'));
                    ?>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</section>
