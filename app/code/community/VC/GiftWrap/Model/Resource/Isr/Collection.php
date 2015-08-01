<?php
class VC_GiftWrap_Model_Resource_Isr_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    /**
     * Define resource model
     *
     */
    protected function _construct()
    {
        $this->_init('vc_giftwrap/isr', null);
    }

}
