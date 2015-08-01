<?php
class VC_GiftWrap_Model_Observer {

	
	public function _updateWrap($item, $params) {
		$model = Mage::getModel('vc_giftwrap/wrap');
		// Exit when update item
		$data = array('box_id' => 0, 'group_id' => 0);
		
		if (!isset($params['product']) || ($params['product'] != $item->getProductId())) return;
		
		if (isset($params['super_attribute'])) {
			$option = Mage::getModel('sales/quote_item_option')->load($item->getId(), 'item_id');
			$_params = unserialize( $option->getValue());
			$_rs1 = array_diff($params['super_attribute'], $_params['super_attribute']);
			$_rs2 = array_diff($_params['super_attribute'], $params['super_attribute']);
			if (count(array_merge($_rs1, $_rs2))) {
				return;
			}
		}
		
		
		// update attribute
		if (($oldModel = Mage::registry('old_giftwrap'))) {
			$data = $oldModel->getData();
			unset($data['id']);
		}
		 
		// update qty
		$oldModel = Mage::getModel('vc_giftwrap/wrap')->load($item->getItemId(),'quote_item_id');
		if ($oldModel && $oldModel->getId() > 0) {
			$data = $oldModel->getData();
			unset($data['id']);
			if (isset($params['quote_item_id']) && $params['quote_item_id'] > 0){
				$model = $oldModel;
			}
		}
		
		// Add new item
		
		if (isset($params['giftwrap_group'][0]) && ($params['giftwrap_group'][0] > 0) && $data['group_id'] != $params['giftwrap_group'][0]) {
			$model1 = Mage::getModel('vc_giftwrap/wrap')->load($params['giftwrap_group'][0], 'group_id');
			if ($model1 && $model1->getId() > 0) {
				$data = $model1->getData();
				unset($data['id']);
			}
		}
		
		
		
		if (!$item->getParentItemId() && ((isset($params['giftwrap_box'][0]) && $params['giftwrap_box'][0] > 0) || (isset($params['giftwrap_group']) && isset($params['giftwrap_group'][0]) && $params['giftwrap_group'][0] > 0) )) {
			
			$price = 0.00;
			if (isset($params['giftwrap_box']) && isset($params['giftwrap_box'][0]) && $params['giftwrap_box'][0] > 0) {
				$data['box_id'] = $params['giftwrap_box'][0];
			}
			
			if ($data['box_id']) {
				$boxModel = Mage::getModel('vc_giftwrap/box')->load($data['box_id']);
				if ($boxModel && $boxModel->getId()) {
					$price = $boxModel->getPrice();
				}
			}
			
			if (!$data['box_id']) return; 
			
			
			
			if (isset($params['giftwrap_card']) && isset($params['giftwrap_card'][0]) && $params['giftwrap_card'][0] > 0) {
				$data['card_id'] = $params['giftwrap_card'][0];
			}
			
			if ($data['card_id']) {
				$cardModel = Mage::getModel('vc_giftwrap/card')->load($data['card_id']);
				if ($cardModel && $cardModel->getId()) {
					$price += $cardModel->getPrice();
					
					if (isset($params['card_message']) && strlen(trim($params['card_message'])) > 0) {
						$data['card_message'] = Mage::helper('core/string')->substr($params['card_message'], 0, $cardModel->getMaxCharacters());
					}
				}
			}
			
			$data['product_id'] = $item->getProductId();
			$data['sku'] = $item->getSku();
			$data['quote_item_id'] = $item->getItemId();
			$data['quote_id'] = $item->getQuoteId();
			$data['qty'] = isset($params['qty']) ? $params['qty'] : $item->getQty();
			$data['price'] = $price;
			
			
			
			if ($model->getId() > 0) {
				// edit when changing qty
				$model
				->setBoxId($data['box_id'])
				->setCardId($data['card_id'])
				//->setGroupId($model->getId())
				->setQty($data['qty'])
				->setPrice($data['price']);
				
			} else {
				$model->setData($data);
			}
			$model->save();
			
			if (isset($params['giftwrap_group']) && isset($params['giftwrap_group'][0]) && $params['giftwrap_group'][0] > 0) {
				$wrapModel = Mage::getModel('vc_giftwrap/wrap')->load($params['giftwrap_group'][0], 'group_id');
				if ($wrapModel && $wrapModel->getId() > 0) {
					$model->setCardMessage($wrapModel->getCardMessage());
				}
				
				$data['group_id'] = $params['giftwrap_group'][0];
				$model->setGroupId($params['giftwrap_group'][0])
				
				->save();
			} else {
				/*
				$collection = Mage::getModel('vc_giftwrap/wrap')->getCollection()
				->addFieldToFilter('box_id', array('eq' => $boxId))
				->addFieldToFilter('card_id', array('eq' => $cardId))
				->addFieldToFilter('quote_id', array('eq' => $model->getQuoteId()));
				$item = $collection->getFirstItem();
				if ($item) {
					$model->setGroupId($item->getGroupId())->save();
				} else
				*/
					$model->setGroupId($model->getId())->save();		
			}
			
			
			// Update group_id infor if main record was deleted
			/*
			if ($data['group_id'] > 0) {
				$groupModel = Mage::getModel('vc_giftwrap/wrap')->load($data['group_id']);
				if (!$groupModel || !$groupModel->getId()) {
					$collection = Mage::getModel('vc_giftwrap/wrap')->getCollection()
					->addFieldToFilter('group_id', array('eq' => $data['group_id']));
					if ($collection) {
						foreach ($collection as $item) {
							$item->setGroupId($item->getId())->save();
						}
					}
				}
			}
			*/
			
			/*
			$content = file_get_contents('test.log');
			ob_start();
			print_r($params);
			$content .= "\n".$item->getItemId().'=>'.ob_get_clean();
			file_put_contents('test.log', $content);
			*/
		}	
	}
	
