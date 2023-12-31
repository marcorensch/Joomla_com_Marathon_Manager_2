<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  mod_nxd_quickicons
 *
 * @copyright   Copyright (C) 2022 by nx-designs.ch
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */


namespace NXD\Component\MarathonManager\Administrator\Field;

use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');


// The class name must always be the same as the filename (in camel case)
class IconSelectorField extends FormField
{

    //The field class must know its own type through the variable $type.
    protected $type = 'IconSelector';

    protected $iconList = array(
        "no-icon",
        "fas fa-address-book",
        "fas fa-address-card",
        "fas fa-adjust",
        "fas fa-align-center",
        "fas fa-align-justify",
        "fas fa-align-left",
        "fas fa-align-right",
        "fas fa-ambulance",
        "fas fa-american-sign-language-interpreting",
        "fas fa-anchor",
        "fas fa-angle-double-down",
        "fas fa-angle-double-left",
        "fas fa-angle-double-right",
        "fas fa-angle-double-up",
        "fas fa-angle-down",
        "fas fa-angle-left",
        "fas fa-angle-right",
        "fas fa-angle-up",
        "fas fa-archive",
        "fas fa-arrow-alt-circle-down",
        "fas fa-arrow-alt-circle-left",
        "fas fa-arrow-alt-circle-right",
        "fas fa-arrow-alt-circle-up",
        "fas fa-arrow-circle-down",
        "fas fa-arrow-circle-left",
        "fas fa-arrow-circle-right",
        "fas fa-arrow-circle-up",
        "fas fa-arrow-down",
        "fas fa-arrow-left",
        "fas fa-arrow-right",
        "fas fa-arrow-up",
        "fas fa-arrows-alt",
        "fas fa-arrows-alt-h",
        "fas fa-arrows-alt-v",
        "fas fa-assistive-listening-systems",
        "fas fa-asterisk",
        "fas fa-at",
        "fas fa-audio-description",
        "fas fa-backward",
        "fas fa-balance-scale",
        "fas fa-ban",
        "fas fa-barcode",
        "fas fa-bars",
        "fas fa-bath",
        "fas fa-battery-empty",
        "fas fa-battery-full",
        "fas fa-battery-half",
        "fas fa-battery-quarter",
        "fas fa-battery-three-quarters",
        "fas fa-bed",
        "fas fa-beer",
        "fas fa-bell",
        "fas fa-bell-slash",
        "fas fa-bicycle",
        "fas fa-binoculars",
        "fas fa-birthday-cake",
        "fas fa-blind",
        "fas fa-bold",
        "fas fa-bolt",
        "fas fa-bomb",
        "fas fa-book",
        "fas fa-bookmark",
        "fas fa-braille",
        "fas fa-briefcase",
        "fas fa-bug",
        "fas fa-building",
        "fas fa-bullhorn",
        "fas fa-bullseye",
        "fas fa-bus",
        "fas fa-calculator",
        "fas fa-calendar",
        "fas fa-calendar-alt",
        "fas fa-calendar-check",
        "fas fa-calendar-minus",
        "fas fa-calendar-plus",
        "fas fa-calendar-times",
        "fas fa-camera",
        "fas fa-camera-retro",
        "fas fa-car",
        "fas fa-caret-down",
        "fas fa-caret-left",
        "fas fa-caret-right",
        "fas fa-caret-square-down",
        "fas fa-caret-square-left",
        "fas fa-caret-square-right",
        "fas fa-caret-square-up",
        "fas fa-caret-up",
        "fas fa-cart-arrow-down",
        "fas fa-cart-plus",
        "fas fa-certificate",
        "fas fa-chart-area",
        "fas fa-chart-bar",
        "fas fa-chart-line",
        "fas fa-chart-pie",
        "fas fa-check",
        "fas fa-check-circle",
        "fas fa-check-square",
        "fas fa-chevron-circle-down",
        "fas fa-chevron-circle-left",
        "fas fa-chevron-circle-right",
        "fas fa-chevron-circle-up",
        "fas fa-chevron-down",
        "fas fa-chevron-left",
        "fas fa-chevron-right",
        "fas fa-chevron-up",
        "fas fa-child",
        "fas fa-circle",
        "fas fa-circle-notch",
        "fas fa-clipboard",
        "fas fa-clock",
        "fas fa-clone",
        "fas fa-closed-captioning",
        "fas fa-cloud",
        "fas fa-cloud-download-alt",
        "fas fa-cloud-upload-alt",
        "fas fa-code",
        "fas fa-code-branch",
        "fas fa-coffee",
        "fas fa-cog",
        "fas fa-cogs",
        "fas fa-columns",
        "fas fa-comment",
        "fas fa-comment-alt",
        "fas fa-comments",
        "fas fa-compass",
        "fas fa-compress",
        "fas fa-copy",
        "fas fa-copyright",
        "fas fa-credit-card",
        "fas fa-crop",
        "fas fa-crosshairs",
        "fas fa-cube",
        "fas fa-cubes",
        "fas fa-cut",
        "fas fa-database",
        "fas fa-deaf",
        "fas fa-desktop",
        "fas fa-dollar-sign",
        "fas fa-dot-circle",
        "fas fa-download",
        "fas fa-edit",
        "fas fa-eject",
        "fas fa-ellipsis-h",
        "fas fa-ellipsis-v",
        "fas fa-envelope",
        "fas fa-envelope-open",
        "fas fa-envelope-square",
        "fas fa-eraser",
        "fas fa-euro-sign",
        "fas fa-exchange-alt",
        "fas fa-exclamation",
        "fas fa-exclamation-circle",
        "fas fa-exclamation-triangle",
        "fas fa-expand",
        "fas fa-expand-arrows-alt",
        "fas fa-external-link-alt",
        "fas fa-external-link-square-alt",
        "fas fa-eye",
        "fas fa-eye-dropper",
        "fas fa-eye-slash",
        "fas fa-fast-backward",
        "fas fa-fast-forward",
        "fas fa-fax",
        "fas fa-female",
        "fas fa-fighter-jet",
        "fas fa-file",
        "fas fa-file-alt",
        "fas fa-file-archive",
        "fas fa-file-audio",
        "fas fa-file-code",
        "fas fa-file-excel",
        "fas fa-file-image",
        "fas fa-file-pdf",
        "fas fa-file-powerpoint",
        "fas fa-file-video",
        "fas fa-file-word",
        "fas fa-film",
        "fas fa-filter",
        "fas fa-fire",
        "fas fa-fire-extinguisher",
        "fas fa-flag",
        "fas fa-flag-checkered",
        "fas fa-flask",
        "fas fa-folder",
        "fas fa-folder-open",
        "fas fa-font",
        "fas fa-forward",
        "fas fa-frown",
        "fas fa-futbol",
        "fas fa-gamepad",
        "fas fa-gavel",
        "fas fa-gem",
        "fas fa-genderless",
        "fas fa-gift",
        "fas fa-glass-martini",
        "fas fa-globe",
        "fas fa-graduation-cap",
        "fas fa-h-square",
        "fas fa-hand-lizard",
        "fas fa-hand-paper",
        "fas fa-hand-peace",
        "fas fa-hand-point-down",
        "fas fa-hand-point-left",
        "fas fa-hand-point-right",
        "fas fa-hand-point-up",
        "fas fa-hand-pointer",
        "fas fa-hand-rock",
        "fas fa-hand-scissors",
        "fas fa-hand-spock",
        "fas fa-handshake",
        "fas fa-hashtag",
        "fas fa-hdd",
        "fas fa-heading",
        "fas fa-headphones",
        "fas fa-heart",
        "fas fa-heartbeat",
        "fas fa-history",
        "fas fa-home",
        "fas fa-hospital",
        "fas fa-hourglass",
        "fas fa-hourglass-end",
        "fas fa-hourglass-half",
        "fas fa-hourglass-start",
        "fas fa-i-cursor",
        "fas fa-id-badge",
        "fas fa-id-card",
        "fas fa-image",
        "fas fa-images",
        "fas fa-inbox",
        "fas fa-indent",
        "fas fa-industry",
        "fas fa-info",
        "fas fa-info-circle",
        "fas fa-italic",
        "fas fa-key",
        "fas fa-keyboard",
        "fas fa-language",
        "fas fa-laptop",
        "fas fa-leaf",
        "fas fa-lemon",
        "fas fa-level-down-alt",
        "fas fa-level-up-alt",
        "fas fa-life-ring",
        "fas fa-lightbulb",
        "fas fa-link",
        "fas fa-lira-sign",
        "fas fa-list",
        "fas fa-list-alt",
        "fas fa-list-ol",
        "fas fa-list-ul",
        "fas fa-location-arrow",
        "fas fa-lock",
        "fas fa-lock-open",
        "fas fa-long-arrow-alt-down",
        "fas fa-long-arrow-alt-left",
        "fas fa-long-arrow-alt-right",
        "fas fa-long-arrow-alt-up",
        "fas fa-low-vision",
        "fas fa-magic",
        "fas fa-magnet",
        "fas fa-male",
        "fas fa-map",
        "fas fa-map-marker",
        "fas fa-map-marker-alt",
        "fas fa-map-pin",
        "fas fa-map-signs",
        "fas fa-mars",
        "fas fa-mars-double",
        "fas fa-mars-stroke",
        "fas fa-mars-stroke-h",
        "fas fa-mars-stroke-v",
        "fas fa-medkit",
        "fas fa-meh",
        "fas fa-mercury",
        "fas fa-microchip",
        "fas fa-microphone",
        "fas fa-microphone-slash",
        "fas fa-minus",
        "fas fa-minus-circle",
        "fas fa-minus-square",
        "fas fa-mobile",
        "fas fa-mobile-alt",
        "fas fa-money-bill-alt",
        "fas fa-moon",
        "fas fa-motorcycle",
        "fas fa-mouse-pointer",
        "fas fa-music",
        "fas fa-neuter",
        "fas fa-newspaper",
        "fas fa-object-group",
        "fas fa-object-ungroup",
        "fas fa-outdent",
        "fas fa-paint-brush",
        "fas fa-paper-plane",
        "fas fa-paperclip",
        "fas fa-paragraph",
        "fas fa-paste",
        "fas fa-pause",
        "fas fa-pause-circle",
        "fas fa-paw",
        "fas fa-pen-square",
        "fas fa-pencil-alt",
        "fas fa-percent",
        "fas fa-phone",
        "fas fa-phone-square",
        "fas fa-phone-volume",
        "fas fa-plane",
        "fas fa-play",
        "fas fa-play-circle",
        "fas fa-plug",
        "fas fa-plus",
        "fas fa-plus-circle",
        "fas fa-plus-square",
        "fas fa-podcast",
        "fas fa-pound-sign",
        "fas fa-power-off",
        "fas fa-print",
        "fas fa-puzzle-piece",
        "fas fa-qrcode",
        "fas fa-question",
        "fas fa-question-circle",
        "fas fa-quote-left",
        "fas fa-quote-right",
        "fas fa-random",
        "fas fa-recycle",
        "fas fa-redo",
        "fas fa-redo-alt",
        "fas fa-registered",
        "fas fa-reply",
        "fas fa-reply-all",
        "fas fa-retweet",
        "fas fa-road",
        "fas fa-rocket",
        "fas fa-rss",
        "fas fa-rss-square",
        "fas fa-ruble-sign",
        "fas fa-rupee-sign",
        "fas fa-save",
        "fas fa-search",
        "fas fa-search-minus",
        "fas fa-search-plus",
        "fas fa-server",
        "fas fa-share",
        "fas fa-share-alt",
        "fas fa-share-alt-square",
        "fas fa-share-square",
        "fas fa-shekel-sign",
        "fas fa-shield-alt",
        "fas fa-ship",
        "fas fa-shopping-bag",
        "fas fa-shopping-basket",
        "fas fa-shopping-cart",
        "fas fa-shower",
        "fas fa-sign-in-alt",
        "fas fa-sign-language",
        "fas fa-sign-out-alt",
        "fas fa-signal",
        "fas fa-sitemap",
        "fas fa-sliders-h",
        "fas fa-smile",
        "fas fa-snowflake",
        "fas fa-sort",
        "fas fa-sort-alpha-down",
        "fas fa-sort-alpha-up",
        "fas fa-sort-amount-down",
        "fas fa-sort-amount-up",
        "fas fa-sort-down",
        "fas fa-sort-numeric-down",
        "fas fa-sort-numeric-up",
        "fas fa-sort-up",
        "fas fa-space-shuttle",
        "fas fa-spinner",
        "fas fa-square",
        "fas fa-star",
        "fas fa-star-half",
        "fas fa-step-backward",
        "fas fa-step-forward",
        "fas fa-stethoscope",
        "fas fa-sticky-note",
        "fas fa-stop",
        "fas fa-stop-circle",
        "fas fa-street-view",
        "fas fa-strikethrough",
        "fas fa-subscript",
        "fas fa-subway",
        "fas fa-suitcase",
        "fas fa-sun",
        "fas fa-superscript",
        "fas fa-sync",
        "fas fa-sync-alt",
        "fas fa-table",
        "fas fa-tablet",
        "fas fa-tablet-alt",
        "fas fa-tachometer-alt",
        "fas fa-tag",
        "fas fa-tags",
        "fas fa-tasks",
        "fas fa-taxi",
        "fas fa-terminal",
        "fas fa-text-height",
        "fas fa-text-width",
        "fas fa-th",
        "fas fa-th-large",
        "fas fa-th-list",
        "fas fa-thermometer-empty",
        "fas fa-thermometer-full",
        "fas fa-thermometer-half",
        "fas fa-thermometer-quarter",
        "fas fa-thermometer-three-quarters",
        "fas fa-thumbs-down",
        "fas fa-thumbs-up",
        "fas fa-thumbtack",
        "fas fa-ticket-alt",
        "fas fa-times",
        "fas fa-times-circle",
        "fas fa-tint",
        "fas fa-toggle-off",
        "fas fa-toggle-on",
        "fas fa-trademark",
        "fas fa-train",
        "fas fa-transgender",
        "fas fa-transgender-alt",
        "fas fa-trash",
        "fas fa-trash-alt",
        "fas fa-tree",
        "fas fa-trophy",
        "fas fa-truck",
        "fas fa-tty",
        "fas fa-tv",
        "fas fa-umbrella",
        "fas fa-underline",
        "fas fa-undo",
        "fas fa-undo-alt",
        "fas fa-universal-access",
        "fas fa-university",
        "fas fa-unlink",
        "fas fa-unlock",
        "fas fa-unlock-alt",
        "fas fa-upload",
        "fas fa-user",
        "fas fa-user-circle",
        "fas fa-user-md",
        "fas fa-user-plus",
        "fas fa-user-secret",
        "fas fa-user-times",
        "fas fa-users",
        "fas fa-utensil-spoon",
        "fas fa-utensils",
        "fas fa-venus",
        "fas fa-venus-double",
        "fas fa-venus-mars",
        "fas fa-video",
        "fas fa-volume-down",
        "fas fa-volume-off",
        "fas fa-volume-up",
        "fas fa-wheelchair",
        "fas fa-wifi",
        "fas fa-window-close",
        "fas fa-window-maximize",
        "fas fa-window-minimize",
        "fas fa-window-restore",
        "fas fa-won-sign",
        "fas fa-wrench",
        "fas fa-yen-sign",
    );

