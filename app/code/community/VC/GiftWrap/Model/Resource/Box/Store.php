<?php
class VC_GiftWrap_Model_Resource_Box_Store extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('vc_giftwrap/box_store', null);
    }
	
    public function deleteByBoxId(Mage_Core_Model_Abstract $object, $boxId = 0)
    {
        $this->_beforeDelete($object);
        $this->_getWriteAdapter()->delete(
            $this->getMainTable(),
            $this->_getWriteAdapter()->quoteInto('box_id =?', $boxId)
        );
        $this->_afterDelete($object);
        return $this;
    }
}
