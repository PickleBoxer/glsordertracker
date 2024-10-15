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
if (!defined('_PS_VERSION_')) {
    exit;
}

class GlsOrderTrackerAjaxModuleFrontController extends ModuleFrontController
{
    /** @var bool */
    public $ajax;

    public function displayAjax()
    {
        $this->ajax = 1;
        $action = Tools::getValue('action');
        $response = [];

        $actionsMap = [
            'trackOrder' => 'handleTrackOrder',
            // Add more actions here
        ];

        if (isset($actionsMap[$action])) {
            $response = $this->{$actionsMap[$action]}();
        } else {
            $response = [
                'success' => false,
                'message' => 'Invalid action',
            ];
        }

        $this->ajaxRender(json_encode($response));
    }

    private function handleTrackOrder(): array
    {
        $shippingNumber = Tools::getValue('shippingNumber');
        if (!$shippingNumber) {
            return [
                'success' => false,
                'message' => 'Missing shipping number',
            ];
        }

        return $this->trackOrder($shippingNumber);
    }

    private function trackOrder(string $shippingNumber): array
    {
        $apiService = $this->get('aerdigital.glsordertracker.service.gls_api_service');
        $trackingInfo = $apiService->trackAndTrace($shippingNumber);

        if (!$trackingInfo['success']) {
            return [
                'success' => false,
                'message' => '<div class="alert alert-danger">Error fetching tracking data: ' . htmlspecialchars($trackingInfo['error']) . '</div>',
            ];
        }

        $trackingData = $trackingInfo['tracking_data'];
        $lastTracking = end($trackingData);
        $city = !empty($lastTracking['destination_address']) ? htmlspecialchars($lastTracking['destination_address']) : htmlspecialchars($lastTracking['city']);
        $coordinates = $apiService->getCoordinates($city);

        return [
            'success' => true,
            'trackingData' => $trackingData,
            'lastTracking' => $lastTracking,
            'city' => $city,
            'coordinates' => $coordinates,
        ];
    }
}
