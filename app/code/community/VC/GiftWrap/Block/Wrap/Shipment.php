<?php
class VC_GiftWrap_Block_Wrap_Shipment extends VC_GiftWrap_Block_Wrap
{
    public function getShipmentId()
    {
		$id = parent::getShipmentId();
		if ($id > 0) {
			return $id;
		}
		
		$id = $this->getRequest()->getParam('shipment_id', 0);
        return $id;
    }
	

	protected function _setCollection($collection) {
		$collection->getSelect()->joinInner(array('isr' => Mage::getSingleton('core/resource')->getTableName('vc_giftwrap/isr')), 
		'main_table.id = isr.wrap_id AND isr.entity_id = '.$this->getShipmentId().' AND isr.code ="'.VC_GiftWrap_Model_Isr::SHIPMENT.'"', array('isr.qty AS isr_qty'));
		
		return $collection;
	}

	protected function _appendItem($group, $item) {
		$group['product_qty'] = $item->getIsrQty();
		return $group;
	}

}