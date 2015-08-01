<?php
class VC_GiftWrap_Model_Sales_Order_Invoice_Total_Subtotal extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
	protected function _giftWrapAmount($order) {
		return Mage::helper('vc_giftwrap')->getAmountInvoiceByOrderId($order->getId());
	}
	
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
		$amount = $this->_giftWrapAmount($invoice->getOrder());
		$invoice->setGiftWrapAmount($amount);
        $invoice->setGrandTotal($invoice->getGrandTotal() + $amount);
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $amount);
		
        return $this;
    }
}
