<?php
class VC_GiftWrap_Block_Adminhtml_Box_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	public function __construct() {
		parent::__construct();
		$this->setId('boxGrid');
		$this->setDefaultSort('box_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}
	
    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }
	
	
	/**
	@ method : _prepareCollection
	**/
	
	protected function _prepareCollection() {
		//$this->setSaveParametersInSession(false);
		$collection = Mage::getModel('vc_giftwrap/box')->getCollection();
		//$storeId = $this->getParam('store', null);
		
		$storeId = $this->getRequest()->getParam('store', null);
		if ($storeId > 0) {
			$collection->getSelect()
			->joinInner(
				array('ps' => Mage::getSingleton('core/resource')->getTableName('vc_giftwrap/box_store')),
				'ps.box_id = main_table.box_id AND ps.store_id = '. $storeId,
				array('ps.store_id AS store_id')
			);
		}
		
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	
	/**
	@ method : _prepareColumns
	**/
	
	protected function _prepareColumns() {
		$this->addColumn('box_id', array(
		  'header'    => Mage::helper('vc_giftwrap')->__('ID'),
		  'align'     =>'right',
		  'width'     => '50px',
		  'index'     => 'box_id',
		));

		$this->addColumn('image', array(
		  'header'    => Mage::helper('vc_giftwrap')->__('Image'),
		  'align'     =>'left',
		  'index'     => 'image',
		  'renderer' => 'vc_giftwrap/adminhtml_column_renderer_image',
		  'filter' => false
		));
		
		
		$this->addColumn('title', array(
		  'header'    => Mage::helper('vc_giftwrap')->__('Title'),
		  'align'     =>'left',
		  'index'     => 'title',
		));
		
		$store = $this->_getStore();
		
		$this->addColumn('price', array(
		  'header'    => Mage::helper('vc_giftwrap')->__('Price'),
		  'align'     =>'left',
		  'index'     => 'price',
		'type'  => 'price',
		'currency_code' => $store->getBaseCurrency()->getCode(),
		  
		));
		
				
		$this->addColumn('created_at', array(
		  'header'    => Mage::helper('vc_giftwrap')->__('Created At'),
		  'align'     =>'left',
		  'width'     => '120px',
		  'index'     => 'created_at',
		  'type' => 'datetime',
		 'gmtoffset' => true,
		 'default'   => ' -- '
		  
		));
		
		$this->addColumn('updated_at', array(
		  'header'    => Mage::helper('vc_giftwrap')->__('Updated At'),
		  'align'     =>'left',
		  'width'     => '120px',
		  'index'     => 'updated_at',
		  'type' => 'datetime',
		 'gmtoffset' => true,
		 'default'   => ' -- '
		  
		));
		
		
		
		$this->addColumn('is_active', array(
		  'header'    => Mage::helper('vc_giftwrap')->__('Status'),
		  'align'     => 'left',
		  'width'     => '80px',
		  'index'     => 'is_active',
		  'type'      => 'options',
		  'options'   => array(
			  1 => 'Enabled',
			  2 => 'Disabled',
		  ),
		));
		
		$this->addColumn('action',
			array(
				'header'    =>  Mage::helper('vc_giftwrap')->__('Action'),
				'width'     => '100px',
				'type'      => 'action',
				'getter'    => 'getId',
				'actions'   => array(
					array(
						'caption'   => Mage::helper('vc_giftwrap')->__('Change Status'),
						'url'       => array('base'=> '*/*/status'),
						'field'     => 'id'
					),
				
					array(
						'caption'   => Mage::helper('vc_giftwrap')->__('Edit'),
						'url'       => array('base'=> '*/*/edit'),
						'field'     => 'id'
					),
					array(
						'caption'   => Mage::helper('vc_giftwrap')->__('Delete'),
						'url'       => array('base'=> '*/*/delete'),
						'field'     => 'id'
					)
				),
				'filter'    => false,
				'sortable'  => false,
				'index'     => 'stores',
				'is_system' => true,
		));
		
		
		return parent::_prepareColumns();
	}
	
	/**
	@ method : _prepareMassaction
	**/
	
	protected function _prepareMassaction() {
		$this->setMassactionIdField('box_id');
		$this->getMassactionBlock()->setFormFieldName('vc_giftwrap');
		
		$this->getMassactionBlock()->addItem('delete', array(
			 'label'    => Mage::helper('vc_giftwrap')->__('Delete'),
			 'url'      => $this->getUrl('*/*/massDelete'),
			 'confirm'  => Mage::helper('vc_giftwrap')->__('Are you sure?')
		));
		
		$statusAr = Mage::getSingleton('vc_giftwrap/status')->getOptionArray();
		array_unshift($statusAr, array('label' => '', 'value' =>  ''));
		$this->getMassactionBlock()->addItem('status', array(
			 'label' => Mage::helper('vc_giftwrap')->__('Change status'),
			 'url'  => $this->getUrl('*/*/massStatus', array('_current' => true)),
			 'additional' => array(
					'visibility' => array(
						 'name' => 'status',
						 'type' => 'select',
						 'class' => 'required-entry',
						 'label' => Mage::helper('vc_giftwrap')->__('Status'),
						 'values' => $statusAr
					 )
			 )
		));
		
		return $this;
	}
	
	/**
	@ method : getRowUrl
	**/
	
	public function getRowUrl($row) {
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}
	
}