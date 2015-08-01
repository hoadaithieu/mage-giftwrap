<?php
//class VC_GiftWrap_Block_Adminhtml_Wrap extends VC_GiftWrap_Block_List
class VC_GiftWrap_Block_Adminhtml_Wrap extends Mage_Adminhtml_Block_Abstract
{
	public function showValidQty() {
		$error = Mage::registry('vc_giftwrap_error');
		if (is_string($error) && strlen($error) > 0) {
			Mage::throwException($error);
		}
	}

	protected function _setCollection($collection) {
		return $collection;
	}
	
	protected function _appendItem($group, $item) {
		return $group;
	}
	
    public function getOrder()
    {
		$order = parent::getOrder();
		if ($order) {
			return $order;
		}
        return Mage::registry('current_order');
    }
	
	public function getImagePath($image) {
		return $this->helper('vc_giftwrap')->getImagePath().'/'.$image;
	}
	
	public function getImage($image) {
		return $this->helper('vc_giftwrap')->getImageUrl().'/'.$image;
	}

	public function getList() {
		//$storeId = Mage::app()->getStore()->getId();
		$order = $this->getOrder();
		$collection = Mage::getModel('vc_giftwrap/wrap')->getCollection()
		->addFieldToFilter('main_table.order_id', $order->getId());
		
		$collection->getSelect()
		->joinInner(
			array('box' => Mage::getSingleton('core/resource')->getTableName('vc_giftwrap/box')),
			'main_table.box_id = box.box_id',
			array('box.image AS box_image', 'box.price AS box_price', 'box.title AS box_title')
		)
		
		->joinInner(
			array('product' => Mage::getSingleton('core/resource')->getTableName('catalog/product')),
			'main_table.product_id = product.entity_id',null
			//array('product.sku AS sku')
		)
		->joinLeft(
			array('card' => Mage::getSingleton('core/resource')->getTableName('vc_giftwrap/card')),
			'main_table.card_id = card.card_id',
			array('card.image AS card_image', 'card.price AS card_price', 'card.title AS card_title')
		)
		->order('group_id ASC')->order('main_table.id ASC')
		;
		
		return $this->_setCollection($collection);
	}
	
	public function processGroup($collection) {
		$items = null;
		if ($collection && $collection->getSize() > 0) {
			$items = new Varien_Data_Collection();
			//$items->clear();
			$_groupId = 0;
			$_item = null;
			$_groupItem = array();
			$_groupQty = array();
			foreach ($collection as $item) {
				$product = Mage::getModel('catalog/product')->load($item->getProductId());
				
				$_groupItem = array('product_name' => $product->getName(), 
				'product_id' => $item->getProductId(),
				'sku' => $item->getSku(),
				'product_qty' => $item->getQty(),
				'product_qty_invoiced' => $item->getQtyInvoiced(),
				'product_qty_shipped' => $item->getQtyShipped(),
				'product_qty_refunded' => $item->getQtyRefunded(),
				'price' => $item->getPrice(),
				'wrap_id' => $item->getId());
			
				$_groupItem = $this->_appendItem($_groupItem, $item);
				
				if ($_groupId != $item->getGroupId()) {
					$_item = $item;
					
					
					$_groupId = $item->getGroupId();
					$_item->setGroupItem(array($_groupItem));
					
					$items->addItem($_item);
				} else {
					$_groupItem = $_item->getGroupItem();
					$_groupItem[] = $_groupItem;
					
					$_item->setGroupItem($_groupItem);
				}
				
			}
		}
		return $items;
	}
}