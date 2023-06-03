<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

if (!defined('_PS_VERSION_')) {
    exit;
}

class OrangeMoneyPayment extends PaymentModule
{
    protected $_html = '';
    protected $_postErrors = array();

    public $details;
    public $owner;
    public $address;
    public $extra_mail_vars;

    public function __construct()
    {
        $this->name = 'orangemoneypayment';
        $this->configKeys = [
            'ORANGE_MONEY_MERCHANT_ID', 'ORANGE_MONEY_MERCHANT_NUMBER',
            'ORANGE_MONEY_MERCHANT_PASSWORD', 'ORANGE_MONEY_IS_TEST_MODE'
        ];
        $this->tab = 'payments_gateways';
        $this->version = '1.0.0';
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        $this->author = 'Kulturman';
        $this->controllers = array('validation');
        $this->is_eu_compatible = 1;

        $this->currencies = true;
        $this->currencies_mode = 'checkbox';

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('OrangeMoney payment');
        $this->description = $this->l('Permet de payer par orange money');

        if (!count(Currency::checkPaymentCurrencies($this->id))) {
            $this->warning = $this->l('Aucune monnaie pour ce module.');
        }
    }
    public function getContent()
    {
        if (Tools::isSubmit('submit'.$this->name)) {
            foreach($this->configKeys as $key) {
                $value = Tools::getValue($key);
                Configuration::updateValue($key, $value);
            }
            $output = $this->displayConfirmation($this->l('Paramètres mis à jour avec succès'));
        }
    
        return $output.$this->displayForm();
    }
    public function displayForm() {
        $testModeOptions = [
            ['name' => $this->l('Oui'), 'value' => true],
            ['name' => $this->l('Non'), 'value' => false],
        ];
        $fieldsForm[0]['form'] = [
            'legend' => [
                'title' => $this->l('Paramètres'),
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('Identifiant marchand'),
                    'name' => 'ORANGE_MONEY_MERCHANT_ID',
                    'required' => true
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Numéro de téléphone marchand'),
                    'name' => 'ORANGE_MONEY_MERCHANT_NUMBER',
                    'required' => true
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Mot de passe API'),
                    'name' => 'ORANGE_MONEY_MERCHANT_PASSWORD',
                    'required' => true
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Mode test'),
                    'name' => 'ORANGE_MONEY_IS_TEST_MODE',
                    'required' => true,
                    'options' => [
                        'query' => $testModeOptions,
                        'id' => 'value',
                        'name' => 'name'
                    ]
                ]
            ],
            'submit' => [
                'title' => $this->l('Enregistrer'),
                'class' => 'btn btn-default pull-right'
            ]
        ];

        $helper = new HelperForm;
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        $helper->submit_action = 'submit'.$this->name;
        foreach($this->configKeys as $key) {
            $helper->fields_value[$key] = Tools::getValue($key, Configuration::get($key));
        }
        return $helper->generateForm($fieldsForm);
    }

    public function install()
    {
        if (!parent::install() || !$this->registerHook('paymentOptions') || !$this->registerHook('paymentReturn')) {
            return false;
        }
        return true;
    }

    public function hookPaymentOptions($params)
    {
        if (!$this->active) {
            return;
        }

        if (!$this->checkCurrency($params['cart'])) {
            return;
        }

        $payment_options = [
            $this->getOrangeMoneyPaymentOption(),
        ];

        return $payment_options;
    }

    public function checkCurrency($cart)
    {
        $currency_order = new Currency($cart->id_currency);
        $currencies_module = $this->getCurrency($cart->id_currency);

        if (is_array($currencies_module)) {
            foreach ($currencies_module as $currency_module) {
                if ($currency_order->id == $currency_module['id_currency']) {
                    return true;
                }
            }
        }
        return false;
    }

    public function getOrangeMoneyPaymentOption()
    {
        $offlineOption = new PaymentOption();
        $offlineOption->setCallToActionText($this->l('Orange money'))
                      ->setAction($this->context->link->getModuleLink($this->name, 'pay', array(), true))
                      ->setLogo(Media::getMediaPath(_PS_MODULE_DIR_.$this->name.'/orangemoneypayment.png'));
        return $offlineOption;
    }

}
