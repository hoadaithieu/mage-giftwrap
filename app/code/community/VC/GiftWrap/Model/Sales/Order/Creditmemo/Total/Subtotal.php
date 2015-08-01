<?php
class VC_GiftWrap_Model_Sales_Order_Creditmemo_Total_Subtotal extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
	protected function _giftWrapAmount($order) {
		return Mage::helper('vc_giftwrap')->getAmountCreditmemoByOrderId($order->getId());
	}
	
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
		$amount = $this->_giftWrapAmount($creditmemo->getOrder());
		$creditmemo->setGiftWrapAmount($amount);
        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $amount);
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $amount);
		
        return $this;
    }
}