	/**
	event: sales_quote_item_save_after
	**/
	
	public function addWrap($observer) {
		if (!Mage::getStoreConfig('vc_giftwrap/general/enable')) return;
		
		$item = $observer->getEvent()->getDataObject();
		$params = Mage::app()->getRequest()->getParams();
		
		
		$params['action'] = 'add';
		$this->_updateWrap($item, $params);
		
		// use for merge quote (login after adding item)
		if (Mage::getSingleton('customer/session')->getReUpdateQuote() && !$item->getParentItemId()) {
			$collection = Mage::getModel('vc_giftwrap/wrap')->getCollection()
			->addFieldToFilter('quote_id', array('eq' => $item->getQuoteId()))
			->addFieldToFilter('product_id', array('eq' => $item->getProductId()))
			->addFieldToFilter('sku', array('eq' => $item->getSku()));
			
			if ($collection && $collection->getSize() > 0) {
				foreach ($collection as $iWrap) {
					$iWrap->setQuoteItemId($item->getId())->save();
				}
			}
			//Mage::getSingleton('customer/session')->setReUpdateQuote(false);
		}
		
	}
	
	/**
	event: checkout_cart_update_item_complete
	**/
	
	public function updateWrap($observer) {
		if (!Mage::getStoreConfig('vc_giftwrap/general/enable')) return;
		
		$item = $observer->getEvent()->getItem();
		$params = $observer->getEvent()->getRequest()->getParams();
		$params['action'] = 'update';
		$this->_updateWrap($item, $params);
	}

	/**
	event: sales_quote_item_delete_before
	**/
	
	public function saveWrapByQuoteItem($observer) {
		if (!Mage::getStoreConfig('vc_giftwrap/general/enable')) return;
	
		$quoteItem = $observer->getEvent()->getDataObject();
		$model = Mage::getModel('vc_giftwrap/wrap')->load($quoteItem->getId(),'quote_item_id');
		if ($model && $model->getId()) {
			if (Mage::registry('old_giftwrap')) Mage::unregister('old_giftwrap');
			Mage::register('old_giftwrap', $model);
		}
	}
	
	/**
	event: sales_quote_item_delete_after
	**/	
	
	public function removeWrapByQuoteItem($observer) {
		if (!Mage::getStoreConfig('vc_giftwrap/general/enable')) return;
		//$quoteItem = $observer->getEvent()->getQuoteItem();
		$quoteItem = $observer->getEvent()->getDataObject();
		$collection = Mage::getModel('vc_giftwrap/wrap')->getCollection()
		->addFieldToFilter('quote_item_id', $quoteItem->getId())
		->addFieldToFilter('product_id', $quoteItem->getProductId())
		->addFieldToFilter('quote_id', $quoteItem->getQuoteId())
		;
		
		if ($collection && $collection->getSize() > 0) {
			foreach ($collection as $item) {
				$item->delete();
			}
		}
	}
	
