<?php
class VC_GiftWrap_Block_List extends Mage_Core_Block_Template
{

	public function getBoxList() {
		$storeId = Mage::app()->getStore()->getId();
		$collection = Mage::getModel('vc_giftwrap/box')->getCollection();
		$collection->getSelect()->joinInner(array('box_store' => Mage::getSingleton('core/resource')->getTableName('vc_giftwrap/box_store')),
		'main_table.box_id = box_store.box_id AND (box_store.store_id ='.$storeId.' OR box_store.store_id = 0)',null)
		->group('main_table.box_id')
		;
		return $collection;
	}
	
	public function getCardList() {
		$storeId = Mage::app()->getStore()->getId();
		$collection = Mage::getModel('vc_giftwrap/card')->getCollection();
		$collection->getSelect()->joinInner(array('card_store' => Mage::getSingleton('core/resource')->getTableName('vc_giftwrap/card_store')),
		'main_table.card_id = card_store.card_id AND (card_store.store_id = '.$storeId.' OR card_store.store_id = 0)', null)
		->group('main_table.card_id')
		;
		return $collection;
	}
	
	public function getGroupList() {
		$quote = Mage::getModel('checkout/cart')->getQuote();
		if ($quote && $quote->getId() > 0) {
			$storeId = Mage::app()->getStore()->getId();
			$collection = Mage::getModel('vc_giftwrap/wrap')->getCollection()
			->addFieldToFilter('quote_id', $quote->getId());
			
			$collection->getSelect()
			->joinInner(
				array('box' => Mage::getSingleton('core/resource')->getTableName('vc_giftwrap/box')),
				'main_table.box_id = box.box_id',
				array('box.image AS box_image', 'box.price AS box_price', 'box.title AS box_title')
			)
			
			->joinInner(
				array('product' => Mage::getSingleton('core/resource')->getTableName('catalog/product')),
				'main_table.product_id = product.entity_id',
				array('product.sku AS sku')
			)
			->joinLeft(
				array('card' => Mage::getSingleton('core/resource')->getTableName('vc_giftwrap/card')),
				'main_table.card_id = card.card_id',
				array('card.image AS card_image', 'card.price AS card_price', 'card.title AS card_title')
			)
			->order('group_id ASC')->order('main_table.id ASC')
			->group('group_id')
			;
		
			return $collection;
		
		}
		return null;
	}
	
	public function getImage($image) {
		return $this->helper('vc_giftwrap')->getImageUrl().'/'.$image;
	}
}