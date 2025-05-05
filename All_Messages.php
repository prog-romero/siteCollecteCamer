<?php
  use Phppot\Message;
  session_start();
  ini_set('display_errors', '1');
  ini_set('display_startup_errors', '1');
  error_reporting(E_ALL);
  if (isset($_SESSION["username"])) 
  {
    $user = $_SESSION["username"];
	$id = $_SESSION["id"];
    require_once './Model/Message.php';
    $message = new Message();
    $result = $message->GetAllMessages($id);
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
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                   
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" >
    
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css"> 

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.css"integrity="-8bHTC73gkZ7rZ7vpqUQThUDhqcNFyYi2xgDgPDHc+GXVGHXq+xPjynxIopALmOPqzo9JZj0k6OqqewdGO3EsrQ==" crossorigin="anonymous" />
    
    <title> Messages </title>
    
    <link href="assets/css/phppot-style.css" type="text/css" rel="stylesheet" />

    <link href="assets/css/user-registration.css" type="text/css" rel="stylesheet" />
</head>

    <BODY>
    <div class="phppot-container" style = "margin-top: 30px;">
		<div class="page-header">
    <span class="login-signup"><a href="logout.php" style="font-size: 16px ;">Logout</a></span>
		</div>
		<div class="page-content">  <h3> Bienvenue <?php echo $user; ?> </h3> </div>
	</div>  
    

        
    
    <div class="container">

    <?php echo $result ?>
 
    </div>
     

    
<script>
    $(document).ready(function () {
        $('#example').DataTable({
            pagingType: 'full_numbers',
        });

        $("input.mycheckbox").change(function () {

            var id = $(this).attr("value");
            if (this.checked){
                // alert($(this).attr("value"));
                var check = "checked";
            }
            else{
                // alert($(this).attr("value"));
                var check = "unchecked";
            }

            $.ajax({
                url: "valid.php",
                dataType: "json",
                data: {'checked' : check, "id" : id},
                type: "POST",
                success: function(result){
                    // alert(result.abc);
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });

            // alert("sucessss");


            
        })
    });
 
</script>

</BODY>
</HTML>
    