	/**
	event: checkout_type_onepage_save_order_after
	**/
	
	public function updateOrderId($observer) {
		if (!Mage::getStoreConfig('vc_giftwrap/general/enable')) return;
		
		$order = $observer->getEvent()->getOrder();
		$quote = $observer->getEvent()->getQuote();
		$collection = Mage::getModel('vc_giftwrap/wrap')->getCollection()
		->addFieldToFilter('quote_id', array('eq' => $quote->getId()))
		->addFieldToFilter('order_id', array('eq' => 0));
		
		if ($collection && $collection->getSize() > 0) {
			foreach ($collection as $item) {
				$item->setOrderId($order->getId())->save();
			}
		}
	}
	
	/**
	event: controller_action_predispatch_adminhtml_sales_order_invoice_updateqty
	**/
	
	public function invoiceUpdateQty($observer) {
		if (!Mage::getStoreConfig('vc_giftwrap/general/enable')) return;
		
		$controllerAction = $observer->getEvent()->getControllerAction();
		$params = $controllerAction->getRequest()->getParams('invoice');
		$wrapInvoiceAr = array();
		if (isset($params['invoice']['wrapped_items']) && is_array($params['invoice']['wrapped_items']) && count($params['invoice']['wrapped_items']) > 0) {
			foreach ($params['invoice']['wrapped_items'] as $wrapId => $qty) {
				$wrapModel = Mage::getModel('vc_giftwrap/wrap')->load($wrapId);
				if ($wrapModel && $wrapModel->getId() > 0) {
					$item = Mage::getModel('sales/order_item')->load($wrapModel->getQuoteItemId(),'quote_item_id');
					if ($item && $item->getId() > 0 && ($qty + (int)$item->getQtyInvoiced()) > $item->getQtyOrdered()) {
						Mage::register('vc_giftwrap_error', Mage::helper('vc_giftwrap')->__('Cannot update item quantity.'));
						break;
					}
					
					if ($qty >= 0) {
						$wrapInvoiceAr[$wrapId] = $qty;
					}
				}
			}
		}
		
		if (count($wrapInvoiceAr) > 0) {
			Mage::unregister('wrap_invoice_ar');
			Mage::register('wrap_invoice_ar', $wrapInvoiceAr);
		}
	}
	
	/**
	event: controller_action_predispatch_adminhtml_sales_order_invoice_save
	**/
	
	public function invoiceSave($observer) {
		if (!Mage::getStoreConfig('vc_giftwrap/general/enable')) return;
		
		$controllerAction = $observer->getEvent()->getControllerAction();
		$params = $controllerAction->getRequest()->getParams('invoice');
		if (isset($params['invoice']['wrapped_items']) && is_array($params['invoice']['wrapped_items']) && count($params['invoice']['wrapped_items']) > 0) {
			foreach ($params['invoice']['wrapped_items'] as $wrapId => $qty) {
				$wrapModel = Mage::getModel('vc_giftwrap/wrap')->load($wrapId);
				if ($wrapModel && $wrapModel->getId() > 0) {
					$item = Mage::getModel('sales/order_item')->load($wrapModel->getQuoteItemId(),'quote_item_id');
					if ($item && $item->getId() > 0 && ($qty + (int)$item->getQtyInvoiced()) > $item->getQtyOrdered()) {
						Mage::register('vc_giftwrap_error', Mage::helper('vc_giftwrap')->__('Cannot update item quantity.'));
						break;
					}
					
					if ($qty > 0) {
						$wrapModel->setQtyInvoiced($qty + $wrapModel->getQtyInvoiced())
						->save();
					} else {
						$wrapModel->delete();
					}
				}
			}
		}
	}
	
	
	
	/**
	event: controller_action_predispatch_adminhtml_sales_order_creditmemo_updateqty
	**/
	
