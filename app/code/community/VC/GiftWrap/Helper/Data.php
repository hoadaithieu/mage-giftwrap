<?php
class VC_GiftWrap_Helper_Data extends Mage_Core_Helper_Abstract {
	public function uploadFile($name) {
		try {
			$exAr = explode(',', str_replace('.', '', Mage::getStoreConfig('vc_giftwrap/general/image_extension_allow')));
			
			$uploader = new Varien_File_Uploader($name);
			// Any extention would work
			//$uploader->setAllowedExtensions(Mage::helper('vc_giftwrap')->getExtensionAr());
			//$uploader->setAllowedExtensions(array('png','gif','jpg','jpeg'));
			$uploader->setAllowedExtensions($exAr);
			$uploader->setAllowRenameFiles(false);
			$uploader->setFilesDispersion(false);
			// We set media as the upload dir
			$path = $this->getImagePath();
			$fileName = $_FILES[$name]['name'];
			
			if (file_exists($path.'/'.$fileName)) {
				$fileName = $this->renameFile($path, $fileName);
			}
			
			$uploader->save($path, $fileName);
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
		
		return $uploader->getUploadedFileName();	
	}
	
	/**
	@ method : renameFile
	**/
	
	public function renameFile($path, $fileName) {
		$pathinfo = pathinfo($path . '/' . $fileName);
		$fileTmp = $fileBase = preg_replace('/[^0-9,a-z,A-Z]/', '', $pathinfo['filename']);
		$ext = $pathinfo['extension'];
	
		$i = 0;
		while (file_exists($path . '/' . $fileBase . '.' . $ext)) {
			$fileBase = $fileTmp.$i;
			$i++;
		}
		return $fileBase.'.'.$ext;
	}
	
	/**
	@ method : _deleteFile
	**/
	
	public function deleteFile($file) {
		$file = (string)$file;
		if (file_exists($this->getImagePath().'/'.$file)) {
			@unlink($this->getImagePath().'/'.$file);	
		}
	}
	
	/**
	@ method : createFolder
	**/
	
	public function createFolder($name) {
		$path = trim(Mage::getBaseDir('media'), '/').'/'.$name;
		if (!file_exists($path)) {
			mkdir($path);
			chmod($path, 0777);
		}
	}
	
	/**
	@ method : getImagePath
	**/
	
	public function getImagePath() {
		return Mage::getBaseDir('media') .'/' . Mage::getStoreConfig('vc_giftwrap/general/image_folder');
	}
	
	/**
	@ method : getImageUrl
	**/
	
	public function getImageUrl() {
		return trim(Mage::getBaseUrl('media'), '/') .'/'. Mage::getStoreConfig('vc_giftwrap/general/image_folder');
	}
	
	
	public function convertTargetUrl($id) {
		//return 'vc_giftwrap/banner/index/id/'.$id;	
	}
	
	public function getAmountByQuoteId($quoteId = 0) {
		$amount = 0.00;
		$collection = Mage::getModel('vc_giftwrap/wrap')->getCollection()
		->addFieldToFilter('quote_id', $quoteId);
		if ($collection && $collection->getSize() > 0) {
			foreach ($collection as $item) {
				$amount += $item->getQty() * $item->getPrice();
			}
		}
		return $amount;
	}
	
	public function getAmountByOrderId($orderId = 0) {
		$amount = 0.00;
		$collection = Mage::getModel('vc_giftwrap/wrap')->getCollection()
		->addFieldToFilter('order_id', $orderId);
		
		if ($collection && $collection->getSize() > 0) {
			foreach ($collection as $item) {
				$qty = $item->getQty();
				$amount += $qty * $item->getPrice();
			}
		}
		
		return $amount;
	
	}
	
	public function getAmountInvoiceByOrderId($orderId = 0) {
		$amount = 0.00;
		$collection = Mage::getModel('vc_giftwrap/wrap')->getCollection()
		->addFieldToFilter('main_table.order_id', $orderId);
		
		$invoiceId = Mage::app()->getRequest()->getParam('invoice_id', 0);
		if ($invoiceId > 0) {
			$collection->getSelect()->joinInner(array('isr' =>  Mage::getSingleton('core/resource')->getTableName('vc_giftwrap/isr')), 
			'main_table.id = isr.wrap_id AND isr.entity_id = '.$invoiceId.' AND isr.code ="'.VC_GiftWrap_Model_Isr::INVOICE.'"', array('isr.qty AS isr_qty'));
		}
		
		$wrapInvoiceAr = array();
		if (Mage::registry('wrap_invoice_ar')) {
			$wrapInvoiceAr = Mage::registry('wrap_invoice_ar');
		}
		
		$params = Mage::app()->getRequest()->getParams('invoice');
		if (isset($params['invoice']['wrapped_items']) && is_array($params['invoice']['wrapped_items']) && count($params['invoice']['wrapped_items']) > 0) {
			foreach ($params['invoice']['wrapped_items'] as $wrapId => $qty) {
				$wrapInvoiceAr[$wrapId] = $qty;
			}
		}
		
		if ($collection && $collection->getSize() > 0) {
			foreach ($collection as $item) {
				
				$qty = $item->getQtyInvoiced();
				if ($qty == NULL) {
					$qty = $item->getQty();
				}
				
				if ($invoiceId > 0) {
					$qty = $item->getIsrQty();
				}
				
				
				if (isset($wrapInvoiceAr[$item->getId()])) {
					$qty = $wrapInvoiceAr[$item->getId()];
				}
				$amount += $qty * $item->getPrice();
			}
		}
		
		return $amount;
	
	}
	
	public function getAmountCreditmemoByOrderId($orderId = 0) {
		$amount = 0.00;
		$collection = Mage::getModel('vc_giftwrap/wrap')->getCollection()
		->addFieldToFilter('main_table.order_id', $orderId);
		
		$creditmemoId = Mage::app()->getRequest()->getParam('creditmemo_id', 0);
		if ($creditmemoId > 0) {
			$collection->getSelect()->joinInner(array('isr' =>  Mage::getSingleton('core/resource')->getTableName('vc_giftwrap/isr')), 
			'main_table.id = isr.wrap_id AND isr.entity_id = '.$creditmemoId.' AND isr.code ="'.VC_GiftWrap_Model_Isr::REFUND.'"', array('isr.qty AS isr_qty'));
		}
		
		$wrapCreditmemoAr = array();
		if (Mage::registry('wrap_creditmemo_ar')) {
			$wrapCreditmemoAr = Mage::registry('wrap_creditmemo_ar');
		}
		
		$params = Mage::app()->getRequest()->getParams('creditmemo');
		if (isset($params['creditmemo']['wrapped_items']) && is_array($params['creditmemo']['wrapped_items']) && count($params['creditmemo']['wrapped_items']) > 0) {
			foreach ($params['creditmemo']['wrapped_items'] as $wrapId => $qty) {
				$wrapCreditmemoAr[$wrapId] = $qty;
			}
		}
		
		
		if ($collection && $collection->getSize() > 0) {
			foreach ($collection as $item) {
				
				$qty = $item->getQtyRefunded();
				if ($qty == NULL) {
					$qty = $item->getQtyInvoiced();
				}

				if ($creditmemoId > 0) {
					$qty = $item->getIsrQty();
				}
				
				
				if (isset($wrapCreditmemoAr[$item->getId()])) {
					$qty = $wrapCreditmemoAr[$item->getId()];
				}
				
				$amount += $qty * $item->getPrice();
			}
		}
		
		return $amount;
	
	}	
	
	public function getWrapByQuoteItemId($quoteItemId = 0) {
		$model = Mage::getModel('vc_giftwrap/wrap')->load($quoteItemId,'quote_item_id');
		return $model;
	}
	
	public function isWrapped($quoteItemId = 0) {
		$model = Mage::getModel('vc_giftwrap/wrap')->load($quoteItemId, 'quote_item_id');
		if ($model && $model->getId() > 0) {
			return true;
		}
		return false;
	}
}