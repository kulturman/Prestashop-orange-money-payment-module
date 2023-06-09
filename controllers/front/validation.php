<?php
require_once __DIR__.'/../../util.php';
require_once __DIR__.'/../../models/OmOrderTransaction.php';

class OrangemoneypaymentValidationModuleFrontController extends ModuleFrontController
{
    /**
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        $cart = $this->context->cart;
        if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        // Check that this payment option is still available in case the customer changed his address just before the end of the checkout process
        $authorized = false;
        foreach (Module::getPaymentModules() as $module) {
            if ($module['name'] == 'orangemoneypayment') {
                $authorized = true;
                break;
            }
        }

        if (!$authorized) {
            die($this->module->l('Méthode de paiement non disponible.', 'validation'));
        }

        $this->context->smarty->assign([
            'params' => $_REQUEST,
        ]);

        $customer = new Customer($cart->id_customer);

        if (!Validate::isLoadedObject($customer)) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        $otp = isset($_REQUEST['otp']) ? $_REQUEST['otp'] : '';
        $phoneNumber = isset($_REQUEST['phone_number']) ? $_REQUEST['phone_number'] : '';
        $total = $cart->getOrderTotal(true, Cart::BOTH);
        $result = sendOrangeMoneyPayment($phoneNumber, $total, $otp);

        if($result->status == '200') {
            $currency = $this->context->currency;
            $this
                ->module
                ->validateOrder(
                        $cart->id, 2 , $total, 'Orange money', NULL,
                        [] , (int)$currency->id, false, $customer->secure_key
                );

            $transaction =  new OmOrderTransaction();
            $transaction->id_transaction = $result->transID;
            $transaction->payment_method = 'Orange money';
            $transaction->id_order = $this->module->currentOrder;
            $transaction->add();

            die(json_encode([
                'success' => true,
                'url'     => 'index.php?controller=order-confirmation&id_cart='.$cart->id.'&id_module='.$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$customer->secure_key
            ]));
        }

        else {
            die(json_encode([
                'success' => false,
                'message' => $this->translateErrors($result),
                'url'     => $this->context->link->getModuleLink('orangemoneypayment', 'pay')
            ]));
        }
        
    }

    private function translateErrors($result) {
        switch($result->status) {
            case '08':
                return $this->module->l('Le montant ne correspond pas à la somme à payer');
            case '990422':
            case '00066':
                return $this->module->l('Le numéro est invalide, vérifiez qu\'il est lié à un compte Orange Money', 'validation');
            case '990418':
                return $this->module->l('Le code OTP a déjà été utilisé', 'validation');
            default:
                return $result->message;
        }
    }
}
