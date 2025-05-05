<?php
use Phppot\Message;
session_start();
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
if (isset($_SESSION["username"])) 
{
    if ($_SESSION["type"] != "Approvisioneur") {
        $url = "./home2.php";
        header("Location: $url");
  
    }
    $username = $_SESSION["username"];
    $id = $_SESSION["id"];
    $admin = $_SESSION["admin"];
} 
else 
{
    
    session_write_close();
    $url = "./index.php";
    header("Location: $url");
}

?>
<HTML>
    <HEAD>
    <TITLE>Ajout Menu</TITLE>
    <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="stylesheet" href=  {% static 'bootstrap-4.4.1/css/bootstrap.min.css' %} > -->
    <!-- <link rel="stylesheet" href=  {% static 'bootstrap-5.0.2-dist/css/bootstrap.min.css' %} > -->
                        
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
     
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css"> 
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.css"integrity="-8bHTC73gkZ7rZ7vpqUQThUDhqcNFyYi2xgDgPDHc+GXVGHXq+xPjynxIopALmOPqzo9JZj0k6OqqewdGO3EsrQ==" crossorigin="anonymous" />
     
    <link href="assets/css/phppot-style.css" type="text/css"
        rel="stylesheet" />
    <link href="assets/css/user-registration.css" type="text/css"
        rel="stylesheet" />
    </HEAD>
    <BODY>
        <div class="phppot-container" style = "margin-top: 30px;">
            <div class="page-header">
            <span class="login-signup"><a href="logout.php" style="font-size: 16px ;">Logout</a></span>
            </div>
            <div class="page-content">  <h3> Bienvenue  <?php echo $username?> </h3> </div>
        </div>  
        
    
        <div class="container">

            <div class="row justify-content-center">
                
            
                <div class="col-md-8">
                    <div class="card text-center mt-5">
                        <div class="card-body">
                            <h4 class="card-title">Ajout par saisie : </h4>
                            Ajoutez message par message par saisie . </br></br>

                        <a href="home.php"> <i class="fa fa-2x fa-arrow-right"></i> </a>

                        </div>
                         
                            
                    </div>
                </div>
            
            </div>
            
            <div class="row justify-content-center">
                
            
                <div class="col-md-8">
                    <div class="card text-center mt-5">
                        <div class="card-body">
                            <h4 class="card-title font-weight-bold">Ajout par fichier csv : </h4>
                            Ajouter du texte via un fichier csv avec un format précis  . </br></br>

                            <a href="uploadcsv.php"> <i class="fa fa-2x fa-arrow-right"></i> </a>
    
                        </div>
                            
                    </div>
                </div>
            
            </div>

            <?php if ($admin == 1){ ?>
            
            <div class="row justify-content-center">
                
            
                <div class="col-md-8">
                    <div class="card text-center mt-5">
                        <div class="card-body">
                            <h4 class="card-title font-weight-bold"> Page Admin. </h4>
                            Ensemble de fonctionnalité reservés aux administrateurs . </br></br>
                            <a href="admin.php"> <i class="fa fa-2x fa-arrow-right"></i> </a>
    
                        </div>
                            
                    </div>
                </div>
            
            </div>

           <?php }?>



<!--             
            <div class="row justify-content-center">
                
            
                <div class="col-md-8">
                    <div class="card text-center mt-5">
                        <div class="card-body">
                            <h4 class="card-title font-weight-bold"> Ajout via l'API twiter : </h4>
                            Ajout du texte via l'API officiel twiter. Il faudrait avoir un compte developer 
                            et des identifiant . </br></br>

                            <a href=""> <i class="fa fa-2x fa-arrow-right"></i> </a>
    
                        </div>
                            
                    </div>
                </div>
            
            </div> -->
    </div>
     
    
    </BODY>
    </HTML>
    