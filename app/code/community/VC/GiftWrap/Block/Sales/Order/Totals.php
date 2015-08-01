<?php
class VC_GiftWrap_Block_Sales_Order_Totals extends Mage_Sales_Block_Order_Totals
{
    protected function _initTotals()
    {
		parent::_initTotals();
		if (Mage::getStoreConfig('vc_giftwrap/general/enable')) {
			$price = Mage::helper('vc_giftwrap')->getAmountByOrderId($this->getOrder()->getId());
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