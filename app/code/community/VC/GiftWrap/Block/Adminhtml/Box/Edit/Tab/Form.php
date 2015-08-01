<?php
class VC_GiftWrap_Block_Adminhtml_Box_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('box_form', array('legend'=>Mage::helper('vc_giftwrap')->__('Box information')));
		$priceGroup = Mage::registry('box_data');
		
		$fieldset->addField('title', 'text', array(
		  'label'     => Mage::helper('vc_giftwrap')->__('Title'),
		  'class'     => 'required-entry',
		  'required'  => true,
		  'name'      => 'title',
		));
	
		$fieldset->addType('fileupload', 'VC_GiftWrap_Block_Adminhtml_Form_Element_FileUpload');
		$fieldset->addField('image', 'fileupload',
			array(
				'name'  => 'image',
				'label' => Mage::helper('vc_giftwrap')->__('Image'),
				'title' => Mage::helper('vc_giftwrap')->__('Image'),
				'note'  => 'Allowed Extensions : '. Mage::getStoreConfig('vc_giftwrap/general/image_extension_allow').'
				 with dimension ('.Mage::getStoreConfig('vc_giftwrap/general/image_width').'px x '.Mage::getStoreConfig('vc_giftwrap/general/image_height').'px)',
				'class'     => 'required-entry',
				'required'  => true,
				'path_url' => Mage::helper('vc_giftwrap')->getImageUrl()
			)
		);	
	
	
		$fieldset->addField('price', 'text', array(
		  'label'     => Mage::helper('vc_giftwrap')->__('Price'),
		  'class'     => 'required-entry',
		  'required'  => true,
		  'name'      => 'price',
		));
		

        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField(
                'store_id',
                'multiselect',
                array(
                     'name'     => 'stores[]',
                     'label'    => Mage::helper('cms')->__('Store View'),
                     'title'    => Mage::helper('cms')->__('Store View'),
                     'required' => true,
                     'values'   => Mage::getSingleton('adminhtml/system_store')
                             ->getStoreValuesForForm(false, true),
                )
            );
        }
		
		$fieldset->addField('position', 'text', array(
		  'label'     => Mage::helper('vc_giftwrap')->__('Position'),
		  'class'     => 'required-entry',
		  'required'  => true,
		  'name'      => 'position',
		));
		
		
		$fieldset->addField('is_active', 'select', array(
		  'label'     => Mage::helper('vc_giftwrap')->__('Status'),
		  'name'      => 'is_active',
		  'values'    => array(
			  array(
				  'value'     => 1,
				  'label'     => Mage::helper('vc_giftwrap')->__('Enabled'),
			  ),
			  array(
				  'value'     => 2,
				  'label'     => Mage::helper('vc_giftwrap')->__('Disabled'),
			  ),
		  ),
		));
		
		
		if ( Mage::registry('box_data') ) {
			$data = Mage::registry('box_data')->getData();
			$data['store_id'] = Mage::registry('store_data');
			$form->setValues($data);
		}
		return parent::_prepareForm();
	}
}