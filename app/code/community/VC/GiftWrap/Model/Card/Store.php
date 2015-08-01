<?php
class VC_GiftWrap_Model_Card_Store extends Mage_Core_Model_Abstract
{
    public function _construct() {
        parent::_construct();
        $this->_init('vc_giftwrap/card_store', null);
    }
	
    public function deleteByCardId($cardId = 0)
    {
        $this->_getResource()->beginTransaction();
        try {
            $this->_beforeDelete();
            $this->_getResource()->deleteByCardId($this, $cardId);
            $this->_afterDelete();
            $this->_getResource()->commit();
            $this->_afterDeleteCommit();
        }
        catch (Exception $e){
            $this->_getResource()->rollBack();
            throw $e;
        }
        return $this;
    }
	
}
