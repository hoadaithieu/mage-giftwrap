<?php
class VC_GiftWrap_WrapController extends Mage_Core_Controller_Front_Action {
 	/**
	@ method : indexAction
	**/
	
    protected function _getSession()
    {
        return Mage::getSingleton('core/session');
    }
	
	
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}
	
	public function deleteGroupAction() {
		$data = $this->getRequest()->getParams();
		try{
			if (isset($data['group_id']) && $data['group_id'] > 0) {
				$collection = Mage::getModel('vc_giftwrap/wrap')->getCollection()
				->addFieldToFilter('group_id', array('eq' => $data['group_id']));
				if ($collection && $collection->getSize() > 0) {
					foreach ($collection as $item) {
						$item->delete();
					}
				}
				$this->_getSession()->addSuccess($this->__('Delete gift card box successful.'));
			}
		} catch (Exception $e) {
			$this->_getSession()->addError($this->__('Delete gift card box error.'));
		}
		$this->_redirect('checkout/cart');
	}
	
	public function deleteItemAction() {
		$data = $this->getRequest()->getParams();
		try {
			if (isset($data['item_id']) && $data['item_id'] > 0) {
				$collection = Mage::getModel('vc_giftwrap/wrap')->getCollection()
				->addFieldToFilter('quote_item_id', array('eq' => $data['item_id']));
				
				if ($collection && $collection->getSize() > 0) {
					foreach ($collection as $item) {
						$item->delete();
					}
					$this->_getSession()->addSuccess($this->__('Delete item from gift card box successful.'));
				}
			} else if (isset($data['id']) && $data['id'] > 0) {
				$model = Mage::getModel('vc_giftwrap/wrap')->load($data['id']);
				if ($model && $model->getId() > 0) {
					$model->delete();
					$this->_getSession()->addSuccess($this->__('Delete item from gift card box successful.'));
				}
			}
		} catch (Exception $e) {
			$this->_getSession()->addError($this->__('Delete item from gift card box error.'));
		}
		
		$this->_redirect('checkout/cart');
	}
	
	public function updateQtyAction() {
		$data = $this->getRequest()->getParams();
		try {
			if (isset($data['id']) && $data['id'] > 0 && isset($data['qty']) && $data['qty'] >= 0 ) {
				$model = Mage::getModel('vc_giftwrap/wrap')->load($data['id']);
				if ($model && $model->getId() > 0) {
					if ($data['qty'] > 0) {
						//$item = Mage::getModel('sales/quote_item')->load($model->getQuoteItemId());
						$collection = Mage::getModel('sales/quote_item')->getCollection()
						->setQuote(Mage::getSingleton('checkout/cart')->getQuote())
						->addFieldToFilter('item_id', array('eq' => $model->getQuoteItemId()))
						->addFieldToFilter('parent_item_id', array('null' => NULL));
						$item = $collection->getFirstItem();
						
						$qty = 0;
						$collection2 = Mage::getModel('vc_giftwrap/wrap')->getCollection()
						->addFieldToFilter('quote_item_id', array('eq' => $model->getQuoteItemId()));
						if ($collection2 && $collection2->getSize() > 0) {
							foreach ($collection2 as $item2) {
								if ($item2->getId() == $model->getId()) continue;
								$qty += $item2->getQty();
							}
						}
						
						
						if ($item && $item->getQty() < ($qty + $data['qty'])) {
							Mage::throwException($this->__('Your input quantity is larger than product\'s quantity in cart.'));
						}
						$model->setQty($data['qty'])->save();
					} else {
						$model->delete();
					}
					$this->_getSession()->addSuccess($this->__('Update qty of item successful.'));
				}
			}
		} catch (Exception $e) {
			//$this->_getSession()->addError($this->__('Update qty of item error.'));
			$this->_getSession()->addError($e->getMessage());
		}
		$this->_redirect('checkout/cart');
	}

	public function changeWrapLayoutAction() {
		$result = array('code' => 'error', 
			'msg' => '',
			'link' => '');
			
		$params = $this->getRequest()->getParams();
		try {
			if (!isset($params['id']) || !$params['id']) {
				//Mage::throwException($this->__('Params is invalid.'));
			}
			
			$boxId = 0;
			if (isset($params['giftwrap_box'][0]) && $params['giftwrap_box'][0] > 0) {
				$boxId = $params['giftwrap_box'][0];
			}
			
			$cardId = 0;
			if (isset($params['giftwrap_card'][0]) && $params['giftwrap_card'][0] > 0) {
				$cardId = $params['giftwrap_card'][0];
			}
			
			
			if (isset($params['item_id']) && $params['item_id'] > 0) {
				$item = Mage::getModel('sales/quote_item')->load($params['item_id']);
				$params['qty'] = $item->getQty();
				$params['product'] = $item->getProductId();
			} else if ($params['id']) {
				$model = Mage::getModel('vc_giftwrap/wrap')->load($params['id']);
				if ($model && $model->getId() > 0) {
					$params['product'] = $model->getProductId();
					$item = Mage::getModel('sales/quote_item')->load($model->getQuoteItemId());
				}
			}
			
			if ($item && $item->getId() > 0) {
				$params['quote_item_id'] = $item->getId();
				Mage::getModel('vc_giftwrap/observer')->_updateWrap($item, $params);
			}
			
			$this->_getSession()->addSuccess($this->__('Update giftwrap successful.'));
			$result['code'] = 'success';
		} catch (Exception $e) {
			$result['msg'] = $e->getMessage();
		}
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
	}
	
	public function loadWrapLayoutAction() {
		$result = array('code' => 'error', 
			'msg' => '',
			'content' => '');
		$params = $this->getRequest()->getParams();	
		try {	
			$update = $this->getLayout()->getUpdate();
			$update->addHandle('vc_giftwrap_load_layout');
			$this->loadLayoutUpdates();
			$this->generateLayoutXml()->generateLayoutBlocks();			
			
			$itemId = 0;
			if (isset($params['item_id']) && $params['item_id'] > 0) {
				$model = Mage::getModel('vc_giftwrap/wrap')->load($params['item_id'], 'quote_item_id');
				if ($model) {
					$params['id'] = $model->getId();
				}
				$itemId = $params['item_id'];
			}
			
			$groupId = 0;
			$model = Mage::getModel('vc_giftwrap/wrap')->load($params['id']);
			if ($model) {
				$groupId = $model->getGroupId();
			}
			
			$result['content'] = $this->getLayout()->getBlock('product.giftwrap.layout')
			->setType($params['type'])
			->setId($params['id'])
			->setItemId($itemId)
			->setGroupId($groupId)
			->toHtml();
			$result['code'] = 'success';
		} catch (Exception $e) {
			$result['msg'] = $this->__('Loading data has error.');
		}
		
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
	}
	
	
	public function emptyAction() {
		$quote = Mage::getModel('checkout/cart')->getQuote();	
		$collection = Mage::getModel('vc_giftwrap/wrap')->getCollection()
		->addFieldToFilter('quote_id', array('eq' => $quote->getId()))
		;
		if ($collection && $collection->getSize() > 0) {
			foreach ($collection as $item) {
				$item->delete();
			}
			$this->_getSession()->addSuccess($this->__('Delete giftwrap successful.'));
		}
		$this->_redirect('checkout/cart');
	}
}