<?php
class VC_GiftWrap_Block_Adminhtml_Card_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('card_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('vc_giftwrap')->__('Card Information'));
	}
	
	protected function _beforeToHtml()
	{
		$this->addTab('form_section', array(
		  'label'     => Mage::helper('vc_giftwrap')->__('Card Information'),
		  'title'     => Mage::helper('vc_giftwrap')->__('Card Information'),
		  'content'   => $this->getLayout()->createBlock('vc_giftwrap/adminhtml_card_edit_tab_form')->toHtml(),
		));

		return parent::_beforeToHtml();
	}
}