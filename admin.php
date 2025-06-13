<?php
use Phppot\Message;
session_start();

// Sécurité et initialisation des données
if (isset($_SESSION["username"]) && isset($_SESSION["admin"]) && $_SESSION["admin"] == 1) 
{
    $user = $_SESSION["username"];
    require_once './Model/Message.php';
    $message = new Message();
    
    // Récupération de TOUTES les données nécessaires pour la page
    $allMessagesTable = $message->GetAllMessages_Adm();
    $totalMessages = $message->getTotalMessages();
    $messagesWithVoteFinalThree = $message->getMessagesWithVoteFinalThree();
    $messagesVotedAtLeastOnce = $message->getMessagesVotedAtLeastOnce();
    $topFiveVoters = $message->getTopFiveVoters();
    
    session_write_close();
} 
else 
{
    // Redirection si l'utilisateur n'est pas un admin connecté
    header("Location: ./index.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panneau d'administration</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" >
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="assets/css/phppot-style.css" type="text/css" rel="stylesheet" />
    <style>
        .phppot-container { max-width: 1200px; margin: 30px auto; }
        .vote-haineux { color: #dc3545; font-weight: bold; }
        .vote-non_haineux { color: #28a745; font-weight: bold; }
        .vote-hesite { color: #ffc107; font-weight: bold; }
    </style>
</head>
<body>
    <div class="phppot-container">
        <div class="page-header d-flex justify-content-between align-items-center">
            <h3>Panneau d'administration - Bienvenue <?php echo htmlspecialchars($user); ?></h3>
            <span class="login-signup"><a href="logout.php" class="btn btn-outline-secondary">Déconnexion</a></span>
        </div>

        <div class="page-content mb-4">
            <a href="ExportMessageData.php" class="btn btn-success"><i class="fa fa-download"></i> Exporter Messages</a>
            <a href="ExportVotesData.php" class="btn btn-success"><i class="fa fa-download"></i> Exporter Votes</a>
            <button id="showVoters" class="btn btn-warning ml-2"><i class="fa fa-users"></i> Afficher/Cacher les votants</button>
        </div>

        <!-- ========================================================== -->
        <!-- SECTION STATISTIQUES ET TOP 5 (RESTAURÉE ET AMÉLIORÉE) -->
        <!-- ========================================================== -->
        <div class="statistics mb-4">
            <div class="row">
                <div class="col-md-6">
                    <h4>Statistiques des messages</h4>
                    <ul class="list-group">
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                        Messages totaux
                        <span class="badge badge-primary badge-pill"><?php echo $totalMessages; ?></span>
                      </li>
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                        Messages votés au moins une fois
                        <span class="badge badge-primary badge-pill"><?php echo $messagesVotedAtLeastOnce; ?></span>
                      </li>
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                        Messages avec 3 votes ou plus
                        <span class="badge badge-primary badge-pill"><?php echo $messagesWithVoteFinalThree; ?></span>
                      </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h4>Top 5 des votants</h4>
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Utilisateur</th>
                                <th>Nb. Votes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($topFiveVoters)): ?>
                                <?php foreach ($topFiveVoters as $voter): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($voter['username']); ?></td>
                                        <td><?php echo $voter['vote_count']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2" class="text-center">Aucun vote enregistré pour le moment.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- ========================================================== -->
        <!-- FIN DE LA SECTION RESTAURÉE -->
        <!-- ========================================================== -->

        <div id="votersList" style="display: none;" class="mb-4">
            <h4>Liste complète des votants</h4>
            <table class="table table-bordered table-hover">
                <thead class="thead-light"><tr><th>Utilisateur</th><th>Nb. Votes</th><th>Actions</th></tr></thead>
                <tbody id="votersTableBody"></tbody>
            </table>
        </div>
        
        <div class="container-fluid p-0">
            <h4>Tous les messages de la base de données</h4>
            <?php 
                // Assurez-vous que la méthode GetAllMessages_Adm existe dans Message.php
                if(method_exists($message, 'GetAllMessages_Adm')) {
                    echo $allMessagesTable;
                } else {
                    echo "<p class='alert alert-danger'>Erreur: La méthode GetAllMessages_Adm() est introuvable.</p>";
                }
            ?>
        </div>
    </div>

    <!-- MODAL POUR LES DÉTAILS -->
    <div class="modal fade" id="userDetailsModal" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header"><h5 class="modal-title" id="userDetailsModalLabel"></h5><button type="button" class="close" data-dismiss="modal">×</button></div>
          <div class="modal-body">
            <div id="userStats"></div><hr>
            <div style="max-height: 400px; overflow-y: auto;">
                <table class="table table-striped">
                    <thead><tr><th>Message</th><th>Vote</th><th>Explication</th></tr></thead>
                    <tbody id="userVotesTableBody"></tbody>
                </table>
            </div>
          </div>
          <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button></div>
        </div>
      </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <script>
    $(document).ready(function () {
        $('#example').DataTable({ "language": { "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json" } });

        $("#showVoters").click(function() {
            var votersList = $("#votersList");
            if (votersList.is(':visible')) {
                votersList.slideUp();
            } else {
                $.ajax({
                    url: "get_voters.php",
                    dataType: "json", type: "GET",
                    success: function(voters) {
                        var votersTableBody = $("#votersTableBody").empty();
                        if (voters && voters.length > 0) {
                            $.each(voters, function(index, voter) {
                                var row = `<tr data-user-row-id="${voter.id}">
                                    <td>${voter.username}</td>
                                    <td>${voter.vote_count}</td>
                                    <td>
                                        <button class="btn btn-info btn-sm view-details" title="Voir les détails" data-userid="${voter.id}" data-username="${voter.username}"><i class="fa fa-eye"></i></button>
                                        <button class="btn btn-danger btn-sm delete-user" title="Supprimer l'utilisateur" data-userid="${voter.id}" data-username="${voter.username}"><i class="fa fa-trash"></i></button>
                                    </td></tr>`;
                                votersTableBody.append(row);
                            });
                        } else {
                            votersTableBody.append("<tr><td colspan='3' class='text-center'>Aucun votant trouvé.</td></tr>");
                        }
                        votersList.slideDown();
                    },
                    error: function(xhr) { alert("Erreur chargement votants: " + xhr.responseText); }
                });
            }
        });

        // GESTION DU CLIC SUR "DÉTAILS"
        $(document).on('click', '.view-details', function() {
            var userId = $(this).data('userid');
            var username = $(this).data('username');
            $.ajax({
                url: 'get_user_details.php',
                type: 'GET',
                dataType: 'json',
                data: { user_id: userId },
                success: function(response) {
                    if (response.status === 'success') {
                        $('#userDetailsModalLabel').text('Détails des votes pour ' + username);
                        var stats = response.data.stats;
                        $('#userStats').html(
                            `<h4>Statistiques</h4><p><strong>Haineux:</strong> <span class="vote-haineux">${stats.haineux_count}</span> | ` +
                            `<strong>Non Haineux:</strong> <span class="vote-non_haineux">${stats.non_haineux_count}</span> | `+
                            `<strong>Hésitations:</strong> <span class="vote-hesite">${stats.hesite_count}</span></p>`
                        );
                        var votesTableBody = $('#userVotesTableBody').empty();
                        if (response.data.votes && response.data.votes.length > 0) {
                            $.each(response.data.votes, function(i, vote) {
                                var voteClass = 'vote-' + (vote.vote || '').toLowerCase();
                                var escapedText = $('<div/>').text(vote.text || '').html();
                                var escapedExplication = $('<div/>').text(vote.explication || '').html();
                                votesTableBody.append(`<tr><td>${escapedText}</td><td class="${voteClass}">${vote.vote}</td><td>${escapedExplication}</td></tr>`);
                            });
                        } else {
                            votesTableBody.append('<tr><td colspan="3" class="text-center">Cet utilisateur n\'a encore fait aucun vote.</td></tr>');
                        }
                        $('#userDetailsModal').modal('show');
                    } else {
                        alert('Erreur: ' + response.message);
                    }
                },
                error: function(xhr) {
                    var errorMessage = "Erreur AJAX: " + (xhr.responseJSON ? xhr.responseJSON.message : xhr.responseText);
                    alert(errorMessage);
                }
            });
        });

        // GESTION DU CLIC SUR "SUPPRIMER"
        $(document).on('click', '.delete-user', function() {
            var userId = $(this).data('userid');
            var username = $(this).data('username');
            if (confirm(`Êtes-vous sûr de vouloir supprimer ${username} ?\nCette action est irréversible et annulera tous ses votes.`)) {
                $.ajax({
                    url: 'delete_user.php', type: 'POST', dataType: 'json', data: { user_id: userId },
                    success: function(response) {
                        if (response.status === 'success') {
                            alert(response.message);
                            location.reload();
                        } else {
                            alert('Erreur lors de la suppression: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        var errorMessage = "Erreur AJAX: " + (xhr.responseJSON ? xhr.responseJSON.message : xhr.responseText);
                        alert(errorMessage);
                    }
                });
            }
        });
    });
    </script>
</body>
</html>