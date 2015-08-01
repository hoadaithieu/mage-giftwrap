<?php
class VC_GiftWrap_Model_Wrap extends Mage_Core_Model_Abstract
{
    public function _construct() {
        parent::_construct();
        $this->_init('vc_giftwrap/wrap', 'id');
    }
	
	/*
	public function getQuantityByQuoteItemId($quoteItemId = 0) {
		return $this->_getResource()->getQuantityByQuoteItemId($this, $quoteItemId);
	}
	*/
	public function getQuantityByQuoteItemId() {
		return $this->_getResource()->getQuantityByQuoteItemId($this, $this->getQuoteItemId());
	}
	
}
