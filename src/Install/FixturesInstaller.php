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

namespace AerDigital\GlsOrderTracker\Install;

if (!defined('_PS_VERSION_')) {
    exit;
}

use Db;
use Order;

/**
 * Installs data fixtures for the module.
 */
class FixturesInstaller
{
    /**
     * @var Db
     */
    private $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function install(): void
    {
        $startDate = (new \DateTime())->modify('-1 week')->format('Y-m-d');
        $endDate = (new \DateTime())->format('Y-m-d');
        $orderIds = Order::getOrdersIdByDate($startDate, $endDate);

        foreach ($orderIds as $orderId) {
            $order = new Order($orderId);
            if ($order->hasBeenShipped()) {
                $this->insertTracker($orderId, $order->getCurrentState());
            }
        }
    }

    private function insertTracker(int $orderId, $state): void
    {
        $this->db->insert('gls_order_tracker', [
            'id_order' => $orderId,
            'state' => $state,
        ]);
    }
}
