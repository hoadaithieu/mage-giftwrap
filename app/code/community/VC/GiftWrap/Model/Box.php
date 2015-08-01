<?php
class VC_GiftWrap_Model_Box extends Mage_Core_Model_Abstract
{
    public function _construct() {
        parent::_construct();
        $this->_init('vc_giftwrap/box', 'box_id');
    }
}
