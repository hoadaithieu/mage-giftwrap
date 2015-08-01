<?php
class VC_GiftWrap_Block_Adminhtml_Card extends Mage_Adminhtml_Block_Widget_Grid_Container {
	public function __construct() {
		$this->_controller = 'adminhtml_card';
		$this->_blockGroup = 'vc_giftwrap';
		$this->_headerText = Mage::helper('vc_giftwrap')->__('Gift Card Manager');
		$this->_addButtonLabel = Mage::helper('vc_giftwrap')->__('Add Card');
		parent::__construct();
		$this->setTemplate('vc_giftwrap/card.phtml');
	}
	
    public function isSingleStoreMode()
    {
        if (!Mage::app()->isSingleStoreMode()) {
               return false;
        }
        return true;
    }
	
}
