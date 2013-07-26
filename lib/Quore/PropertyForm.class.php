<?php

namespace Quore;

class PropertyForm extends \DBO\AbstractForm
{
    protected $_formName      = 'propertyEdit';
    
    public function __construct() {
        parent::__construct($this->_formName);
        $this->_init();
    }
    
    private function _init() {
          $this->setId('propertyEditForm')
                ->setClass('editForm');
          
          $input_name = new \DBO\FormElementInput();
          $input_name->setName('name')
                  ->setClass('formInput')
                  ->setId('txtName')
                  ->setLabel('Name:');
          $this->addElement($input_name);

          $input_region = new \DBO\FormElementInput();
          $input_region->setName('region_id')
                  ->setClass('formInput')
                  ->setId('txtRegionId')
                  ->setLabel('Region:');
          $this->addElement($input_region);

          $input_brand = new \DBO\FormElementInput();
          $input_brand->setName('brand')
                  ->setClass('formInput')
                  ->setId('txtBrand')
                  ->setLabel('Brand:');
          $this->addElement($input_brand);

          $input_phone = new \DBO\FormElementInput();
          $input_phone->setName('phone')
                  ->setClass('formInput')
                  ->setId('txtPhone')
                  ->setLabel('Phone:');
          $this->addElement($input_phone);

          $input_serviceType = new \DBO\FormElementInput();
          $input_serviceType->setName('isFullService')
                  ->setClass('formInput')
                  ->setId('txtServiceType')
                  ->setLabel('Service Type:');
          $this->addElement($input_serviceType);

          $input_url = new \DBO\FormElementInput();
          $input_url->setName('url')
                  ->setClass('formUrl')
                  ->setId('txtUrl')
                  ->setLabel('Web Site:');
          $this->addElement($input_url);

          $input_id = new \DBO\FormElementInput();
          $input_id->setName('id')
                  ->setInputType('hidden')
                  ->setClass('formInput')
                  ->setId('txtId');
          $this->addElement($input_id);
          
          //-- Add control buttons to form --//
          $btn_add = new \DBO\FormElementSubmit();
          $btn_add->setName('btnSubmit')
                  ->setClass("formButton")
                  ->setId('btnAdd')
                  ->setValue('Add');
          $this->addElement($btn_add);
          
          $btn_submit = new \DBO\FormElementSubmit();
          $btn_submit->setName('btnSubmit')
                  ->setClass("formButton")
                  ->setId('btnSubmit')
                  ->setValue('Save');
          $this->addElement($btn_submit);
          
          $btn_delete = new \DBO\FormElementSubmit();
          $btn_delete->setName('btnSubmit')
                  ->setClass('formButton')
                  ->setId('btnDelete')
                  ->setValue('Delete');
          $this->addElement($btn_delete);
          
          $btn_cancel = new \DBO\FormElementSubmit();
          $btn_cancel->setName('btnSubmit')
                  ->setClass("formButton")
                  ->setId('btnCancel')
                  ->setValue('Cancel');
          $this->addElement($btn_cancel);
    }
}
?>