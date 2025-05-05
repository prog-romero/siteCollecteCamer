<?php
  namespace Phppot;
  use Phppot\Message;
  ini_set('display_errors', '1');
  ini_set('display_startup_errors', '1');
  error_reporting(E_ALL);
//   echo $_POST['checked'] ;

require_once __DIR__ . '/lib/DataSource.php';
$ds = new DataSource();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  if (isset($_POST['checked']) && isset($_POST['id'])) {

    // echo "bonnnnjour" ;
    if ($_REQUEST['checked'] == "checked") {
        $check = 1;
    } else {
        $check = 0;
    }

    $q = 'UPDATE message set valide =  ? WHERE id = ?';
    $paramType = 'ii';
    	 $paramValue = array(
            $check ,
            $_REQUEST['id']
                );
         $r = $ds->update($q, $paramType, $paramValue);

         echo json_encode(array("abc"=>'successfuly update'));

    }

}

?>