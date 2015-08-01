<?php
class VC_GiftWrap_Block_Sales_Order_Invoice_Totals extends Mage_Sales_Block_Order_Invoice_Totals
{
    protected function _initTotals()
    {
		parent::_initTotals();
		if (Mage::getStoreConfig('vc_giftwrap/general/enable')) {
			$this->getRequest()->setParam('invoice_id', $this->getInvoice()->getId());
			
			$price = Mage::helper('vc_giftwrap')->getAmountInvoiceByOrderId($this->getOrder()->getId());
			$giftwrap = new Varien_Object(array(
				'code'      => 'giftcard',
				'value'     => $price,
				'base_value'=> $price,
				'label'     => $this->helper('vc_giftwrap')->__('Gift Wrap'),
			));
			
			$this->addTotalBefore($giftwrap, 'grand_total');
		}
		return $this;
	}
}