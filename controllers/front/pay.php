<?php

class orangemoneypaymentpayModuleFrontController extends ModuleFrontController {
    public function initContent() {
        parent::initContent();
        $cart = $this->context->cart;
        $productsCount = $cart->nbProducts();
        $amountToPay = $cart->getOrderTotal();
        $formattedAmount = $cart->getTotalCart($cart->id);
        if ($productsCount !== 0) {
            $this->context->smarty->assign(array(
                'amountToPay' => $amountToPay,
                'productsCount' => $productsCount,
                'formattedAmount' => $formattedAmount,
            ));
            return $this->setTemplate('module:orangemoneypayment/views/templates/front/transaction.tpl');
        }

        Tools::redirect('index.php');
    }

    public function getBreadcrumbLinks() {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = [
            'title' => $this->getTranslator()->trans('Votre commande', [], 'Breadcrumb'),
            'url' => ''
        ];

        $breadcrumb['links'][] = [
            'title' => $this->getTranslator()->trans('Paiement par Orange money', [], 'Breadcrumb'),
            'url' => ''
        ];

        return $breadcrumb;
    }

    public function setMedia() {
        parent::setMedia();
        $css = 'modules/orangemoneypayment/views/css/HoldOn.min.css';
        $js = [
            'modules/orangemoneypayment/views/js/om.js',
            'modules/orangemoneypayment/views/js/HoldOn.min.js',
            'modules/orangemoneypayment/views/js/sweetalert2.all.min.js',
        ];

        foreach ($js as $js_uri) {
            $this->registerJavascript(sha1($js_uri), $js_uri, array('position' => 'bottom', 'priority' => 80));
        }
        $this->registerStyleSheet(sha1($css), $css);
    }

}
