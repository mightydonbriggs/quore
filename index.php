<?php

    require_once("./dboinit.php");
    print "<pre>\n";
//    print_r($_SERVER);
//    print_r($_SESSION);
    $view = new DBO\View('property_index.phtml');
//    print "</pre>\n";

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
            $view->buttonPanel = new \Quore\ButtonPanel();
            break;
        
        case 'edit':
            $id = chop(intval($_REQUEST['id']));
            $objProperty = new \Quore\Property;
            $propertyRec = $objProperty->getFieldValueArray($_REQUEST['id']);
            $propertyForm = new Quore\PropertyForm;
//            $propertyForm->setValueByName('name', 'testing');
            $propertyForm->setValuesByArray($propertyRec);
            $view->setTemplate('property_edit.phtml');
            $view->propertyForm = $propertyForm;           
            break;
    default:
        break;
}
    $view->content = $content;
print "</pre>";
    $view->render();

?>
