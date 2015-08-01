<?php
class VC_GiftWrap_Model_Resource_Card extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('vc_giftwrap/card', 'card_id');
    }
	
}
