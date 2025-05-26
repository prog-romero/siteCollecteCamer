<?php
use Phppot\Message;
session_start();
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
if (isset($_SESSION["username"])) 
{
    $user = $_SESSION["username"];
    require_once './Model/Message.php';
    $message = new Message();
    $result = $message->GetAllMessages_Adm();
    // Récupérer les statistiques
    $totalMessages = $message->getTotalMessages();
    $messagesWithVoteFinalThree = $message->getMessagesWithVoteFinalThree();
    $messagesVotedAtLeastOnce = $message->getMessagesVotedAtLeastOnce();
    // Récupérer les 5 votants les plus actifs
    $topFiveVoters = $message->getTopFiveVoters();
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
<html>
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

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.css" integrity="-8bHTC73gkZ7rZ7vpqUQThUDhqcNFyYi2xgDgPDHc+GXVGHXq+xPjynxIopALmOPqzo9JZj0k6OqqewdGO3EsrQ==" crossorigin="anonymous" />
    
    <title>Admin</title>
    
    <link href="assets/css/phppot-style.css" type="text/css" rel="stylesheet" />
    <link href="assets/css/user-registration.css" type="text/css" rel="stylesheet" />
</head>

<body>
    <div class="phppot-container" style="margin-top: 30px;">
        <div class="page-header">
            <span class="login-signup"><a href="logout.php" style="font-size: 16px;">Logout</a></span>
        </div>
        <span class="return"><a href="ajout.php"><i class="fa fa-2x fa-arrow-left"></i></a></span>
        <div class="page-content">  
            <h3>Bienvenue <?php echo $user; ?></h3> 
            <a href="ExportMessageData.php" class="btn btn-success"><i class="dwn"></i> Export Message To CSV</a>
            <a href="ExportVotesData.php" class="btn btn-success"><i class="dwn"></i> Export Votes To CSV</a>
            <button id="showVoters" class="btn btn-primary" style="margin-left: 10px;"><i class="fa fa-users"></i> Afficher les votants</button>
        </div>
        <!-- Affichage des statistiques -->
        <div class="statistics" style="margin-top: 20px;">
            <h4>Statistiques des messages</h4>
            <p><strong>Nombre total de messages :</strong> <?php echo $totalMessages; ?></p>
            <p><strong>Messages avec vote final de 3 :</strong> <?php echo $messagesWithVoteFinalThree; ?></p>
            <p><strong>Messages votés au moins une fois :</strong> <?php echo $messagesVotedAtLeastOnce; ?></p>
            <!-- Affichage des 5 votants les plus actifs -->
            <h4>Top 5 des votants les plus actifs</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nom d'utilisateur</th>
                        <th>Nombre de votes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($topFiveVoters)) : ?>
                        <?php foreach ($topFiveVoters as $voter) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($voter['username']); ?></td>
                                <td><?php echo $voter['vote_count']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="2">Aucun votant trouvé.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <!-- Conteneur pour la liste des votants -->
        <div id="votersList" style="margin-top: 20px; display: none;">
            <h4>Liste des votants</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nom d'utilisateur</th>
                        <th>Nombre de votes</th>
                    </tr>
                </thead>
                <tbody id="votersTableBody">
                </tbody>
            </table>
        </div>
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
            var check = this.checked ? "checked" : "unchecked";

            $.ajax({
                url: "valid.php",
                dataType: "json",
                data: {'checked': check, "id": id},
                type: "POST",
                success: function(result) {
                    // alert(result.abc);
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        });

        // Gestion du clic sur le bouton "Afficher les votants"
        $("#showVoters").click(function() {
            $.ajax({
                url: "get_voters.php",
                dataType: "json",
                type: "GET",
                success: function(result) {
                    var votersTableBody = $("#votersTableBody");
                    votersTableBody.empty(); // Vider le tableau avant de le remplir
                    if (result.length > 0) {
                        $.each(result, function(index, voter) {
                            votersTableBody.append(
                                "<tr>" +
                                "<td>" + voter.username + "</td>" +
                                "<td>" + voter.vote_count + "</td>" +
                                "</tr>"
                            );
                        });
                        $("#votersList").show();
                    } else {
                        votersTableBody.append(
                            "<tr><td colspan='2'>Aucun votant trouvé.</td></tr>"
                        );
                        $("#votersList").show();
                    }
                },
                error: function(xhr, status, error) {
                    alert("Erreur lors de la récupération des votants : " + xhr.responseText);
                }
            });
        });
    });
    </script>
</body>
</html>