	public function creditmemoUpdateQty($observer) {
		if (!Mage::getStoreConfig('vc_giftwrap/general/enable')) return;
		
		$controllerAction = $observer->getEvent()->getControllerAction();
		$params = $controllerAction->getRequest()->getParams('creditmemo');
		$wrapCreditmemoAr = array();
		if (isset($params['creditmemo']['wrapped_items']) && is_array($params['creditmemo']['wrapped_items']) && count($params['creditmemo']['wrapped_items']) > 0) {
			foreach ($params['creditmemo']['wrapped_items'] as $wrapId => $qty) {
				$wrapModel = Mage::getModel('vc_giftwrap/wrap')->load($wrapId);
				if ($wrapModel && $wrapModel->getId() > 0) {
					$item = Mage::getModel('sales/order_item')->load($wrapModel->getQuoteItemId(),'quote_item_id');
					if ($item && $item->getId() > 0 && ($qty + (int)$item->getQtyRefunded()) > $item->getQtyInvoiced()) {
						Mage::register('vc_giftwrap_error', Mage::helper('vc_giftwrap')->__('Cannot update item quantity.'));
						break;
					}
					
					if ($qty >= 0) {
						$wrapCreditmemoAr[$wrapId] = $qty;
					}
				}
			}
		}
		
		if (count($wrapCreditmemoAr) > 0) {
			Mage::unregister('wrap_creditmemo_ar');
			Mage::register('wrap_creditmemo_ar', $wrapCreditmemoAr);
		}
	}
	
	/**
	event: controller_action_predispatch_adminhtml_sales_order_creditmemo_save
	**/
	
	public function creditmemoSave($observer) {
		if (!Mage::getStoreConfig('vc_giftwrap/general/enable')) return;
		
		$controllerAction = $observer->getEvent()->getControllerAction();
		$params = $controllerAction->getRequest()->getParams('creditmemo');
		if (isset($params['creditmemo']['wrapped_items']) && is_array($params['creditmemo']['wrapped_items']) && count($params['creditmemo']['wrapped_items']) > 0) {
			foreach ($params['creditmemo']['wrapped_items'] as $wrapId => $qty) {
				$wrapModel = Mage::getModel('vc_giftwrap/wrap')->load($wrapId);
				if ($wrapModel && $wrapModel->getId() > 0) {
					$item = Mage::getModel('sales/order_item')->load($wrapModel->getQuoteItemId(),'quote_item_id');
					if ($item && $item->getId() > 0 && ($qty + (int)$item->getQtyRefunded()) > $item->getQtyInvoiced()) {
						Mage::register('vc_giftwrap_error', Mage::helper('vc_giftwrap')->__('Cannot update item quantity.'));
						break;
					}
					
					if ($qty > 0) {
						$wrapModel->setQtyRefunded($qty + $wrapModel->getQtyRefunded())
						->save();
					} else {
						$wrapModel->delete();
					}
				}
			}
		}
	}
	
	/**
	event: controller_action_predispatch_adminhtml_sales_order_shipment_save
	**/
	
	public function shipmentSave($observer) {
		if (!Mage::getStoreConfig('vc_giftwrap/general/enable')) return;
		
		$controllerAction = $observer->getEvent()->getControllerAction();
		$params = $controllerAction->getRequest()->getParams('shipment');
		
		if (isset($params['shipment']['wrapped_items']) && is_array($params['shipment']['wrapped_items']) && count($params['shipment']['wrapped_items']) > 0) {
			foreach ($params['shipment']['wrapped_items'] as $wrapId => $qty) {
				$wrapModel = Mage::getModel('vc_giftwrap/wrap')->load($wrapId);
				if ($wrapModel && $wrapModel->getId() > 0) {
					$item = Mage::getModel('sales/order_item')->load($wrapModel->getQuoteItemId(),'quote_item_id');
					
					if ($item && $item->getId() > 0 && ($qty + (int)$item->getQtyRefunded() + (int)$item->getQtyShipped()) > $item->getQtyOrdered()) {
						Mage::register('vc_giftwrap_error', Mage::helper('vc_giftwrap')->__('Cannot update item quantity.'));
						break;
					}
					
					if ($qty > 0) {
						$wrapModel->setQtyShipped($qty + (int)$wrapModel->getQtyShipped())
						->save();
					} else {
						$wrapModel->delete();
					}
				}
			}
		}
	}	
	
		
	/**
	event: load_customer_quote_before
	**/
	
