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

namespace AerDigital\GlsOrderTracker\DTO;

if (!defined('_PS_VERSION_')) {
    exit;
}

use DateTimeImmutable;

final class Order
{
    /**
     * @var int
     */
    private $orderId;

    /**
     * @var string
     */
    private $reference;

    /**
     * @var int
     */
    private $orderStateId;

    /**
     * @var DateTimeImmutable
     */
    private $orderDate;

    public function __construct(int $orderId, string $reference, int $orderStateId, DateTimeImmutable $orderDate)
    {
        $this->orderId = $orderId;
        $this->reference = $reference;
        $this->orderStateId = $orderStateId;
        $this->orderDate = $orderDate;
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function getOrderStateId(): int
    {
        return $this->orderStateId;
    }

    public function getOrderDate(): DateTimeImmutable
    {
        return $this->orderDate;
    }
}
