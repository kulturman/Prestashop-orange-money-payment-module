<?php

/**
 *
 * @author kulturman
 */
class OmOrderTransaction extends ObjectModel {
    public $id_order;
    public $id_transaction;
    public $payment_method;

    public static $definition = array(
        'table' => 'orange_money_module_order_transaction',
        'primary' => 'id',
        'multilang' => false,
        'fields' => array(
            'id_transaction' => array('type' => self::TYPE_STRING),
            'payment_method' => array('type' => self::TYPE_STRING),
            'id_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
        ),
    );
}