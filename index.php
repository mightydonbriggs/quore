<?php

    require_once("./dboinit.php");
    print "<pre>\n";
//    print_r($_SERVER);
//    print_r($_SESSION);
    print "</pre>\n";

    //Set action that will be exicuted
    if(!isset($_REQUEST['action']) || ($_REQUEST['action'] == '')) {
        $_REQUEST['action'] = 'index';
    } else {
        $_REQUEST['action'] = trim($_REQUEST['acton']);
    }
    $action = $_REQUEST['action'];
    
    /*
     * This next block of code will branch on weither we are viewing the property
     * list or the record detail. This code would normally be handled by a
     * controller object. Howerer, I have not yet written the code to handle said
     * controllers. That would make the code much cleaner. 
     */
    if($action == 'index') {
        $propList = new \Quore\PropertyList;
        $table = new \Quore\PropertyListTable($propList->getRecArray());
        $content = $table->getHTML();
//        print_r($propList);
    }

?>
<html>
    <head>
        <title>Quore Tech Test</title>
        <link type="text/css" rel="stylesheet" href="css/quore.css" />
    </head>
<body>
    <div id='container'>
        <div id='header'>
            <h1 class='title'>Quore Tech Test</h1>
        </div>
        <div id='navbar'>
            <br>
            List<br>
            Details<br>
            <br>
           
        </div>
        <div id='content'>
            <?php echo $content; ?>
        </div>
    </div>    
</body>    
</html>