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

use AerDigital\GlsOrderTracker\Install\InstallerFactory;
use AerDigital\GlsOrderTracker\Presenter\OrderTrackerPresenter;
use AerDigital\GlsOrderTracker\Repository\GlsOrderTrackerRepository;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;

if (!defined('_PS_VERSION_')) {
    exit;
}

// need it because InstallerFactory is not autoloaded during the install
require_once __DIR__ . '/vendor/autoload.php';

class GlsOrderTracker extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'glsordertracker';
        $this->tab = 'shipping_logistics';
        $this->version = '1.0.0';
        $this->author = 'AerDigital';
        $this->need_instance = 0;

        /*
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('GLS Order Tracker');
        $this->description = $this->l('This PrestaShop module integrates GLS tracking into your store.');

        $this->ps_versions_compliancy = ['min' => '1.7.7.0', 'max' => _PS_VERSION_];
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        $installer = InstallerFactory::create();

        return $installer->install($this);
    }

    public function uninstall()
    {
        $installer = InstallerFactory::create();

        return $installer->uninstall() && parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /*
         * If values have been submitted in the form, process.
         */
        if (((bool) Tools::isSubmit('submitGlsordertrackerModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');

        return $output . $this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitGlsordertrackerModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$this->getConfigForm()]);
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->l('Live mode'),
                        'name' => 'GLSORDERTRACKER_LIVE_MODE',
                        'is_bool' => true,
                        'desc' => $this->l('Use this module in live mode'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('Enter the GLS Sede'),
                        'name' => 'GLSORDERTRACKER_SEDE',
                        'label' => $this->l('Sede'),
                    ],
                    [
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('Enter the GLS Codice Contrato'),
                        'name' => 'GLSORDERTRACKER_CODICE_CONTRATO',
                        'label' => $this->l('Codice Contrato'),
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return [
            'GLSORDERTRACKER_LIVE_MODE' => Configuration::get('GLSORDERTRACKER_LIVE_MODE'),
            'GLSORDERTRACKER_SEDE' => Configuration::get('GLSORDERTRACKER_SEDE'),
            'GLSORDERTRACKER_CODICE_CONTRATO' => Configuration::get('GLSORDERTRACKER_CODICE_CONTRATO'),
        ];
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    public function hookActionOrderStatusPostUpdate(array $params): void
    {
        // Get the new order status and order ID from the parameters
        $newOrderStatus = $params['newOrderStatus'];
        $orderId = $params['id_order'];

        if ((int) $newOrderStatus->id !== (int) Configuration::get('PS_OS_SHIPPING')) {
            return;
        }

        /** @var GlsOrderTrackerRepository $glsOrderTrackerRepository */
        $glsOrderTrackerRepository = $this->get(
            'aerdigital.glsordertracker.repository.gls_order_tracker_repository'
        );

        // Add a new order to the database
        $glsOrderTrackerRepository->addOrderTracker((int) $orderId, (int) $newOrderStatus->id);
    }

    public function hookDisplayOrderDetail($params)
    {
        /** @var GlsOrderTrackerRepository $glsOrderTrackerRepository */
        $glsOrderTrackerRepository = $this->get(
            'aerdigital.glsordertracker.repository.gls_order_tracker_repository'
        );

        /** @var OrderTrackerPresenter $orderTrackerPresenter */
        $orderTrackerPresenter = $this->get(
            'aerdigital.glsordertracker.presenter.order_tracker_presenter'
        );

        $tracker = $glsOrderTrackerRepository->findOneByOrderId($params['order']->id);

        if (!$tracker) {
            return '';
        }

        $this->context->smarty->assign([
            'tracker' => $orderTrackerPresenter->present($tracker, (int) $this->context->language->id),
            'ajaxLink' => $this->context->link->getModuleLink('glsordertracker', 'ajax', ['ajax' => true]),
        ]);

        return $this->context->smarty->fetch($this->local_path . 'views/templates/front/tracking_info.tpl');
    }

    public function hookDisplayAdminOrderSide($params)
    {
        /** @var GlsOrderTrackerRepository $glsOrderTrackerRepository */
        $glsOrderTrackerRepository = $this->get(
            'aerdigital.glsordertracker.repository.gls_order_tracker_repository'
        );

        /** @var OrderTrackerPresenter $orderTrackerPresenter */
        $orderTrackerPresenter = $this->get(
            'aerdigital.glsordertracker.presenter.order_tracker_presenter'
        );

        $tracker = $glsOrderTrackerRepository->findOneByOrderId($params['id_order']);

        if (!$tracker) {
            return '';
        }

        return $this->render('@Modules/glsordertracker/views/templates/admin/tracking_info.html.twig', [
            'tracker' => $orderTrackerPresenter->present($tracker, (int) $this->context->language->id),
            'ajaxUrl' => $this->generateAjaxControllerURI(),
        ]);
    }

    /**
     * Render a twig template.
     */
    private function render(string $template, array $params = []): string
    {
        /** @var Twig_Environment $twig */
        $twig = $this->get('twig');

        return $twig->render($template, $params);
    }

    protected function generateAjaxControllerURI()
    {
        $router = SymfonyContainer::getInstance()->get('router');

        return $router->generate('admin_ajax');
    }
}
