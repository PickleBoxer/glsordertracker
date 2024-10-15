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

namespace AerDigital\GlsOrderTracker\Presenter;

if (!defined('_PS_VERSION_')) {
    exit;
}

use AerDigital\GlsOrderTracker\Entity\GlsOrderTracker;
use Gender;
use Order;

class OrderTrackerPresenter
{
    /**
     * @var string
     */
    //private $imgDir;

    //public function __construct(string $imgDir)
    //{
    //$this->imgDir = $imgDir;
    //}

    public function present(GlsOrderTracker $orderTracker, int $languageId): array
    {
        $order = new Order($orderTracker->getOrderId());
        $customer = $order->getCustomer();
        $gender = new Gender($customer->id_gender, $languageId);
        $shippingNumber = $order->getWsShippingNumber();
        $orderStatus = $order->getCurrentStateFull($languageId);

        return [
            'firstName' => $customer->firstname,
            'lastName' => $customer->lastname,
            'gender' => $gender->name,
            //'imagePath' => $this->imgDir.$orderTracker->getFilename()
            'shippingNumber' => $shippingNumber,
            'orderStatusName' => $orderStatus['name'],
            'orderStatusId' => $orderStatus['id_order_state'],
        ];
    }
}
