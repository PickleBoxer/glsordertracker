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

namespace AerDigital\GlsOrderTracker\Entity;

if (!defined('_PS_VERSION_')) {
    exit;
}

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AerDigital\GlsOrderTracker\Repository\GlsOrderTrackerRepository")
 */
class GlsOrderTracker
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id_tracker", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $trackerId;

    /**
     * @ORM\Column(name="id_order", type="integer", unique=true)
     */
    private int $orderId;

    /**
     * @ORM\Column(name="state", type="integer")
     */
    private int $state;

    /**
     * @ORM\Column(name="tracking_status", type="string", length=64, nullable=true)
     */
    private ?string $tracking_status;

    /**
     * @ORM\Column(name="status_description", type="text", nullable=true)
     */
    private ?string $status_description;

    /**
     * @ORM\Column(name="city", type="string", length=128, nullable=true)
     */
    private ?string $city;

    /**
     * @ORM\Column(name="datetime_creation", type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private \DateTime $datetime_creation;

    /**
     * @ORM\Column(name="datetime_tracking", type="datetime", options={"default": "CURRENT_TIMESTAMP", "onUpdate": "CURRENT_TIMESTAMP"})
     */
    private \DateTime $datetime_tracking;

    // Getters and Setters

    public function getTrackerId(): int
    {
        return $this->trackerId;
    }

    public function setTrackerId(int $trackerId): void
    {
        $this->trackerId = $trackerId;
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function setOrderId(int $orderId): void
    {
        $this->orderId = $orderId;
    }

    public function getState(): int
    {
        return $this->state;
    }

    public function setState(int $state): void
    {
        $this->state = $state;
    }

    public function getTrackingStatus(): ?string
    {
        return $this->tracking_status;
    }

    public function setTrackingStatus(?string $tracking_status): void
    {
        $this->tracking_status = $tracking_status;
    }

    public function getStatusDescription(): ?string
    {
        return $this->status_description;
    }

    public function setStatusDescription(?string $status_description): void
    {
        $this->status_description = $status_description;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    public function getDatetimeCreation(): \DateTime
    {
        return $this->datetime_creation;
    }

    public function setDatetimeCreation(\DateTime $datetime_creation): void
    {
        $this->datetime_creation = $datetime_creation;
    }

    public function getDatetimeTracking(): \DateTime
    {
        return $this->datetime_tracking;
    }

    public function setDatetimeTracking(\DateTime $datetime_tracking): void
    {
        $this->datetime_tracking = $datetime_tracking;
    }
}
