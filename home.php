<?php
use Phppot\Message;
session_start();
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
if (isset($_SESSION["username"])) {

  if ($_SESSION["type"] != "Approvisioneur") {
    $url = "./home2.php";
    header("Location: $url");

  }
    $username = $_SESSION["username"];
    $id = $_SESSION["id"];
    session_write_close();
    if (! empty($_POST["save"])) {
        require_once './Model/Message.php';
        $message = new Message();
        $registrationResponse = $message->registerMessage();
    }
} else {
    
    session_write_close();
    $url = "./index.php";
    header("Location: $url");
}

?>
<HTML>
<HEAD>
<TITLE>Ajout</TITLE>
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
    <span class="return"><a href="ajout.php"> <i class="fa fa-2x fa-arrow-left"></i> </a></span>
		<div class="page-content">  <h3> Bienvenue <?php echo $username; ?> </h3> </div>
	</div>  
    

    <div class="container">

<div class="row justify-content-center">

  <div class="col-md-8">

  <div class="card text-center mt-5">
    <div class="card-body">
      
      <form action="" method="POST"  onsubmit="return MessageValidation()">
          
        <div class="form-group">
          <label for="text" class="fst-italic font-weight-bold">Ajoutez un Commentaire</label>

          <?php
    if (! empty($registrationResponse["status"])) {
        ?>
                    <?php
        if ($registrationResponse["status"] == "error") {
            ?>
				    <div class="server-response error-msg"><?php echo $registrationResponse["message"]; ?></div>
                    <?php
        } else if ($registrationResponse["status"] == "success") {
            ?>
                    <div class="server-response success-msg"><?php echo $registrationResponse["message"]; ?></div>
                    <?php
        }
        ?>
				<?php
    }
    ?>

          <div class="form-label">
			<span class="required error fst-italic" id="text-info" style = " font-size : 11px;"></span>
		 </div>

          <textarea class="form-control" id="text" name="text" aria-describedby="commentaire"  rows="8" placeholder="Saisir le commentaire ici ..."></textarea>
          <small id="commentaire" class="form-text text-muted">
                un commentaire doit faire 200 mots maximum <br/> 
                Mots utilisés : <span id="display_count">0</span>.  Mots restant : <span id="word_left">200</span>
              </small>
        </div>

        <div class="form-group">
          <input  type="submit" name="save"  id="save"  value="Ajouter" class="btn btn-primary">
        </div>

      </form>
  </div>
        
  </div>

</div>

</div>


<script>

$(document).ready(function() {
  $("#text").on('keyup', function() {
    var words = 0;

    if ((this.value.match(/\S+/g)) != null) {
      words = this.value.match(/\S+/g).length;
    }

    if (words > 200) {
      // Split the string on first 200 words and rejoin on spaces
      var trimmed = $(this).val().split(/\s+/, 100).join(" ");
      // Add a space at the end to make sure more typing creates new words
      $(this).val(trimmed + " ");
    }
    else {
      $('#display_count').text(words);
      $('#word_left').text(200-words);
    }
  });
}); 


function MessageValidation() {
	var valid = true;

	$("#text").removeClass("error-field");

    $("#text-info").html("").hide();

	var Text = $("#text").val();

	if (Text.trim() == "") {
		$("#text").addClass("error-field");
        $("#text-info").html("Veillez saisir le text ").css("color", "#ee0000").show();
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
