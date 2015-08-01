<?php
class VC_GiftWrap_Block_Adminhtml_Product extends Mage_Adminhtml_Block_Widget_Grid_Container {
	public function __construct() {
		$this->_controller = 'adminhtml_product';
		$this->_blockGroup = 'vc_giftwrap';
		$this->_headerText = Mage::helper('vc_giftwrap')->__('Gift Product Manager');
		$this->_addButtonLabel = Mage::helper('vc_giftwrap')->__('Add Product');
		parent::__construct();
		$this->setTemplate('vc_giftwrap/product.phtml');
	}
	
    public function isSingleStoreMode()
    {
        if (!Mage::app()->isSingleStoreMode()) {
               return false;
        }
        return true;
    }
	
    public function getCreateUrl()
    {
        return $this->getUrl('adminhtml/catalog_product/new');
    }
	
}
