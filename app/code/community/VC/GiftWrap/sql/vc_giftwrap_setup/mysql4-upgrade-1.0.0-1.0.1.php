<?php
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
//$installer = $this;
$installer = new Mage_Catalog_Model_Resource_Eav_Mysql4_Setup('core_setup');
$installer->startSetup();

// is product used for recurring payments
$installer->addAttribute('catalog_product', 'is_wrapable', array(
    'group'             => 'General',
    'type'              => 'int',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'Enable Wrapable Product',
    'note'              => 'Add Gift Wrap feature to this product or not.',
    'input'             => 'select',
    'class'             => '',
    'source'            => 'eav/entity_attribute_source_boolean',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => false,
    'default'           => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'unique'            => false,
    'apply_to'          => 'simple,virtual,bundle,configurable,grouped',
    'is_configurable'   => false
));
$installer->endSetup();