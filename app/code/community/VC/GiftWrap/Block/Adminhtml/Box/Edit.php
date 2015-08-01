<?php
class VC_GiftWrap_Block_Adminhtml_Box_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'vc_giftwrap';
        $this->_controller = 'adminhtml_box';
        
        $this->_updateButton('save', 'label', Mage::helper('vc_giftwrap')->__('Save Box'));
        $this->_updateButton('delete', 'label', Mage::helper('vc_giftwrap')->__('Delete Box'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('description') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'description');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'description');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action + 'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('box_data') && Mage::registry('box_data')->getId() ) {
            return Mage::helper('vc_giftwrap')->__("Edit Box '%s'", $this->htmlEscape(Mage::registry('box_data')->getTitle()));
        } else {
            return Mage::helper('vc_giftwrap')->__('Add Box');
        }
    }
}