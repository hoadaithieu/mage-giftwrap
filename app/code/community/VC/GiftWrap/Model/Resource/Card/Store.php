<?php
class VC_GiftWrap_Model_Resource_Card_Store extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('vc_giftwrap/card_store', null);
    }
	
    public function deleteByCardId(Mage_Core_Model_Abstract $object, $cardId = 0)
    {
        $this->_beforeDelete($object);
        $this->_getWriteAdapter()->delete(
            $this->getMainTable(),
            $this->_getWriteAdapter()->quoteInto('card_id =?', $cardId)
        );
        $this->_afterDelete($object);
        return $this;
    }
}
