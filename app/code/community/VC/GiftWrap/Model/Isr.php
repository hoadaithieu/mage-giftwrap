<?php
class VC_GiftWrap_Model_Isr extends Mage_Core_Model_Abstract
{
	const INVOICE = 'invoice';
	const SHIPMENT = 'shipment';
	const REFUND = 'refund';
	
    public function _construct() {
        parent::_construct();
        $this->_init('vc_giftwrap/isr', null);
    }
}
