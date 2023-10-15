<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_marathonmanager
 * @var         $event object the current event object
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 *              All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

\defined('_JEXEC') or die;
$user = Factory::getApplication()->getIdentity();
$isAdmin = $user->authorise('core.admin');
$isLoggedIn = !empty($user->id);
$alreadyRegistered = false;
if ($isLoggedIn) {
    $alreadyRegistered = $this->event->alreadyRegistered;
}

$loginUrl = Route::_('index.php?option=com_users&view=login&return=' . base64_encode("index.php?option=com_marathonmanager&layout=registration&event_id=" . $this->event->id));

?>


<?php if ($alreadyRegistered) echo LayoutHelper::render('uk-alert', ['text' => Text::_('COM_MARATHONMANAGER_EVENT_ALREADY_REGISTERED'), 'type' => 'success']); ?>
<?php if ($isAdmin) echo LayoutHelper::render('uk-alert', ['text' => Text::_('COM_MARATHONMANAGER_EVENT_ALREADY_REGISTERED_ADMIN_NOTE'), 'type' => 'danger', 'size']); ?>

<?php if (!$isAdmin): ?>
    <?php if ($isLoggedIn): ?>
        <div class="">
            <?php include __DIR__ . '/form.php'; ?>
        </div>
    <?php else: ?>
        <div class="uk-margin-large-top uk-margin-auto uk-width-xlarge uk-card uk-card-default uk-card-hover uk-card-body uk-text-center uk-text-large">
            <p><?php echo Text::sprintf('COM_MARATHONMANAGER_REGISTRATION_FORM_LOGIN_REQUIRED', $loginUrl); ?></p>
        </div>
    <?php endif; ?>
<?php endif; ?>

