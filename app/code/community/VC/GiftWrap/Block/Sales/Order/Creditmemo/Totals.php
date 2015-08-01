<?php
class VC_GiftWrap_Block_Sales_Order_Creditmemo_Totals extends Mage_Sales_Block_Order_Creditmemo_Totals
{
    protected function _initTotals()
    {
		parent::_initTotals();
		if (Mage::getStoreConfig('vc_giftwrap/general/enable')) {
			$this->getRequest()->setParam('creditmemo_id', $this->getCreditmemo()->getId());
			
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