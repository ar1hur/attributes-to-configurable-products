<?php

  class Ar1hur_Atcp_Block_Adminhtml_Atcp_Catalog_Product_Attributes_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
  {


	  public function __construct() {
		  parent::__construct();

		  $this->_objectId = 'id';
		  $this->_blockGroup = 'atcp';
		  $this->_controller = 'adminhtml_atcp_catalog_product_attributes';		  
		  $this->_mode = 'edit';
		  $this->removeButton('reset');
	  }

	  public function getHeaderText() {
		  return Mage::helper('atcp')->__('Adding Attribute to existing configurable product(s) (atcp)');
	  }

  }

  