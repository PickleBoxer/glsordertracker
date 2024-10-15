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

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class GlsOrderTrackerCronModuleFrontController extends ModuleFrontController
{
    /** @var bool If set to true, will be redirected to authentication page */
    public $auth = false;

    /** @var bool */
    public $ajax;

    public function display()
    {
        // Create a log channel
        $log = new Logger('cron');
        $logFilePath = __DIR__ . '/../../log/log_' . date('d-M-Y') . '.log';
        $log->pushHandler(new StreamHandler($logFilePath, Logger::DEBUG));

        $this->ajax = 1;

        if (!Tools::isPHPCLI()) {
            $this->ajaxRender('Forbidden call.');
            exit;
        }

        // Additional token checks

        // ...

        $log->info('Cron execution started at ' . date('Y-m-d H:i:s'));

        $orders = $this->get('aerdigital.glsordertracker.repository.gls_order_tracker_repository')->findAllOrders();

        $changes = [];
        $cronStatus = 'ok';

        // Extract only the required fields from each order object using getters
        foreach ($orders as $orderTracker) {
            $order = new Order($orderTracker->getOrderId());

            // Check if the order status is PS_OS_SHIPPING
            if ($order->getCurrentState() != Configuration::get('PS_OS_SHIPPING')) {
                continue; // Skip this order if the status is not PS_OS_SHIPPING
            }

            $log->info('Processing order ID: ' . $order->id);

            $shippingNumber = $order->getWsShippingNumber();
            if (!$shippingNumber) {
                $log->warning('Missing shipping number - Order ID:', ['order_id' => $order->id]);
                continue; // Skip to the next iteration if shipping number is missing
            }

            // Get tracking info from GLSApiService using the shipping number
            $glsApiService = $this->get('aerdigital.glsordertracker.service.gls_api_service');
            $trackingInfo = $glsApiService->trackAndTrace($shippingNumber);

            // Extract the last entry from tracking_data
            $lastTrackingData = end($trackingInfo['tracking_data']);
            $log->info('Order ID: ' . $order->id . ' - Last tracking data:', $lastTrackingData);

            $isUpdated = false;
            $previousState = $order->getCurrentState();
            $previousTrackingStatus = $orderTracker->getTrackingStatus();

            if ($orderTracker->getCity() !== $lastTrackingData['city']) {
                $orderTracker->setCity($lastTrackingData['city']);
                $isUpdated = true;
            }
            if ($orderTracker->getTrackingStatus() !== $lastTrackingData['status']) {
                $orderTracker->setTrackingStatus($lastTrackingData['status']);
                $isUpdated = true;
            }
            if ($orderTracker->getStatusDescription() !== $lastTrackingData['status_desc']) {
                $orderTracker->setStatusDescription($lastTrackingData['status_desc']);
                $isUpdated = true;
            }

            // Update order status and ordertracket state if delivered
            if ($lastTrackingData['status'] === 'delivered') {
                $order->setCurrentState(Configuration::get('PS_OS_DELIVERED'));
                $orderTracker->setState($order->getCurrentState());
                $isUpdated = true;
            }

            // Save changes if any field was updated
            if ($isUpdated) {
                $this->get('aerdigital.glsordertracker.repository.gls_order_tracker_repository')->save($orderTracker);

                $changes[] = [
                    'orderId' => $orderTracker->getOrderId(),
                    'previousState' => $previousState,
                    'newState' => $order->getCurrentState(),
                    'previousTrackingStatus' => $previousTrackingStatus,
                    'newTrackingStatus' => $orderTracker->getTrackingStatus(),
                ];
            }
        }
        $log->info('Cron execution ended at ' . date('Y-m-d H:i:s'));

        // Return the summary of changes and cron status
        $result = [
            'cronStatus' => $cronStatus,
            'changes' => $changes,
        ];

        // Render all orders as pretty-printed JSON
        $this->ajaxRender(json_encode($result, JSON_PRETTY_PRINT));
    }
}
