<?php
class VC_GiftWrap_Model_Card extends Mage_Core_Model_Abstract
{
    public function _construct() {
        parent::_construct();
        $this->_init('vc_giftwrap/card', 'card_id');
    }
}
