<?php
//class VC_GiftWrap_Block_Adminhtml_Wrap extends VC_GiftWrap_Block_List
class VC_GiftWrap_Block_Adminhtml_Wrap_Invoice extends VC_GiftWrap_Block_Adminhtml_Wrap
{
    public function getInvoiceId()
    {
		$id = parent::getInvoiceId();
		if ($id > 0) {
			return $id;
		}
		
		$id = $this->getRequest()->getParam('invoice_id', 0);
        return $id;
    }
	

	protected function _setCollection($collection) {
		$collection->getSelect()->joinInner(array('isr' => Mage::getSingleton('core/resource')->getTableName('vc_giftwrap/isr')), 
		'main_table.id = isr.wrap_id AND isr.entity_id = '.$this->getInvoiceId().' AND isr.code ="'.VC_GiftWrap_Model_Isr::INVOICE.'"', array('isr.qty AS isr_qty'));
		return $collection;
	}

	protected function _appendItem($group, $item) {
		$group['product_qty_invoiced'] = $item->getIsrQty();
		return $group;
	}
	
}