    private function includeAssets()
    {
        $doc = \JFactory::getDocument();
        $wa = $doc->getWebAssetManager();
        $wa->addInlineStyle('.nxd-icon-preview-container .card{cursor:pointer;}');
        $wa->addInlineScript('
            document.addEventListener("DOMContentLoaded", () => {
                const cardElements = document.querySelectorAll(".nxd-icon-preview-container .card");
                const cardContainers = document.querySelectorAll(".nxd-icon-preview-container .col");
                
                document.getElementById("' . $this->id . '-search").addEventListener("keyup", function() {
              
                  const search = this.value.toLowerCase();
                  for(let element of cardContainers){
                  console.log(element);
                    const value = element.dataset.value.toLowerCase();
                    const shouldShow = value.indexOf(search) > -1;
                    element.style.display = shouldShow ? "" : "none";
                  };
                });

              setActiveIcon(cardElements);
              
              for(let element of cardElements){
                element.addEventListener("click", (event) => {
                
                    const value = event.currentTarget.getAttribute("value");
                    const inputField = ' . $this->id . ';
                    const previewId = inputField.dataset.previewId;
                    const previewElement = document.getElementById(previewId);
                    previewElement.className = value;
                    inputField.value = value;
                    inputField.dispatchEvent(new Event("iconChanged"));
                  
                  cardElements.forEach((element) => {
                    element.classList.remove("bg-dark", "text-white");
                  });
                  element.classList.add("bg-dark","text-white");
                });
              }
              
              function setActiveIcon(cardElements){
                const inputField = document.getElementById("' . $this->id . '");
                const value = inputField.value;
               console.log(value);
                for(let element of cardElements){
                  if(element.getAttribute("value") === value){
                    element.classList.add("bg-dark","text-white");
                  }
                }
              }
            });
        ');
    }

    private function buildIconGridHtml()
    {
        $html = '<div class="row g-0">';
        foreach ($this->iconList as $icon) {
            $html .= '<div class="col col-2 col-sm-2 col-md-1 col-lg-1 col-xl-1 text-center" data-value="' . $icon . '" value="' . $icon . '">';
            $html .= '<div class="card text-dark bg-light m-1" data-value="' . $icon . '" value="' . $icon . '">';
            $html .= '<div class="card-body" style="min-height:56px;" title="' . $icon . '">';
            $html .= '<i class="' . $icon . ' fa-lg nxd-icon-preview" data-for-id="' . $this->id . '"></i>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
        }
        $html .= '</div>';

        return $html;
    }

    public function getLabel()
    {
        // code that returns HTML that will be shown as the label
        return parent::getLabel();
    }

    public function getInput()
    {
        $for = $this->id;
        $this->includeAssets();

        $value = strlen($this->value) ? $this->value : 'fas fa-question';

        $html = '<span class="text-muted">'.Text::_('COM_MARATHONMANAGER_ICON_PREVIEW_TEXT') . ':&nbsp;</span><i class="' . $this->value . '" id="' . $this->id . '-preview"></i>';

        $html .= '<input class="nxd-icon-search-input form-control" id="' . $for . '-search" '
            . ' data-required="false" name="' . $this->name . '-search" placeholder="' . Text::_('COM_MARATHONMANAGER_SEARCH_PLACEHOLDER') . '" />';

        $html .= '<input class="nxd-icon-value-input" hidden data-id="' . $for . '" id="' . $for . '" data-preview-id="' . $for . '-preview"'
            . ' data-required="false" name="' . $this->name . '"'
            . ' value="' . $value . '" />';


        $html .= '<div class="container nxd-icon-preview-container mt-2">';
        $html .= $this->buildIconGridHtml();
        $html .= '</div>';

        return $html;
    }
}