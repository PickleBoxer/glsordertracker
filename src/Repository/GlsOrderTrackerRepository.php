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

namespace AerDigital\GlsOrderTracker\Repository;

if (!defined('_PS_VERSION_')) {
    exit;
}

use AerDigital\GlsOrderTracker\Entity\GlsOrderTracker;
use Doctrine\ORM\EntityRepository;
use PrestaShopLogger;

class GlsOrderTrackerRepository extends EntityRepository
{
    /**
     * @param int $orderId
     *
     * @return object|null
     */
    public function findOneByOrderId(int $orderId)
    {
        return $this->findOneBy(['orderId' => $orderId]);
    }

    /**
     * Get all orders
     *
     * @return OrderTracker[]
     */
    public function findAllOrders(): array
    {
        return $this->findAll();
    }

    /**
     * Save the order tracker
     *
     * @param OrderTracker $orderTracker
     */
    public function save($orderTracker)
    {
        $em = $this->getEntityManager();
        $em->persist($orderTracker);
        $em->flush();
    }

    /**
     * Add a new order to the database
     *
     * @param int $orderId
     * @param int $newOrderStatus
     */
    public function addOrderTracker(int $orderId, int $newOrderStatus): void
    {
        $glsOrderTracker = new GlsOrderTracker();
        $glsOrderTracker->setOrderId($orderId);
        $glsOrderTracker->setState($newOrderStatus);
        $glsOrderTracker->setDatetimeCreation(new \DateTime());
        $glsOrderTracker->setDatetimeTracking(new \DateTime());

        $em = $this->getEntityManager();

        try {
            $em->persist($glsOrderTracker);
            $em->flush();
        } catch (\Exception $e) {
            PrestaShopLogger::addLog('Error persisting order: ' . $e->getMessage(), 3);
        }
    }
}
