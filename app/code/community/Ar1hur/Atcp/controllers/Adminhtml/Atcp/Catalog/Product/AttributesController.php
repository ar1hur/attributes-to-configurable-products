<?php

  class Ar1hur_Atcp_Adminhtml_Atcp_Catalog_Product_AttributesController extends Mage_Adminhtml_Controller_Action
  {


	  public function indexAction() {
		  $attributeValues = $this->_getAttributeValues();
		  $productsValues = $this->_getProductsValues();

		  if( count($attributeValues) == 0 ) {
			  $this->_getSession()->addError(Mage::helper('atcp')->__('No attributes found!'));
		  }

		  if( count($productsValues) == 1 ) { // 1 = 'all products'
			  $this->_getSession()->addError(Mage::helper('atcp')->__('No configurable products found!'));
		  }

		  $this->loadLayout();
		  $block = $this->getLayout()->getBlock('atcp');
		  $block->setAttributeValues($attributeValues);
		  $block->setProductsValues($productsValues);
		  $this->renderLayout();
	  }


	  public function saveAction() {
		  if( $this->getRequest()->isPost() ) {
			  $attributeId = $this->getRequest()->getParam('attribute');
			  $products = $this->getRequest()->getParam('products');

			  if( $products[0] == 'all' ) {
				  $products = $this->_getAllConfigurableProducts();
			  }
			  
			  try {
				  $db = Mage::getSingleton('core/resource')->getConnection('core_write');
				  foreach( $products as $product ) {
					  $productId = is_object($product) ? $product->getEntityId() : $product;

					  $sql = 'INSERT INTO catalog_product_super_attribute (`product_id`, `attribute_id`) VALUES ( '.$productId.', '.$attributeId.' )';
					  $db->query($sql);
				  }
			  } catch( Exception $e ) {				  
				  			  
			  }			  
			  $this->_getSession()->addSuccess(Mage::helper('atcp')->__('Attributes successfully added!'));
			  $this->_redirect('*/*/');
		  }
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