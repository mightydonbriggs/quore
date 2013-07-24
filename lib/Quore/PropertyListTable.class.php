<?php

namespace Quore;

class PropertyListTable extends \DBO\Tableizer
{
    protected $_columnTitles = array('name'   => 'Name', 
                                     'brand'  => 'Brand',
                                     'region' => 'Region',
                                     'phone'  => 'Phone',
                                     'serviceType' => 'Service Type',
                                     'url'    => 'Website');
    
    public function __construct($recordArray = null) {
        parent::__construct($recordArray);
        $this->setTitle("Property Listing");
        $this->setTableClass('propertyList');
        $this->setTableTitleClass('propertyListTitle');
        $this->setColumnTitles($this->_columnTitles);
//        print "<pre>";
//        print_r($this);
    }
}
?>
