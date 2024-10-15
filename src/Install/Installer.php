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

use Configuration;
use Db;
use Module;

/**
 * Class responsible for modifications needed during installation/uninstallation of the module.
 */
class Installer
{
    /**
     * @var FixturesInstaller
     */
    private $fixturesInstaller;

    public function __construct(FixturesInstaller $fixturesInstaller)
    {
        $this->fixturesInstaller = $fixturesInstaller;
    }

    /**
     * Module's installation entry point.
     *
     * @param Module $module
     *
     * @return bool
     */
    public function install(Module $module): bool
    {
        if (!$this->registerHooks($module)) {
            return false;
        }

        if (!$this->installDatabase()) {
            return false;
        }

        $this->addConfigurationValues();

        $this->fixturesInstaller->install();

        return true;
    }

    /**
     * Module's uninstallation entry point.
     *
     * @return bool
     */
    public function uninstall(): bool
    {
        if (!$this->uninstallDatabase()) {
            return false;
        }

        $this->removeConfigurationValues();

        return true;
    }

    /**
     * Install the database modifications required for this module.
     *
     * @return bool
     */
    private function installDatabase(): bool
    {
        $queries = [
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'gls_order_tracker` (
              `id_tracker` int(11) NOT NULL AUTO_INCREMENT,
              `id_order` int(11) NOT NULL,
              `state` int(11) NOT NULL,
              `tracking_status` varchar(64),
              `status_description` text,
              `city` varchar(128),
              `datetime_creation` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `datetime_tracking` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id_tracker`),
              UNIQUE KEY (`id_order`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',
        ];

        return $this->executeQueries($queries);
    }

    /**
     * Uninstall database modifications.
     *
     * @return bool
     */
    private function uninstallDatabase(): bool
    {
        $queries = [
            'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'gls_order_tracker`',
        ];

        return $this->executeQueries($queries);
    }

    /**
     * Register hooks for the module.
     *
     * @param Module $module
     *
     * @return bool
     */
    private function registerHooks(Module $module): bool
    {
        // Hooks available in the order view page.
        $hooks = [
            'actionOrderStatusPostUpdate',
            'displayAdminOrderSide',
            'displayOrderDetail',
        ];

        return (bool) $module->registerHook($hooks);
    }

    /**
     * Adds configuration values for GLS Order Tracker.
     *
     * @return void
     */
    private function addConfigurationValues()
    {
        Configuration::updateValue('GLSORDERTRACKER_LIVE_MODE', true);
        Configuration::updateValue('GLSORDERTRACKER_SEDE', 'XX');
        Configuration::updateValue('GLSORDERTRACKER_CODICE_CONTRATO', 1234);
    }

    /**
     * Removes configuration values for GLS Order Tracker.
     *
     * @return void
     */
    private function removeConfigurationValues()
    {
        Configuration::deleteByName('GLSORDERTRACKER_LIVE_MODE');
        Configuration::deleteByName('GLSORDERTRACKER_SEDE');
        Configuration::deleteByName('GLSORDERTRACKER_CODICE_CONTRATO');
    }

    /**
     * A helper that executes multiple database queries.
     *
     * @param array $queries
     *
     * @return bool
     */
    private function executeQueries(array $queries): bool
    {
        foreach ($queries as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }

        return true;
    }
}
