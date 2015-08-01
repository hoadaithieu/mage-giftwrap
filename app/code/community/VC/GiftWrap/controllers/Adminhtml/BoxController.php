<?php
class VC_GiftWrap_Adminhtml_BoxController extends Mage_Adminhtml_Controller_Action {
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('catalog')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Box Manager'), Mage::helper('adminhtml')->__('Box Manager'));
		
		return $this;
	}   
 
 	/**
	@ method : indexAction
	**/
	
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	/**
	@ method : editAction
	**/
	
	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('vc_giftwrap/box')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}
			
			$storeIdAr = array();
			$collection = Mage::getModel('vc_giftwrap/box_store')->getCollection()->addFieldToFilter('box_id', $model->getId());
			if ($collection->getSize() > 0) {
				foreach ($collection as $item) {
					$storeIdAr[] = $item->getStoreId();
				}
			}
			
			Mage::register('store_data', $storeIdAr);
			Mage::register('box_id', $id);
			Mage::register('box_data', $model);

			$this->_getSession()->addNotice(Mage::helper('vc_giftwrap')->__('Notice: Pdf support only extensions ('.Mage::getStoreConfig('vc_giftwrap/pdf/image_extension_allow').')'));			

			$this->loadLayout();
			$this->_setActiveMenu('catalog');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Box Manager'), Mage::helper('adminhtml')->__('Box Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Box News'), Mage::helper('adminhtml')->__('Box News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('vc_giftwrap/adminhtml_box_edit'))
				->_addLeft($this->getLayout()->createBlock('vc_giftwrap/adminhtml_box_edit_tabs'));
			$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('vc_giftwrap')->__('Box does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
 	/**
	@ method : newAction
	**/
	
	public function newAction() {
		$this->_forward('edit');
	}
	
 
 	/**
	@ method : saveAction
	**/
	
	public function saveAction() {
		
		if ($data = $this->getRequest()->getPost()) {
			$model = Mage::getModel('vc_giftwrap/box')->load($this->getRequest()->getParam('id'));		
			try {
			
				//$identifier = Mage::helper('vc_giftwrap')->preProcessIdentifier($data['title']);
				//if (!$model->validIdentifier($identifier)) throw new Exception(Mage::helper('vc_giftwrap')->__('Identifier field is exiting for other item.'));
			
				
                $format = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
                if (isset($data['created_at']) && $data['created_at']) {
                    $dateFrom = Mage::app()->getLocale()->date($data['created_at'], $format);
                    $model->setCreatedAt(Mage::getModel('core/date')->gmtDate(null, $dateFrom->getTimestamp()));
                    $model->setUpdatedAt(Mage::getModel('core/date')->gmtDate());
                } else {
                    $model->setCreatedAt(Mage::getModel('core/date')->gmtDate());
                }
				
				// BEGIN IMAGE
				Mage::helper('vc_giftwrap')->createFolder(Mage::getStoreConfig('vc_giftwrap/general/image_folder'));
				$ar = array('image');
				
				foreach ($ar as $name) {
					$isDeleted = false;
					if (isset($data[$name]['delete']) && isset($data[$name]['value'])) {
						Mage::helper('vc_giftwrap')->deleteFile($data[$name]['value']);
						$data[$name] = '';
						$isDeleted = true;
					}
					
					if(isset($_FILES[$name]['name']) && $_FILES[$name]['name'] != '') {
						$data[$name] = Mage::helper('vc_giftwrap')->uploadFile($name);
					} else if (!$isDeleted) {
						unset($data[$name]);
					}
				}	
				// END
				
				
				$model->setTitle($data['title'])
					->setPosition($data['position'])
					->setPrice($data['price'])
					->setIsActive(($data['is_active'] >= 1 && $data['is_active'] <= 2) ? $data['is_active']: 2);
				
				if (isset($data[$ar[0]])) {
					$model->setImage($data[$ar[0]]);
				}
				$model->save();
				
				$boxId = $model->getId();
				if ($boxId > 0) {
					Mage::getModel('vc_giftwrap/box_store')->deleteByBoxId($boxId);
					
					if (is_array($data['stores']) && count($data['stores']) > 0) {
						foreach ($data['stores'] as $storeId) {
							$model = Mage::getModel('vc_giftwrap/box_store');
							$model->setBoxId($boxId)
							->setStoreId($storeId)
							->save();
						}
					}
				}
				
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('vc_giftwrap')->__('Box was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('vc_giftwrap')->__('Unable to find Box to save'));
        $this->_redirect('*/*/');
	}
	
	/**
	@ method : deleteAction
	**/
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$this->_deleteAction($this->getRequest()->getParam('id'));	 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Box was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

	/**
	@ method : massDeleteAction
	**/
	
    public function massDeleteAction() {
        $ppIds = $this->getRequest()->getParam('vc_giftwrap');
        if(!is_array($ppIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select Box(s)'));
        } else {
            try {
                foreach ($ppIds as $ppId) {
					$this->_deleteAction($ppId);
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($ppIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
	/**
	@ method : _deleteAction
	**/
	
	private function _deleteAction($boxId = 0) {
		$model = Mage::getModel('vc_giftwrap/box')->load($boxId);
		if ($model->getId() > 0) {
			if (strlen($model->getImage()) > 0) {
				Mage::helper('vc_giftwrap')->deleteFile($model->getImage());
			}
		
			$model->delete();	
			
			Mage::getModel('vc_giftwrap/box_store')->deleteByBoxId($boxId);			
		}	
	}
	
	public function statusAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('vc_giftwrap/box')->load($this->getRequest()->getParam('id'));	 
				if ($model->getId() > 0) {
					$model->setIsActive(($model->getIsActive() == 1? 2: 1))->save();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Box was successfully changed status.'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/');	
	}
	
	/**
	@ method : massStatusAction
	**/
	
    public function massStatusAction()
    {
        $ppIds = $this->getRequest()->getParam('vc_giftwrap');
        if(!is_array($ppIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select Box(s)'));
        } else {
            try {
                foreach ($ppIds as $ppId) {
                    $pp = Mage::getSingleton('vc_giftwrap/box')
                        ->load($ppId)
                        ->setIsActive($this->getRequest()->getParam('status'))
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($ppIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
}