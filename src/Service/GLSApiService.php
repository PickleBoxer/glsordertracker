<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

declare(strict_types=1);

namespace AerDigital\GlsOrderTracker\Service;

if (!defined('_PS_VERSION_')) {
    exit;
}

use Configuration;
use DateTime;
use DOMDocument;
use Exception;
use Tools;

class GLSApiService implements GLSApiServiceInterface
{
    private $sede;
    private $codiceContrato;

    public function __construct()
    {
        //$this->glsAccount = $glsAccount;
        $this->sede = Configuration::get('GLSORDERTRACKER_SEDE');
        $this->codiceContrato = Configuration::get('GLSORDERTRACKER_CODICE_CONTRATO');
    }

    public function trackAndTrace(string $shippingNumber): array
    {
        // Remove the 'UF' prefix from the shipping number if it exists
        $cleanShippingNumber = preg_replace('/^UF/', '', $shippingNumber);

        // Default values
        $success = false;
        $codValue = 0;
        $trackingData = [];
        $trackingNumber = '';
        $boxes = 0;
        $status = '';

        // Status code mapping
        $statusMap = [
            1 => 'undelivered',       // Recipient is not at door, delivery scheduled for next business day
            4 => 'undelivered',       // Rejected by the recipient. Waiting for instructions from the sender for possible redelivery
            5 => 'undelivered',
            8 => 'undelivered',        // Recipient closed at this shift, delivery expected next working day
            10 => 'undelivered',      // Available for collection at the recipient site - to come to FERMO DEPOSITO
            12 => 'undelivered',      // Recipient want pickup in GLS depot/headquarter
            14 => 'out_for_delivery', // Delivery agreed with the recipient
            17 => 'out_for_delivery', // Delivery scheduled for next business day
            22 => 'depot',
            25 => 'transit',
            26 => 'transit',
            27 => 'transit',
            28 => 'out_for_delivery', // Delivery scheduled for next business day
            29 => 'exception',
            35 => 'undelivered', // handeled to local carrier, delivery expected in next 3 working days
            66 => 'undelivered',
            67 => 'undelivered', // Recipient closed, delivery in next work day
            68 => 'undelivered', // wrong address, waiting for Sender for instructions
            77 => 'undelivered', // delivery failed, delivery expected next working day
            901 => 'info_received',
            902 => 'transit',
            903 => 'transit',
            904 => 'depot',
            905 => 'out_for_delivery',
            906 => 'delivered',
            907 => 'out_for_delivery', // FERMO DEPOSITO
            908 => 'depot',
        ];

        $request = curl_init('https://infoweb.gls-italy.com/XML/get_xml_track.php?locpartenza=' . $this->sede . '&NumSped=' . trim($cleanShippingNumber) . '&CodCli=' . $this->codiceContrato);
        curl_setopt($request, CURLOPT_TIMEOUT, 30);
        curl_setopt($request, CURLOPT_HEADER, 0);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($request, CURLOPT_POSTFIELDS, null);
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, false);
        $postResponse = curl_exec($request);
        $errorNumber = curl_errno($request);
        curl_close($request);

        if ($errorNumber != 0) {
            return ['success' => false, 'error' => 'Curl error: ' . $errorNumber];
        }

        try {
            $dom = new DOMDocument();
            $dom->loadXML($postResponse);
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Error parsing XML response: ' . $e->getMessage()];
        }

        $index = 0;

        if ($dom->getElementsByTagName('NumSped')->length != 0) {
            $success = true;
            $trackingNumber = (string) trim($dom->getElementsByTagName('NumSped')->item(0)->nodeValue);
            $codValue = (string) trim($dom->getElementsByTagName('Contrassegno')->item(0)->nodeValue);
            $boxes = (int) trim($dom->getElementsByTagName('NumeroColli')->item(0)->nodeValue);
            $status = (string) trim($dom->getElementsByTagName('StatoSpedizione')->item(0)->nodeValue);

            $tracking = $dom->getElementsByTagName('TRACKING')->item(0);
            foreach ($tracking->childNodes as $tracking) {
                if ($tracking->nodeName == 'Data') {
                    $trackingData[$index]['date'] = DateTime::createFromFormat('d/m/y', $tracking->nodeValue)->format('Y-m-d');
                } elseif ($tracking->nodeName == 'Ora') {
                    $trackingData[$index]['time'] = $tracking->nodeValue;
                } elseif ($tracking->nodeName == 'Luogo') {
                    $trackingData[$index]['city'] = $tracking->nodeValue;
                } elseif ($tracking->nodeName == 'Stato') {
                    $trackingData[$index]['status_desc'] = $tracking->nodeValue;
                } elseif ($tracking->nodeName == 'Note') {
                    $trackingData[$index]['note'] = $tracking->nodeValue;
                } elseif ($tracking->nodeName == 'Codice') {
                    $trackingData[$index]['code'] = $tracking->nodeValue;
                    ++$index;
                }
            }
        } else {
            $errorMessage = (string) $dom->getElementsByTagName('ERRORE')->item(0)->nodeValue;

            return ['success' => false, 'error' => 'Error GLS: ' . trim($errorMessage)];
        }

        foreach ($trackingData as $key => $trackingDataTemp) {
            if (isset($statusMap[$trackingDataTemp['code']])) {
                $trackingData[$key]['status'] = $statusMap[$trackingDataTemp['code']];
                $trackingData[$key]['status_desc'] = '(C: ' . $trackingData[$key]['code'] . ') ' . $trackingData[$key]['status_desc'];

                // If status code is 906, add the new key from IndirizzoDestinazione
                if ($trackingDataTemp['code'] == 906) {
                    $indirizzoDestinazione = $dom->getElementsByTagName('IndirizzoDestinazione')->item(0)->nodeValue;
                    $trackingData[$key]['destination_address'] = $indirizzoDestinazione;
                }
            } else {
                $trackingData[$key]['status'] = '';
                $trackingData[$key]['status_desc'] = '(C: ' . $trackingData[$key]['code'] . ') ' . $trackingData[$key]['status_desc'];
            }
        }

        $codValue = str_replace('.', '', $codValue);
        $codValue = str_replace(',', '.', $codValue);

        $trackingData = array_reverse($trackingData);

        return [
            'success' => $success,
            'cod_value' => $codValue,
            'tracking_number' => $trackingNumber,
            'boxes' => $boxes,
            'status' => $status,
            'tracking_data' => $trackingData, ];
    }

    public function getCoordinates($city)
    {
        $apiKey = '1f36133f60b4441098742d4ddf4009a5'; // Replace with your Geoapify API key
        $url = 'https://api.geoapify.com/v1/geocode/search?text=' . urlencode($city) . '&apiKey=' . $apiKey;

        $response = Tools::file_get_contents($url);
        $data = json_decode($response, true);

        if (isset($data['features'][0]['geometry']['coordinates'])) {
            return $data['features'][0]['geometry']['coordinates'];
        }

        return null;
    }
}
