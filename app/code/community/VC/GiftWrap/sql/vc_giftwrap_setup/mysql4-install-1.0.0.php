<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();
$installer->run("
CREATE TABLE IF NOT EXISTS `{$this->getTable('vc_giftwrap_box')}` (
	`box_id` int(10) NOT NULL,
	`title` varchar(200) NOT NULL,
	`image` varchar(200) NOT NULL,
	`position` int(10) NOT NULL DEFAULT '0',
	`price` decimal(10,4) NOT NULL DEFAULT '0',
	`created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	`updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	`is_active` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE `{$this->getTable('vc_giftwrap_box')}`
	ADD PRIMARY KEY (`box_id`);

ALTER TABLE `{$this->getTable('vc_giftwrap_box')}`
	MODIFY `box_id` int(10) NOT NULL AUTO_INCREMENT;");
	

$installer->run("
CREATE TABLE IF NOT EXISTS `{$this->getTable('vc_giftwrap_box_store')}` (
	`box_id` int(10) DEFAULT '0',
	`store_id` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `{$this->getTable('vc_giftwrap_box_store')}`
	ADD PRIMARY KEY (`box_id`,`store_id`);

");


$installer->run("
CREATE TABLE IF NOT EXISTS `{$this->getTable('vc_giftwrap_card')}` (
	`card_id` int(10) NOT NULL,
	`title` varchar(200) NOT NULL,
	`max_characters` int(10) NOT NULL DEFAULT '0',
	`image` varchar(200) NOT NULL,
	
	`position` int(10) NOT NULL DEFAULT '0',
	`price` decimal(10,4) NOT NULL,
	`created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	`updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	`is_active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE `{$this->getTable('vc_giftwrap_card')}`
	ADD PRIMARY KEY (`card_id`);

ALTER TABLE `{$this->getTable('vc_giftwrap_card')}`
	MODIFY `card_id` int(10) NOT NULL AUTO_INCREMENT;
");


$installer->run("
CREATE TABLE IF NOT EXISTS `{$this->getTable('vc_giftwrap_card_store')}` (
	`card_id` int(10) DEFAULT '0',
	`store_id` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `{$this->getTable('vc_giftwrap_card_store')}`
	ADD PRIMARY KEY (`card_id`,`store_id`);

");

$installer->run("
CREATE TABLE IF NOT EXISTS `{$this->getTable('vc_giftwrap_wrap')}` (
	`id` int(10) NOT NULL,
	`box_id` int(10) DEFAULT '0',
	`card_id` int(10) DEFAULT '0',
	`product_id` int(10) DEFAULT '0',
	`sku` varchar(200) NOT NULL,
	`quote_item_id` int(10) DEFAULT '0',
	`quote_id` int(10) DEFAULT '0',
	`order_id` int(10) DEFAULT '0',
	`qty` int(10) DEFAULT '0',
	`qty_invoiced` int(10) DEFAULT NULL,
	`qty_shipped` int(10) DEFAULT NULL,
	`qty_refunded` int(10) DEFAULT NULL,
	`price` decimal(10,4) DEFAULT '0.0000',
	`card_message` text NULL,
	`group_id` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE `{$this->getTable('vc_giftwrap_wrap')}`
	ADD PRIMARY KEY (`id`);
ALTER TABLE `{$this->getTable('vc_giftwrap_wrap')}`
	MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

");
	
	
$installer->run("
CREATE TABLE IF NOT EXISTS `{$this->getTable('vc_giftwrap_isr')}` (
	`code` varchar(20) NOT NULL DEFAULT '',
	`entity_id` int(10) NOT NULL DEFAULT '0',
	`order_id` int(10) NOT NULL DEFAULT '0',
	`wrap_id` int(10) NOT NULL DEFAULT '0',
	`qty` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `{$this->getTable('vc_giftwrap_isr')}`
	ADD PRIMARY KEY (`code`,`order_id`,`entity_id`,`wrap_id`);

");
	

$installer->endSetup(); 

