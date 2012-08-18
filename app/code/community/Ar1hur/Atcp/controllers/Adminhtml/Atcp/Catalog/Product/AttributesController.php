<?php

  class Ar1hur_Atcp_Adminhtml_Atcp_Catalog_Product_AttributesController extends Mage_Adminhtml_Controller_Action
  {


	  public function indexAction() {
		  $attributeValues = $this->_getAttributeValues();

		  if( count($attributeValues) == 0 ) {
			  $this->_getSession()->addError(Mage::helper('atcp')->__('No attributes found!'));
		  }

		  $this->loadLayout();
		  $block = $this->getLayout()->getBlock('atcp');
		  $block->setAttributeValues($attributeValues);		  
		  $this->renderLayout();
	  }


	  public function saveAction() {
		  if( $this->getRequest()->isPost() ) {
			  $value = $this->getRequest()->getParam('attribute');
			  $value = explode('|', $value);
			  
			  $attributeSetId = $value[0];
			  $attributeId = $value[1];
			  
			  $products = $this->getRequest()->getParam('products');

			  if( $products[0] == 'all' ) {
				  $products = $this->_getAllConfigurableProducts($attributeSetId);
			  }

			  try {
				  $db = Mage::getSingleton('core/resource')->getConnection('core_write');
				  foreach( $products as $product ) {
					  $productId = is_object($product) ? $product->getEntityId() : $product;

					  $sql = 'INSERT INTO catalog_product_super_attribute (`product_id`, `attribute_id`) VALUES ( '.$productId.', '.$attributeId.' )';
					  $db->query($sql);
						
						$sql = 'INSERT INTO catalog_product_super_attribute_label(`product_super_attribute_id`, `value`) VALUES ( '.$db->lastInsertId().', "'.$value[2].'")';
						$db->query($sql);
				  }
			  } catch( Exception $e ) {
				  
			  }
			  $this->_getSession()->addSuccess(Mage::helper('atcp')->__('Attributes successfully added!'));
			  $this->_redirect('*/*/');
		  }
	  }


	  public function productsAction() {
		  $value = $this->getRequest()->getParam('value');
		  $value = explode('-', $value);
		  
		  $options = $this->_getMultiselectOptions($value[0]);
		  $html = '';
		  foreach( $options as $option ) {
			  $html .= "<option value=\"{$option['value']}\">{$option['label']}</option>\n";
		  }
		  echo $html;
	  }


	  protected function _getAttributeValues() {
		  $values = array( );
		  $values[0] = Mage::helper('atcp')->__('- Please choose -');

		  $collection = $this->_getAllProductAttributes();
		  foreach( $collection as $item ) {
			  $values[$item->getAttributeSetId().'|'.$item->getAttributeId().'|'.$item->getFrontendLabel()] = $item->getFrontendLabel();
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
		  // to get attribute set id 
		  $collection->getSelect()->join('eav_entity_attribute', 'eav_entity_attribute.attribute_id = main_table.attribute_id', 'attribute_set_id');

		  return $collection;
	  }
	


	  protected function _getMultiselectOptions($attributeSetId = null) {
		  $values[] = array(
			  'value' => 'all',
			  'label' => Mage::helper('atcp')->__('All [shortcut]')
		  );

		  $collection = $this->_getAllConfigurableProducts($attributeSetId);
		  foreach( $collection as $item ) {
			  $values[] = array(
				  'value' => $item->getEntityId(),
				  'label' => $item->getName()
			  );
		  }

		  return $values;
	  }


	  protected function _getAllConfigurableProducts($attributeSetId = null) {
		  $collection = Mage::getModel('catalog/product')->getCollection()
				  ->addAttributeToSelect('name')
				  ->addAttributeToFilter('type_id', 'configurable')
				  ->addAttributeToSort('name', 'ASC');

		  if( !is_null($attributeSetId) && is_numeric($attributeSetId) ) {
			  $collection->addAttributeToFilter('attribute_set_id', $attributeSetId);
		  }

		  return $collection;
	  }

  }