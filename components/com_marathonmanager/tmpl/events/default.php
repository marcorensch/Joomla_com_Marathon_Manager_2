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

$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$css = <<<CSS
body main.tm-main{
padding-top:0;
}
body main.tm-main>div.uk-container{
    padding:0;
    margin:0;
    max-width:100%;
}
CSS;
$wa->addInlineStyle($css);



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
<?php if ($params->get('page_header', '')) {
    $page = Factory::getApplication()->getMenu()->getActive();
    $pageParams = $page->getParams();
    $heading = $pageParams->get('page_heading') ?: $page->title;
    echo '<section class="uk-cover-container uk-height-large">';
    echo HTMLHelper::image($params->get('page_header', ''), 'header', array('class' => '', 'uk-cover' => ''));
    echo '<div class="uk-position-cover" style="background-color: rgba(0, 0, 0, 0.58);"></div>';
    echo '<div class="uk-container uk-position-center uk-light">                
                   <div class="uk-width-1-1">              
                        <div class="uk-text-lead uk-text-center">Swiss International Mountain Marathon</div>
                        <h1 class="uk-heading-large uk-margin-remove-top uk-text-center">' . $heading . '</h1>
                    </div>
          </div>';
    echo '</section>';
}
?>

<section class="uk-section">
    <div class="uk-container">
        <div class="uk-position-relative">
            <div class="<?php echo $gridClasses; ?> " uk-grid uk-height-match=".uk-card-body">
                <?php foreach ($this->items as $event): ?>
                    <?php
                    $item = $event;
                    $item->url = Route::_('index.php?option=com_marathonmanager&view=event&id=' . $item->id);
                    $layout = new FileLayout('grid-card-item', $basePath = JPATH_ROOT . '/components/com_marathonmanager/layouts');
                    echo $layout->render(compact('item', 'params'));
                    ?>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</section>
