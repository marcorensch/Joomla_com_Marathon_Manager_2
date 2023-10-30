<?php
/**
 * @package     Joomla.Administrator
 *              Joomla.Site
 * @subpackage  com_footballmanager
 * @author      Marco Rensch
 * @since        1.0.0
 *
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @copyright   Copyright (C) 2022 nx-designs NXD
 *
 */

namespace NXD\Component\MarathonManager\Site\Field;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Form\Field\NumberField;
use Joomla\CMS\Form\Field\TextField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;
use Joomla\CMS\Form\FormHelper;

FormHelper::loadFieldClass('list');

defined('_JEXEC') or die;

class MapsCountField extends NumberField
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  1.0.0
     */
    protected $type = 'MapsCount';

    /**
     * The ID of the selected map option in the event settings
     *
     * @var int
     * @since 1.0.0
     */
    protected $mapOptionId;

    /**
     * The data of the selected map option in the event settings
     *
     * @var Object
     * @since 1.0.0
     */
    protected $mapOptionData;

    /**
     * The value of the field
     *
     * @var int
     * @since 1.0.0
     */
    protected $value;

    /**
     * The minimum value of the field
     *
     * @var int
     * @since 1.0.0
     */
    protected $min;

    /**
     * The maximum value of the field
     *
     * @var int
     * @since 1.0.0
     */
    protected $max;

    /**
     * The visibility status of the number field
     *
     * @var boolean
     * @since 1.0.0
     */
    protected $hidden;

    /**
     * The price per map
     * @var float
     *
     * @since   1.0.0
     */
    protected float $price_per_map = 0.0;
    private int $count_of_free_maps;

    public function getInput(): string
    {
        $this->mapOptionId = $this->getSelectedMapOptionIdFromEvent();
        if($this->mapOptionId) {
            $this->mapOptionData = $this->getMapOptionData();

            // Set the field options.
            $this->value = $this->mapOptionData->count_of_free_maps;
            $this->min = $this->mapOptionData->count_of_free_maps;
            $this->count_of_free_maps = $this->mapOptionData->count_of_free_maps;
            if($this->mapOptionData->additional_maps_possible) {
                $this->max = $this->mapOptionData->max_maps;
                $this->hidden = false;
                $this->price_per_map = $this->mapOptionData->price_per_map;

                $this->addJavascript();
            }else{
                $this->hidden = true;
            }
        }

        return parent::getInput();
    }

    /**
     * Method to get the data to be passed to the layout for rendering.
     *
     * @return  array
     *
     * @since 3.7
     */
    protected function getLayoutData(): array
    {
        $data = parent::getLayoutData();

        // Initialize some field attributes.
        $extraData = [
            'step' => 1,
            'value' => $this->value,
        ];

        if ($this->min) $extraData['min'] = $this->min;
        $extraData['max'] = $this->max > 0 ? $this->max : '';

        if($this->price_per_map) $extraData['dataAttribute'] = 'data-map-price='.intval($this->price_per_map) . ' data-count-of-included-maps=' . $this->count_of_free_maps;
        if($this->hidden) $extraData['parentclass'] = 'uk-hidden';


        return array_merge($data, $extraData);
    }

    protected function getSelectedMapOptionIdFromEvent() :int
    {
        if($eventId = $this->form->getValue('event_id')) {
            $db = Factory::getContainer()->get(DatabaseInterface::class);
            $query = $db->getQuery(true);
            $query->select('map_option_id');
            $query->from('#__com_marathonmanager_events');
            $query->where('id = ' . $db->quote($eventId));
            $db->setQuery($query);
            $mapOptionId = $db->loadResult();

            return intval($mapOptionId);
        }

        return 0;

    }

    protected function getMapOptionData()
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $query->select("*")
            ->from('#__com_marathonmanager_maps')
            ->where('id = ' . $db->quote($this->mapOptionId));
        $db->setQuery($query);
        return $db->loadObject();
    }

    protected function addJavascript()
    {
        $mapPriceCalculatorScript = <<<JS
            document.addEventListener('DOMContentLoaded', function() {
                const inputElement = document.getElementById('jform_maps_count');
                const mapPriceElement = document.getElementById('calculated_maps_price');
                const countOfIncludedMaps = inputElement.dataset.countOfIncludedMaps;
                const price_per_map = inputElement.dataset.mapPrice;
                
                calculateMapPrice();
                
                function calculateMapPrice() {
                    const value = inputElement.value;
                    const price = (value - countOfIncludedMaps) * price_per_map;
                    mapPriceElement.innerHTML = price.toFixed(0) > 0 ? price.toFixed(0) : 0;
                }
                
                inputElement.addEventListener('change', function() {
                    calculateMapPrice();
                });
            });
        JS;

        $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
        $wa->addInlineScript( $mapPriceCalculatorScript, ['defer'=> true]);

    }
}