<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_marathonmanager
 *
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\MarathonManager\Site\Helper;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\HelperFactory;
use Joomla\Database\DatabaseInterface;

class RegistrationHelper
{

    /**
     * @throws \Exception
     */
    public static function getPaymentInformation($eventId, $registrationDate) :object
    {
        $result = new \stdClass();
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $query->select($db->quoteName(array('earlybird_fee', 'regular_fee', 'earlybird_end_date', 'qr_bank','qr_twint','qr_bank_earlybird','qr_twint_earlybird','banking_details')));
        $query->from($db->quoteName('#__com_marathonmanager_events'));
        $query->where($db->quoteName('id') . ' = ' . $db->quote($eventId));
        $db->setQuery($query);
        $data = $db->loadObject();

        // return effective QR Code's based on the registration date
        $registrationDate = new \DateTime($registrationDate);
        if($data->earlybird_end_date){
            $earlybirdEndDate = new \DateTime($data->earlybird_end_date);
            $result->qr_bank = $registrationDate < $earlybirdEndDate ? $data->qr_bank_earlybird : $data->qr_bank;
            $result->qr_twint = $registrationDate < $earlybirdEndDate ? $data->qr_twint_earlybird : $data->qr_twint;
        }else{
            $result->qr_bank = trim($data->qr_bank);
            $result->qr_twint = trim($data->qr_twint);
        }

		// Handle the QR Code's
	    $result->qr_bank = $result->qr_bank && $result->qr_bank !== "/" ? $result->qr_bank : null;
		$result->qr_twint = $result->qr_twint && $result->qr_twint !=="/" ? $result->qr_twint : null;

        $result->bankingInformation = self::mergeBankingInformation($data->banking_details);

        return $result;

    }

    private static function mergeBankingInformation($bankingDetailsFromEvent): \stdClass
    {
        $bankingDetailsFromEvent = json_decode($bankingDetailsFromEvent, true);
        $app = Factory::getApplication();
        $params = $app->getParams();
        $mergedBankingDetails = new \stdClass();
        // Get the banking information from the params if not set in the event
        foreach ($bankingDetailsFromEvent as $key => $value) {
            if(empty($value) && isset($params->get('banking_details')->$key)){
                $mergedBankingDetails->$key = $params->get('banking_details')->$key;
            }else{
                $mergedBankingDetails->$key = $value;
            }
        }

        return $mergedBankingDetails;

    }

    /**
     * @throws \Exception
     */
    public static function calculateRegistrationFee($eventId, $mapsCount)
    {
        $eventData = RegistrationHelper::getEventData($eventId);
        if (!$eventData) {
            return 0;
        }

        if (isset($eventData['map_option_id'])) {
            $mapOptionData = RegistrationHelper::getMapOptionData($eventData['map_option_id']);
            // Calculate the fee (calculated event fee + ((count of maps - count of free maps) * map price)
            // If no additional maps are possible all maps are already included into the event fee OR no maps available at all
            if($mapOptionData['additional_maps_possible']){
                $mapsPrice = ($mapsCount - $mapOptionData['count_of_free_maps']) * $mapOptionData['price_per_map'];
            }else{
                $mapsPrice = 0;
            }
            return $eventData['calculated_event_fee'] + $mapsPrice ;
        }

        return $eventData['calculated_event_fee'];
    }

    /**
     * @description     Get the event data for the given event id returns an array with the following keys:
     *                  earlybird_fee, regular_fee, earlybird_end_date, map_option_id aswell as the
     *                  calculated_event_fee based on the current date / check if earlybird is given &  is still active
     *
     * @param           $eventId
     * @throws          \Exception
     * @return          array
     * @since           1.0.0
     */
    private static function getEventData($eventId): array
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $query->select($db->quoteName(array('earlybird_fee', 'regular_fee', 'earlybird_end_date', 'map_option_id')));
        $query->from($db->quoteName('#__com_marathonmanager_events'));
        $query->where($db->quoteName('id') . ' = ' . $db->quote($eventId));
        $db->setQuery($query);
        $data = $db->loadAssoc();

        if(isset($data['earlybird_end_date']) && isset($data['earlybird_fee'])){
            $now = new \DateTime();
            $data['calculated_event_fee'] = $now < new \DateTime($data['earlybird_end_date']) ? $data['earlybird_fee'] : $data['regular_fee'];
        }

        return $data;
    }

    private static function getMapOptionData($mapOptionId):array
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $query->select("*");
        $query->from($db->quoteName('#__com_marathonmanager_maps'));
        $query->where($db->quoteName('id') . ' = ' . $db->quote($mapOptionId));
        $db->setQuery($query);
        return $db->loadAssoc();
    }

    public static function getCourse($course_id)
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $query->select('a.*')
            ->from($db->quoteName('#__com_marathonmanager_courses', 'a'))
            ->where($db->quoteName('a.id') . ' = ' . $db->quote($course_id));
        $db->setQuery($query);
        return $db->loadObject();
    }

    public static function getGroup($group_id)
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $query->select('a.*')
            ->from($db->quoteName('#__com_marathonmanager_groups', 'a'))
            ->where($db->quoteName('a.id') . ' = ' . $db->quote($group_id));
        $db->setQuery($query);
        return $db->loadObject();
    }

}