	public function loadCustomerQuoteBefore($observer) {
		if (!Mage::getStoreConfig('vc_giftwrap/general/enable')) return;
		
		$checkoutSession = $observer->getEvent()->getCheckoutSession();
        $customerQuote = Mage::getModel('sales/quote')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->loadByCustomer(Mage::getSingleton('customer/session')->getCustomerId());

        if ($customerQuote->getId() && $checkoutSession->getQuoteId() != $customerQuote->getId()) {
            if ($checkoutSession->getQuoteId()) {
                $collection = Mage::getModel('vc_giftwrap/wrap')->getCollection()
				->addFieldToFilter('quote_id', array('eq' => $checkoutSession->getQuoteId()))
				->addFieldToFilter('order_id', array('eq' => 0));
				if ($collection && $collection->getSize() > 0) {
					foreach ($collection as $item) {
						$item->setQuoteId($customerQuote->getId())
						->save();
					}
					Mage::getSingleton('customer/session')->setReUpdateQuote(true);
				}
            }
		}		
	}
	
	/**
	event: quote_save_commit_after
	**/
	
	public function quoteSaveCommitAfter($observer) {
		if (!Mage::getStoreConfig('vc_giftwrap/general/enable')) return;
		
		Mage::getSingleton('customer/session')->setReUpdateQuote(false);
	}
	
	/**
	event: checkout_cart_update_items_after
	**/
	
	public function cartUpdateItemsAfter($observer) {
		if (!Mage::getStoreConfig('vc_giftwrap/general/enable')) return;
		
		$cart = $observer->getEvent()->getCart();
		$info = $observer->getEvent()->getInfo();
		
		if (is_array($info) && count($info) > 0) {
			foreach ($info as $itemId => $data) {
				$model = Mage::getModel('vc_giftwrap/wrap')->load($itemId,'quote_item_id');
				if ($data['qty'] < $model->getQuantityByQuoteItemId()) {
					Mage::getSingleton('checkout/session')->addError(Mage::helper('vc_giftwrap')->__('Remove/Reduce quantity of product with sku ("'.$model->getSku().'") from giftwrap.'));
				}
			}
		}
	}
	
	protected function _isrSaveAfter($items, $item, $code) {
		foreach ($items as $wrapId => $qty) {
			$model = Mage::getModel('vc_giftwrap/isr');
			$data = array('code' => $code, 
			'order_id' => $item->getOrderId(), 
			'entity_id' => $item->getId(), 
			'wrap_id' => $wrapId,
			'qty' => $qty);
			
			$model->setData($data)->save();
		}
	}
	
	/**
	event: sales_order_invoice_save_after
	**/
	
	public function invoiceSaveAfter($observer) {
		if (!Mage::getStoreConfig('vc_giftwrap/general/enable')) return;
			
		$item = $observer->getEvent()->getDataObject();
		$params = Mage::app()->getRequest()->getParams('invoice');
		if (isset($params['invoice']['wrapped_items']) && is_array($params['invoice']['wrapped_items']) && count($params['invoice']['wrapped_items']) > 0) {
			$this->_isrSaveAfter($params['invoice']['wrapped_items'], $item,  VC_GiftWrap_Model_Isr::INVOICE);
		}			
	}
	
	/**
	event: sales_order_creditmemo_save_after
	**/
	
	public function creditmemoSaveAfter($observer) {
		if (!Mage::getStoreConfig('vc_giftwrap/general/enable')) return;
		
		$item = $observer->getEvent()->getDataObject();
		$params = Mage::app()->getRequest()->getParams('creditmemo');
		if (isset($params['creditmemo']['wrapped_items']) && is_array($params['creditmemo']['wrapped_items']) && count($params['creditmemo']['wrapped_items']) > 0) {
			$this->_isrSaveAfter($params['creditmemo']['wrapped_items'], $item, VC_GiftWrap_Model_Isr::REFUND);
		}			
		
	}

	/**
	event: sales_order_shipment_save_after
	**/
	
	public function shipmentSaveAfter($observer) {
		if (!Mage::getStoreConfig('vc_giftwrap/general/enable')) return;
		
		$item = $observer->getEvent()->getDataObject();
		$params = Mage::app()->getRequest()->getParams('shipment');
		if (isset($params['shipment']['wrapped_items']) && is_array($params['shipment']['wrapped_items']) && count($params['shipment']['wrapped_items']) > 0) {
			$this->_isrSaveAfter($params['shipment']['wrapped_items'], $item, VC_GiftWrap_Model_Isr::SHIPMENT);
		}			
		
	}
	
}