<?php

  class Ar1hur_Atcp_Adminhtml_Atcp_Catalog_Product_AttributesController extends Mage_Adminhtml_Controller_Action
  {


	  public function indexAction() {
		  $attributeValues = $this->_getAttributeValues();
		  $productsValues = $this->_getProductsValues();

		  $this->loadLayout();
		  $block = $this->getLayout()->getBlock('atcp');
		  $block->setAttributeValues($attributeValues);
		  $block->setProductsValues($productsValues);
		  $this->renderLayout();
	  }


	  public function saveAction() {
		  echo "<pre>";
		  print_r($this->getRequest()->getPost());
	  }


	  protected function _getAttributeValues() {
		  $values = array( );

		  $collection = $this->_getAllProductAttributes();
		  foreach( $collection as $item ) {
			  $values[$item->getAttributeId()] = $item->getFrontendLabel();
		  }
		  return $values;
	  }


	  /**
	   * Need attributes names and ids for dropdown in backend
	   * @return catalog/product_attribute_collection 
	   */
	  protected function _getAllProductAttributes() {
		  $collection = Mage::getResourceModel('catalog/product_attribute_collection')
				  ->addFieldToSelect(array( 'frontend_label', 'frontend_input' ))
				  ->addFieldToFilter('is_global', 1)
				  ->addFieldToFilter('is_configurable', 1)
				  ->addFieldToFilter('frontend_input', 'select')
				  ->addVisibleFilter()
				  ->setOrder('frontend_label', 'ASC');

		  return $collection;
	  }


	  protected function _getProductsValues() {
		  $values[] = array(
			  'value' => 'all',
			  'label' => Mage::helper('atcp')->__('All')
		  );

		  $collection = $this->_getAllConfigurableProducts();
		  foreach( $collection as $item ) {
			  $values[] = array(
				  'value' => $item->getEntityId(),
				  'label' => $item->getName()
			  );
		  }

		  return $values;
	  }


	  protected function _getAllConfigurableProducts() {
		  $collection = Mage::getModel('catalog/product')->getCollection()
				  ->addAttributeToSelect('name')
				  ->addAttributeToFilter('type_id', 'configurable')
				  ->addAttributeToSort('name', 'ASC');

		  return $collection;
	  }

  }