<?php
class VC_GiftWrap_Adminhtml_ProductController extends Mage_Adminhtml_Controller_Action {
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('catalog')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Product Manager'), Mage::helper('adminhtml')->__('Product Manager'));
		
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
     * Product grid for AJAX request
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

	
 	/**
	@ method : newAction
	**/
	
	
    public function massWrapableAction()
    {
        $ids = $this->getRequest()->getParam('id');
		$wrap = $this->getRequest()->getParam('wrap');
		$storeId    = (int)$this->getRequest()->getParam('store', 0);
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select Product(s)'));
        } else {
            try {
                Mage::getSingleton('catalog/product_action')
                ->updateAttributes($ids, array('is_wrapable' => $wrap), $storeId);

                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($ids))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
}