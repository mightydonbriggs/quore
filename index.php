<?php

    require_once("./dboinit.php");
    print "<pre>\n";
//    print_r($_SERVER);
//    print_r($_SESSION);
//    print_r($_REQUEST);
    $view = new DBO\View('property_index.phtml'); //Set default view

    /* 
     * First handle the submit button. If it has a value, we have
     * some record work to do 
     */
    if(isset($_REQUEST['btnSubmit'])) {
        $btnSubmit = strtolower($_REQUEST['btnSubmit']);

        $objProperty = new \Quore\Property;  //Instanciate a Property object
        switch ($btnSubmit) {
            case 'save':
                $result = $objProperty->saveFromArray($_REQUEST);
                if($result === false) {
                    //Save Failed
                    //@todo Write code to handle save failure
                    print "Save Error!!\n";
                    print_r($objProperty);
                    die;
                } else {
                    //Save succeeded
                    $id = $objProperty->getId();
                    $_REQUEST['id'] = $id;
                }
                $_REQUEST['action'] = 'view';  //Change display from edit to view
                break;
            case 'cancel':
                $_REQUEST['action'] = 'view';  //Change display from edit to view
                break;
            case 'add':
                $_REQUEST['action'] = 'add';
                $columnList = \Quore\Property::getColumnList();
                \DBO\Utilities::clearRequestFields($columnList);
                break;
            case 'delete':
                $_REQUEST['action'] = 'delete';
                
            default:
                break;
        }
    }
    
    //Set action that will be exicuted
    if(!isset($_REQUEST['action']) || ($_REQUEST['action'] == '')) {
        $_REQUEST['action'] = 'index';
    } else {
        $_REQUEST['action'] = trim($_REQUEST['action']);
    }
    $action = $_REQUEST['action'];
    $content = '';
    
    /*
     * This next block of code will branch on weither we are viewing the property
     * list or the record detail. This code would normally be handled by a
     * controller object. Howerer, I have not yet written the code to handle said
     * controllers. That would make the code much cleaner. 
     */
        
    switch ($action) {
        case 'index':
            $propList = new \Quore\PropertyList;
            $table = new \Quore\PropertyListTable($propList->getRecArray());
            $content = $table->getHTML();
            break;
        
        case 'view':
            $id = chop(intval($_REQUEST['id']));
            $objProperty = new \Quore\Property;
            $propertyRec = $objProperty->getFieldValueArray($_REQUEST['id']);
            $view->setTemplate('property_view.phtml');
            $view->propertyRec = $propertyRec;
            break;
        
        case 'edit':
            $id = chop(intval($_REQUEST['id']));
            $objProperty = new \Quore\Property;
            $propertyRec = $objProperty->getFieldValueArray($_REQUEST['id']);
            $propertyForm = new Quore\PropertyForm;
            $propertyForm->setValuesByArray($propertyRec);
            $view->setTemplate('property_edit.phtml');
            $view->propertyForm = $propertyForm;           
            break;
        
        case 'add':
            $objProperty = new \Quore\Property;
            $propertyForm = new Quore\PropertyForm;
            $view->setTemplate('property_edit.phtml');
            $view->propertyForm = $propertyForm;
            break;
        
        case 'delete':
            $id = chop(intval($_REQUEST['id']));
            $objProperty = new \Quore\Property;
            $objProperty->deleteById($id);
            $propList = new \Quore\PropertyList;
            $table = new \Quore\PropertyListTable($propList->getRecArray());
            $content = $table->getHTML();
    default:
        break;
}
    $view->content = $content;
print "</pre>";
    $view->render();

?>
