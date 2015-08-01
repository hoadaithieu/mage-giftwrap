<?php
class VC_GiftWrap_Block_Adminhtml_Sales_Order_Creditmemo_Totals extends Mage_Adminhtml_Block_Sales_Order_Creditmemo_Totals
{
    protected function _initTotals()
    {
		parent::_initTotals();
		if (Mage::getStoreConfig('vc_giftwrap/general/enable')) {
			$price = Mage::helper('vc_giftwrap')->getAmountCreditmemoByOrderId($this->getOrder()->getId());
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