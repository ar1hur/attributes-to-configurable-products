<?php

  class Ar1hur_Atcp_Block_Adminhtml_Atcp_Catalog_Product_Attributes_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
  {


	  protected function _prepareForm() {
		  $form = new Varien_Data_Form(array(
					  'id' => 'edit_form',
					  'action' => $this->getUrl('*/*/save'),
					  'method' => 'post',
					  'enctype' => 'multipart/form-data'
				  ));

		  $form->setUseContainer(true);
		  $this->setForm($form);

		  $fieldset = $form->addFieldset('atcp', array(
			  'legend' => Mage::helper('atcp')->__('Adding attribute to product')
				  ));

		  $fieldset->addField('attribute', 'select', array(
			  'label' => Mage::helper('atcp')->__('Attribute'),
			  'class' => 'required-entry',
			  'required' => true,
			  'name' => 'attribute',
			  'values' => $this->getParentBlock()->getAttributeValues(),
			  'note' => Mage::helper('atcp')->__('Choose the Attribute which you will add.')
		  ));

		  $fieldset->addField('products', 'multiselect', array(
			  'label' => Mage::helper('atcp')->__('Product(s)'),
			  'class' => 'required-entry',
			  'required' => true,
			  'values' => $this->getParentBlock()->getProductsValues(),
			  'name' => 'products'
		  ));

		  return parent::_prepareForm();
	  }

  }