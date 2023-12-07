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
use Joomla\CMS\Factory;

\defined('_JEXEC') or die;
$params = Factory::getApplication()->getParams();
$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->useStyle('com_marathonmanager.full-width-layout');

?>

<?php if ($params->get('page_header', '')) {
    $page = Factory::getApplication()->getMenu()->getActive();
    $pageParams = $page->getParams();
    $heading = $pageParams->get('page_heading') ?: $page->title;
    echo '<section class="uk-cover-container uk-height-large">';
    echo LayoutHelper::render('joomla.html.image', ['src' => $params->get('page_header', ''), 'alt' => $heading, 'class' => '', 'uk-cover' => 'true']);
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