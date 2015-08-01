<?php
class VC_GiftWrap_Model_Sales_Quote_Address_Total_Subtotal extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    public function __construct(){
        $this->setCode('giftwrap');
    }
		
	protected function _giftWrapAmount() {
		$quote = Mage::getModel('checkout/cart')->getQuote();
		return Mage::helper('vc_giftwrap')->getAmountByQuoteId($quote->getId());
	}
	
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
		if ($address->getAddressType() == Mage_Sales_Model_Quote_Address::TYPE_BILLING) return;
        $quote = $address->getQuote();
		$amount = $this->_giftWrapAmount();
		$address->setAmount($amount);
        $address->setGrandTotal($address->getGrandTotal() + $amount);
        $address->setBaseGrandTotal($address->getBaseGrandTotal() + $amount);
		
        return $this;
    }

    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
		//file_put_contents('test1.log', file_get_contents('test1.log') . '->'.$address->getAmount());
		$amount = $address->getAmount();
		if ($address->getAddressType() == Mage_Sales_Model_Quote_Address::TYPE_BILLING) return;
        if ($amount > 0) {
            $title = Mage::helper('vc_giftwrap')->__('Gift Wrap');
            $address->addTotal(array(
                'code' => $this->getCode(),
                'title' => $title,
                'value' => $amount
            ));
        }
		
        return $this;
    }

}
