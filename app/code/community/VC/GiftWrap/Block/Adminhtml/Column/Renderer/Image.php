<?php
class VC_GiftWrap_Block_Adminhtml_Column_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function _getValue(Varien_Object $row)
    {
		$value = '';
		if (strlen(trim($row->getImage())) > 0) {
			$value = '<img src="'.Mage::getBaseUrl('media').Mage::getStoreConfig('vc_giftwrap/general/image_folder').'/'.$row->getImage().'" width="100px"/>';		
		}
		return $value;
    }
}
