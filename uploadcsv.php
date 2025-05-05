<?php
use Phppot\Message;
session_start();
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require_once './Model/Message.php';

if (isset($_SESSION["username"])) 
{
    $username = $_SESSION["username"];
    $id = $_SESSION["id"];
    if (isset($_POST["Ajouter"]) && $_POST["Ajouter"] == "Ajouter" ) {

        $message = new Message();
        $response = $message->saveCSV();
        if(! empty($response["status"]) ){
    
            $_SESSION['message'] = $response["message"];
            $_SESSION['status']  = $response["status"];

          }
          header('location: uploadcsv.php');
        exit;
    }
session_write_close();
}
else
{
    session_unset();
    session_write_close();
    $url = "./index.php";
    header("Location: $url");
}

?>

<HTML>
    <HEAD>
    <TITLE>Ajout CSV</TITLE>
    <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                     
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
    <span class="return"><a href="ajout.php"> <i class="fa fa-2x fa-arrow-left"></i> </a></span>
		<div class="page-content">  <h3> Bienvenue <?php echo $username; ?> </h3> </div>
	</div>  
    

        
    
        <div class="container">

            <div class="row justify-content-center">
                

            <?php
                if (! empty($_SESSION["status"])) {
                ?>
                    <?php
                    if ($_SESSION["status"] == "error") {
                    ?>
                                <div class="server-response error-msg"><?php echo $_SESSION['message']; ?></div>
                    <?php
                    } else if ($_SESSION["status"] == "success") {
                        ?>
                        <div class="server-response success-msg"><?php echo $_SESSION["message"]; ?></div>
                        
                    <?php
                    }
                    ?>
                    
                <?php
                    session_start();
                    $_SESSION['message'] = "";
                    session_write_close();
                }
            ?>
            
                <div class="col-md-8 justify-content-center " style="text-align : center;">
                    <div class="card text-center mt-5">
                        <div class="card-body justify-content-center">
                            <h4 class="card-title">Chosir un fichier .csv: </h4>
							<h5 style='color:maroon; margin:0;'><b style='text-decoration:underline;'>NB</b> :Le Fichier doit avoir une colone <b >id(id du message)</b> et <b>text(le message en question)</b></h5>
                             
                                <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" onsubmit="return ChoiceFile()" method="post" enctype="multipart/form-data">

                                <div class="form-group" style="margin-top: 20px;">
                                
                                    <input type="file" name="file" id="file" /> <br/> 
                                    <span class="required error fst-italic" id="choicefile" style = " font-size : 11px;"></span>

                                </div>

                                <div class="form-group justify-content-center text-center" style="margin-top: 10px; display : inline-block ; float : none; margin : 0 auto">
                                    <div class="form-label fst-italic font-weight-bold" style = "text-align : center;">
                                    *Donnez la source:<br/> <span class="required error fst-italic" id="lasource" style = " font-size : 11px;"></span>
                                    </div>
                                    <input class="form-control" type="text" name="source" id="source" style="height : 35px; width : 110%;"/> 
                                     
                                </div>

                                <div class="form-group" style = "margin-top : 20px;">
                                    <input  type="submit" name="Ajouter"  id="Ajouter"  value="Ajouter" class="btn btn-primary">
                                </div>

                                </form>
                                
                    </div>
                </div>
            
            </div>
             
    </div>
     
    
    


<script>

    function ChoiceFile() 
    {
        var valid = true;
        $("#choicefile").html("").hide();
        $("#text_expli").html("").hide();
	    $("#source").removeClass("error-field");

	    var Text = $("#source").val();

        if ($('#file').get(0).files.length === 0) 
        {
            $("#choicefile").html("Veillez choisir un fichier ").css("color", "#ee0000").show();
            valid = false;
        }
        if (Text.trim() == "") 
        {
            $("#source").addClass("error-field");
            $("#lasource").html("Veillez remplir").css("color", "#ee0000").show();
            valid = false;
	    }

        if (valid == false) {
		$('.error-field').first().focus();
		valid = false;
	    }
        return valid;
    }
</script>

</BODY>
</HTML>
    