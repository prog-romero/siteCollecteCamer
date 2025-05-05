<?php
  use Phppot\Message;
  session_start();
  ini_set('display_errors', '1');
  ini_set('display_startup_errors', '1');
  error_reporting(E_ALL);
  if (isset($_SESSION["username"])) 
  { 
    $username = $_SESSION["username"];
    $id = $_SESSION["id"];
    if ($_SESSION["type"] == "Approvisioneur") {
      $url = "./ajout.php";
      header("Location: $url"); 

    }
    // $_SESSION['message']["read"];
    require_once './Model/Message.php';
    $message = new Message();
    $m = $message->RandomGetMessage(htmlspecialchars($_GET["id_message"]));
    if (isset($_POST["save"]))
    {
      $response  = $message->voteMessage(htmlspecialchars($_GET["id_message"]));
      if(!empty($response["status"])){
        $_SESSION['message'] = $response["message"];
        $_SESSION['status']  = $response["status"];
      }
	
	$res = $message->GetNextMessage($id);
	if($res != "no"){
		$_GET['id_message'] = $res[0]["id"];
		header("location:home2.php?id_message=".$_GET['id_message']);
	}else{
		header("location:All_Messages.php");
	}
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                   
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css"> 

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.css"integrity="-8bHTC73gkZ7rZ7vpqUQThUDhqcNFyYi2xgDgPDHc+GXVGHXq+xPjynxIopALmOPqzo9JZj0k6OqqewdGO3EsrQ==" crossorigin="anonymous" />
    
    <title> Vote </title>
    
    <link href="assets/css/phppot-style.css" type="text/css" rel="stylesheet" />

    <link href="assets/css/user-registration.css" type="text/css" rel="stylesheet" />
</head>

<body>

  <div class="phppot-container" style = "margin-top: 30px;">
		<div class="page-header">
			<span class="login-signup"><a href="logout.php" style="font-size: 16px ;">Logout</a></span>
		</div>
		<span class="return"><a href="All_Messages.php"> <i class="fa fa-2x fa-arrow-left"></i> </a></span>
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

    <div class="col-md-8">

      <div class="card text-center mt-2">
      <div class="card-body">
      <h5 class="card-title">Texte : </h5>

      <?php
        if (! empty($m[0])) { 
      ?>
        <p class="card-text fst-italic " style="font-size: 18px;"><?php echo $m[0]["text"]; ?> </p>
      <?php }  

        else { ?>      
        <p class="card-text fst-italic " style="font-size: 16px; color: red;"> -- Pas de commentaires disponible pour l'instant --</p>
      <?php }  ?>
        <form action="" method="POST"  onsubmit="return VoteValidation()">
            <div class="form-group">
            
              <div class="form-label fst-italic font-weight-bold" style = "text-align : center;">
                *Vote : <br/> <span class="required error fst-italic" id="text_vote" style = " font-size : 11px;"></span>
              </div>

              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="vote" id="exampleRadios1" value="1">
                <label class="form-check-label" for="exampleRadios1">
                  Haineux
                </label>
              </div>

              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="vote" id="exampleRadios2" value="0">
                <label class="form-check-label" for="exampleRadios2">
                  Non Haineux
                </label>
              </div>

              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="vote" id="exampleRadios3" value="2">
                <label class="form-check-label" for="exampleRadios3">
                  Je ne sais pas 
                </label>
              </div>

            </div>
				<div class="form-group" style="margin-top: 25px;">
					<div class="form-label fst-italic font-weight-bold" style = "text-align : center;">
					*Expliquez le choix(Entrez juste les mots clés ou entrez "RAS" si n'avez pas d'explication) :<br/> <span class="required error fst-italic" id="text_expli" style = " font-size : 11px;"></span>
				</div>
			        
			    <textarea class="form-control" id="explication"  name="explication"  aria-describedby="commentaire"  rows="6" placeholder="Saisir ici ..."></textarea>
			    <small id="commentaire" class="form-text text-muted " >
				  expliquez en quelques mots votre choix (4 mots maximum) <br/> 
				  Mots utilisés : <span id="display_count">0</span>.  Mots restant : <span id="word_left">4</span>
			    </small>
            </div>

            <?php
            if (! empty($m[0])) 
            { 
              ?>
              <div class="form-group">
                <input  type="submit" name="save" class="btn btn-primary" value="Enregistrer" >
              </div>
              <?php 
            }  
            else 
            { 
              ?>  
              <div class="form-group">
                <input  type="submit" name="save" class="btn btn-primary" value="Enregistrer" disabled>
              </div> 
              <?php 
            } ?> 

          </form>
        </div>
        <!-- <div class="card-footer text-muted " style="height: 40px;">
          2 days ago
        </div> -->
      </div>
            
      </div>

    </div>

  </div>



  <script>
function updateExplicationVisibility(voteValue) {
    if (voteValue === "1") {
        $("#explication").closest('.form-group').show();
    } else {
        $("#explication").closest('.form-group').hide();
    }
}

$("input[name='vote']").on("change", function () {
    const vote = $(this).val();
    updateExplicationVisibility(vote);
});

$(document).ready(function () {
    // Masquer le champ explication par défaut au chargement
    $("#explication").closest('.form-group').hide();

    $("#explication").on('keyup', function () {
        var words = 0;
        if ((this.value.match(/\S+/g)) != null) {
            words = this.value.match(/\S+/g).length;
        }

        if (words > 4) {
            var trimmed = $(this).val().split(/\s+/, 4).join(" ");
            $(this).val(trimmed + " ");
        } else {
            $('#display_count').text(words);
            $('#word_left').text(4 - words);
        }
    });
});

function VoteValidation() {
    var valid = true;

    $("#explication").removeClass("error-field");
    $("#text_expli").html("").hide();
    $("#text_vote").html("").hide();

    var Text = $("#explication").val();
    var vote = $('input[name="vote"]:checked').val();

    if (!vote) {
        $("#text_vote").html("Veuillez choisir").css("color", "#ee0000").show();
        valid = false;
    }

    // Seul vote = 1 (Haineux) nécessite une explication
    if (vote === "1" && Text.trim() === "") {
        $("#explication").addClass("error-field");
        $("#text_expli").html("Veuillez remplir").css("color", "#ee0000").show();
        valid = false;
    }

    if (!valid) {
        $('.error-field').first().focus();
    }

    return valid;
}
</script>


</body>
</html>