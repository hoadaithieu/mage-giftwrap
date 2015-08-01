<?php
class VC_GiftWrap_Model_Resource_Wrap extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('vc_giftwrap/wrap', 'id');
    }
	
	/*
	public function getQuantityByQuoteItemId(Mage_Core_Model_Abstract $object, $value = '') {

        $read = $this->_getReadAdapter();
        if ($read && strlen($value) > 0) {
            
			$field  = $this->_getReadAdapter()->quoteIdentifier(sprintf('%s.%s', $this->getMainTable(), 'quote_item_id'));
			$select = $this->_getReadAdapter()->select()
				->from($this->getMainTable())
				->where($field . '=?', $value);

			
            //$data = $read->fetchRow($select);
            //if ($data) {
             //   return false;
            //}
			
			
			$countSelect = clone $select;
			$countSelect->reset(Zend_Db_Select::ORDER);
			$countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
			$countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
			$countSelect->reset(Zend_Db_Select::COLUMNS);
	
			$countSelect->columns('SUM(qty)');			
        }
		return 0;
	}
	*/
	public function getQuantityByQuoteItemId(Mage_Core_Model_Abstract $object, $itemId) {

        $read = $this->_getReadAdapter();
        if ($read && $itemId > 0) {
            
			$field  = $this->_getReadAdapter()->quoteIdentifier(sprintf('%s.%s', $this->getMainTable(), 'quote_item_id'));
			$select = $this->_getReadAdapter()->select()
				->from($this->getMainTable())
				->where($field . '=?', $itemId);

			$countSelect = clone $select;
			$countSelect->reset(Zend_Db_Select::ORDER);
			$countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
			$countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
			$countSelect->reset(Zend_Db_Select::COLUMNS);
			$countSelect->columns('SUM(qty)');
			return $read->fetchOne($countSelect);
					
        }
		return 0;
	}
	
}
