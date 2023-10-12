<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_marathonmanager
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 *              All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */


use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Factory;
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
// Create the $event object for use in the template
$event = $this->item;
$today = date('Y-m-d H:i:s');

$user = Factory::getApplication()->getIdentity();
$canCreateRegistration = $user->authorise('registration.create', 'com_marathonmanager');

$isLoggedIn = !empty($user->id);

$registrationOpen = ($event->registration_start_date < $today) && ($event->registration_end_date > $today);


// Create the required menu options
$menuOptions = array();
$menuOptions[] = new EventContentModel('Description', 'description', 'file-text');
if($isLoggedIn && $registrationOpen && $canCreateRegistration ) $menuOptions[] = new EventContentModel('Register', 'registration', 'file-edit');
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
            <div class="uk-padding-small uk-position-relative">
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
            <?php foreach ($menuOptions as $menuOption) {
                include 'content/' . $menuOption->alias . '.php';
            } ?>
        </ul>
    </section>
<?php endif;

