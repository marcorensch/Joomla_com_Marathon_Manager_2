<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_marathonmanager
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 *              All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Layout\LayoutHelper;
use NXD\Component\MarathonManager\Site\Helper\EventRenderHelper;
use Joomla\CMS\Factory;

\defined('_JEXEC') or die;
$event = $displayData['event'];

// Set Page Header
$document = Factory::getApplication()->getDocument();
$document->setTitle($event->title);
$app = Factory::getApplication();

$menuOptions = EventRenderHelper::createMenuOptions($event);

// Subnav styling
$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->addInlineStyle('
a.nxd-subnav-item-link {
    font-size: 1.1rem !important;
    padding: .8rem 2rem !important;
    color: #000 !important;
}
.uk-active a.nxd-subnav-item-link {
    color: #fff !important;
}
');
$wa->addInlineScript('
//Handles subnav elements with external links class: nxd-subnav-item-link-external click event
document.addEventListener("DOMContentLoaded", function(event) {
    var externalLinks = document.querySelectorAll(".nxd-subnav-item-link-external");
    externalLinks.forEach(function(link) {
        link.addEventListener("click", function(event) {
            event.preventDefault();
            window.open(link.href, "_self");
        });
    });
});
');

?>
<section class="nxd-section">
    <div class="uk-container uk-container-expand">
        <div class="uk-border-rounded uk-overflow-hidden">
            <header class="uk-cover-container uk-height-large">
                <?php echo LayoutHelper::render('joomla.html.image', ['src' => $event->image, 'alt' => $event->title, 'uk-cover' => 'true']); ?>
                <div class="uk-overlay uk-overlay-primary uk-position-bottom">
                    <h1 class="uk-h1"><?php echo $event->title; ?></h1>
                </div>
            </header>
        </div>
    </div>
</section>

<?php if ($menuOptions): ?>
<section class="nxd-section uk-margin-small-top uk-margin-bottom">
    <div class="uk-background-muted uk-border-rounded">
        <div class="uk-padding-small uk-position-relative uk-flex@l">
            <ul class="uk-subnav uk-subnav-pill uk-margin-remove-bottom uk-child-width-1-1 uk-child-width-auto@m">
                <?php foreach ($menuOptions as $menuOption) {
                    echo $menuOption->renderMenuItem();
                } ?>
            </ul>
        </div>
    </div>
</section>
<?php endif; ?>