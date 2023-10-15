<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_marathonmanager
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 *              All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */


use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use NXD\Component\MarathonManager\Site\Model\EventContentModel;

\defined('_JEXEC') or die;

$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->addInlineStyle('
.nxd-subnav-item-link {
    font-size: 1.1rem !important;
    padding: .8rem 2rem !important;
    color: #000 !important;
}
.nxd-subnav-item-link[aria-selected="true"] {
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
// Create the $event object for use in the template
$event = $this->item;
$form = $this->form;
$today = date('Y-m-d H:i:s');

// Set Page Header
$document = Factory::getApplication()->getDocument();
$document->setTitle($event->title);
$app = Factory::getApplication();

$user = Factory::getApplication()->getIdentity();
$canCreateRegistration = $user->authorise('registration.create', 'com_marathonmanager');
$isAdmin = $user->authorise('core.admin');
$isLoggedIn = !empty($user->id);

$registrationOpen = ($event->registration_start_date < $today) && ($event->registration_end_date > $today);

// Create the required menu options
$menuOptions = array();
$menuOptions[] = new EventContentModel(Text::_('COM_MARATHONMANAGER_BACK_TO_EVENTS'), 'back', 'thumbnails', Route::_('index.php?option=com_marathonmanager&view=events'));
$menuOptions[] = new EventContentModel('Description', 'description', 'file-text', '', true);
if ($registrationOpen) $menuOptions[] = new EventContentModel('Register', 'registration', 'file-edit', Route::_('index.php?option=com_marathonmanager&view=registration&layout=edit&event_id=' . $event->id));
$menuOptions[] = new EventContentModel('Results', 'results', 'list');
$menuOptions[] = new EventContentModel('Gallery', 'gallery', 'image');

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
    <section class="nxd-section uk-margin-small-top">
        <div class="uk-background-muted uk-border-rounded">
            <div class="uk-padding-small uk-position-relative uk-flex@l">
                <ul class="uk-subnav uk-subnav-pill uk-margin-remove-bottom uk-child-width-1-1 uk-child-width-auto@m"
                    uk-switcher="connect: .switcher-container">
                    <?php foreach ($menuOptions as $menuOption) {
                        echo $menuOption->renderMenuItem();
                    } ?>

                </ul>
            </div>
        </div>
    </section>

    <section class="nxd-section uk-margin-small-top">
        <ul class="uk-switcher switcher-container uk-margin">
            <?php foreach ($menuOptions as $menuOption) :
                $sectionClassName = 'marathonmanager-event-' . $menuOption->alias . '-section';
                $containerClassName = 'marathonmanager-event-' . $menuOption->alias . '-container';
                if(str_starts_with($menuOption->url, '#')):
                ?>
                <li>
                    <section class="uk-section uk-padding-remove-top <?php echo $sectionClassName; ?>">
                        <div class="uk-container uk-container-expand <?php echo $containerClassName; ?>">
                            <div class="uk-padding uk-padding-remove-horizontal">
                                <h2><?php echo $menuOption->title; ?></h2>
                                <div>
                                    <?php
                                    if (file_exists(JPATH_ROOT . '/components/com_marathonmanager/tmpl/event/content/' . $menuOption->alias . '.php')) {
                                        include JPATH_ROOT . '/components/com_marathonmanager/tmpl/event/content/' . $menuOption->alias . '.php';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </section>
                </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </section>
<?php endif;

