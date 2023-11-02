<h1>Registration view</h1>

<?php

use Joomla\CMS\HTML\HTMLHelper;

echo '<pre>' . var_export($this->registration, true) . '</pre>';
$app = \Joomla\CMS\Factory::getApplication();
$params = $app->getParams();

echo '<pre>' . var_export($params, true) . '</pre>';

?>

<div class="uk-margin-top" uk-margin>
    <div class="uk-card uk-card-default">

        <div class="uk-grid uk-flex-middle">
            <div class="uk-width-1-2@s uk-width-1-4@m">
                <div>
                    <?php echo HTMLHelper::image($this->registration->paymentInformation->qr_bank, 'QR Code Bank', array('class' => 'uk-width-1-1 uk-padding-small')); ?>
                </div>
            </div>
            <div class="uk-width-expand">
                <div class="uk-card-body">
                    <h3 class="uk-card-title">Payment information</h3>
                    <p>Banking Payment information</p>
                    <p>foo</p>
                    <p>bar</p>
                    <p>dings</p>
                </div>
            </div>

        </div>
    </div>
    <div class="uk-card uk-card-default">
        <div class="uk-grid uk-flex-middle">
            <div class="uk-width-1-2@s uk-width-1-4@m">
                <div>
                    <?php echo HTMLHelper::image($this->registration->paymentInformation->qr_twint, 'QR Code Twint', array('class' => 'uk-width-1-1 uk-padding-small')); ?>
                </div>
            </div>
            <div class="uk-width-expand">
                <div class="uk-card-body">
                    <h3 class="uk-card-title">Twint Payment information</h3>
                    <p>Twint Payment information</p>
                    <p>foo</p>
                    <p>bar</p>
                    <p>dings</p>
                </div>
            </div>
        </div>
    </div>

</div>
