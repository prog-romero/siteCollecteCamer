<?php
namespace Phppot;

class Message
{
    private $ds;

    function __construct()
    {
        if (!class_exists('Phppot\DataSource')) {
            require_once __DIR__ . '/../lib/DataSource.php';
        }
        $this->ds = new DataSource();
    }

    // ===================================================================
    // VOTRE CODE ORIGINAL (CONSERVÉ AVEC CORRECTIONS MINEURES)
    // ===================================================================

    public function isTextMessageExists($text)
    {
        $query = 'SELECT * FROM message WHERE text = ?';
        // Utiliser getRecordCount est plus efficace si on veut juste savoir si ça existe
        return $this->ds->getRecordCount($query, 's', [$text]) > 0;
    }

    public function registerMessage()
    {
        if ($this->isTextMessageExists($_POST["text"])) {
            return ["status" => "error", "message" => "Ce texte existe déjà."];
        }
        $valid = (isset($_SESSION["admin"]) && $_SESSION["admin"] == 1) ? 1 : 0;
        $query = 'INSERT INTO message (text, id_user, valide) VALUES (?, ?, ?)';
        // Les ID sont des entiers, donc 'sii' est plus correct
        $paramType = 'sii';
        $paramValue = [htmlspecialchars($_POST["text"]), (int)$_SESSION["id"], $valid];
        $messageId = $this->ds->insert($query, $paramType, $paramValue);
        return !empty($messageId) ? ["status" => "success", "message" => "Message enregistré"] : ["status" => "error", "message" => "Erreur d'enregistrement."];
    }

    public function RandomGetMessage($id_message)
    {
        $query = 'SELECT text FROM message WHERE id = ?';
        // id est un entier, donc 'i' est plus correct
        return $this->ds->select($query, 'i', [$id_message]);
    }

    public function updateVote($id)
    {
        $q = 'UPDATE message SET vote_final = (CASE
                            WHEN total_votes > 0 AND haineux >= non_haineux AND haineux >= hesite THEN "haineux"
                            WHEN total_votes > 0 AND non_haineux > haineux AND non_haineux > hesite THEN "non_haineux"
                            WHEN total_votes > 0 AND hesite > non_haineux AND hesite > haineux THEN "hesite"
                            ELSE NULL
                        END)
              WHERE id = ?';
        // id est un entier, donc 'i' est plus correct
        $this->ds->execute($q, 'i', [$id]);
    }

    public function voteMessage($id)
    {
        $response = ["status" => "error", "message" => "Vous avez déjà voté pour ce message."];
        $vote = "";
        
        $query = 'SELECT total_votes FROM message WHERE id = ?';
        $voteCheck = $this->ds->select($query, 'i', [$id]);

        if (!empty($voteCheck) && $voteCheck[0]['total_votes'] >= 3) {
            return ["status" => "error", "message" => "Ce message a déjà reçu 3 votes."];
        }
        
        $query = 'SELECT * FROM votes WHERE id_message = ? AND id_user = ?';
        $voteRecord = $this->ds->select($query, 'ii', [$id, (int)$_SESSION["id"]]);

        if (empty($voteRecord)) {
            $vote_type = htmlspecialchars($_POST["vote"]);
            $q = '';
            if ($vote_type == "1") { $vote = "haineux"; $q = 'UPDATE message SET haineux = haineux + 1, total_votes = total_votes + 1 WHERE id = ?'; }
            elseif ($vote_type == "0") { $vote = "non_haineux"; $q = 'UPDATE message SET non_haineux = non_haineux + 1, total_votes = total_votes + 1 WHERE id = ?'; }
            elseif ($vote_type == "2") { $vote = "hesite"; $q = 'UPDATE message SET hesite = hesite + 1, total_votes = total_votes + 1 WHERE id = ?'; }

            if ($q) {
                $this->ds->execute($q, 'i', [$id]);
                $this->updateVote($id);

                $query = 'INSERT INTO votes (id_user, id_message, vote, explication) VALUES (?, ?, ?, ?)';
                // Les ID sont des entiers, 'iiss' est plus correct
                $paramType = 'iiss';
                $paramValue = [(int)$_SESSION["id"], (int)$id, $vote, htmlspecialchars($_POST["explication"])];
                $voteId = $this->ds->insert($query, $paramType, $paramValue);

                if (!empty($voteId)) {
                    $response = ["status" => "success", "message" => "Vote bien pris en compte."];
                }
            }
        }
        return $response;
    }

    public function saveCSV()
    {
        if ($_FILES["file"]["error"] > 0) {
            return ["status" => "error", "message" => "Erreur de fichier: " . $_FILES["file"]["error"]];
        }
        $file = fopen($_FILES["file"]["tmp_name"], "r");
        if (strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION)) != "csv") {
            return ["status" => "error", "message" => "Le fichier doit être au format .csv"];
        }
        $headers = fgetcsv($file);
        $id_col = array_search("id", $headers);
        $text_col = array_search("text", $headers);
        if ($id_col === false || $text_col === false) {
            return ["status" => "error", "message" => "Colonnes requises 'id' et 'text' manquantes."];
        }
        $doublon = 0; $insert = 0;
        $valid = (isset($_SESSION["admin"]) && $_SESSION["admin"] == 1) ? 1 : 0;
        $source = isset($_POST["source"]) ? htmlspecialchars($_POST["source"]) : 'CSV Import';

        while (($data = fgetcsv($file)) !== FALSE) {
            if (!$this->isTextMessageExists($data[$text_col])) {
                $query = 'INSERT INTO message (text, id_user, id_source, source, valide) VALUES (?, ?, ?, ?, ?)';
                // Types corrects 'siisi'
                $paramType = 'siisi';
                $paramValue = [$data[$text_col], (int)$_SESSION["id"], $data[$id_col], $source, $valid];
                if ($this->ds->insert($query, $paramType, $paramValue)) {
                    $insert++;
                }
            } else { $doublon++; }
        }
        fclose($file);
        return ["status" => "success", "message" => "Fichier traité avec succès.<br>$insert nouvelles insertions.<br>$doublon doublons."];
    }

    public function GetNextMessage($id)
    {
        $query = 'SELECT * FROM message 
                  WHERE total_votes < 3
                  AND id NOT IN (SELECT id_message FROM votes WHERE id_user = ?)
                  ORDER BY total_votes DESC, RAND()
                  LIMIT 1';
        return $this->ds->select($query, 'i', [$id]) ?: null;
    }

    public function GetAllMessages($id)
    {
        $query = 'SELECT DISTINCT m.id, u.username, m.text, m.valide 
                  FROM message m JOIN utilisateur u ON m.id_user = u.id 
                  WHERE m.total_votes < 3
                  AND m.id NOT IN (SELECT v.id_message FROM votes v WHERE v.id_user = ?)
                  ORDER BY m.total_votes DESC, m.id DESC';
        $resultArray = $this->ds->select($query, 'i', [$id]);
        $result = '<table id="example" class="display" style="width:100%">
         <thead><tr><th style="text-align:center;">Posté par</th><th>Texte</th><th style="text-align:center;">Action</th></tr></thead><tbody>';
        foreach ($resultArray as $data) {
            $result .= '<tr>
            <td style="text-align:center;">'.htmlspecialchars($data['username']).'</td>
            <td>'.htmlspecialchars($data['text']).'</td>
            <td style="text-align:center;font-weight:bold;"><a href="home2.php?id_message='.$data['id'].'">Voter</a></td></tr>';
        }
        $result .= '</tbody></table>';
        return $result;
    }

    public function GetAllMessages_Adm()
    {
        $query = 'SELECT DISTINCT m.id, u.username, m.text, m.valide, m.vote_final 
                  FROM message m JOIN utilisateur u ON m.id_user = u.id';
        $resultArray = $this->ds->select($query);
        $result = '<table id="example" class="display" style="width:100%">
         <thead><tr><th style="text-align:center;">Posté par</th><th>Texte</th><th style="text-align:center;">Label Final</th></tr></thead><tbody>';
        foreach ($resultArray as $data) {
            $result .= '<tr>
            <td style="text-align:center;">'.htmlspecialchars($data['username']).'</td>
            <td>'.htmlspecialchars($data['text']).'</td>
            <td style="text-align:center;font-weight:bold;">'.htmlspecialchars($data['vote_final'] ?? 'N/A').'</td></tr>';
        }
        $result .= '</tbody></table>';
        return $result;
    }

    public function getTotalMessages() { $query = "SELECT COUNT(*) as total FROM message"; $result = $this->ds->select($query); return $result[0]['total'] ?? 0; }
    public function getMessagesWithVoteFinalThree() { $query = "SELECT COUNT(*) as total FROM message WHERE total_votes >= 3"; $result = $this->ds->select($query); return $result[0]['total'] ?? 0; }
    public function getMessagesVotedAtLeastOnce() { $query = "SELECT COUNT(*) as total FROM message WHERE total_votes > 0"; $result = $this->ds->select($query); return $result[0]['total'] ?? 0; }
    public function getVotersWithVoteCount() { $query = "SELECT u.id, u.username, COUNT(v.id) as vote_count FROM utilisateur u LEFT JOIN votes v ON u.id = v.id_user WHERE u.type = ? GROUP BY u.id, u.username ORDER BY u.username ASC"; return $this->ds->select($query, "s", ["Votant"]); }
    public function getTopFiveVoters() { $query = "SELECT u.username, COUNT(v.id) as vote_count FROM utilisateur u LEFT JOIN votes v ON u.id = v.id_user WHERE u.type = ? GROUP BY u.id, u.username ORDER BY vote_count DESC LIMIT 5"; return $this->ds->select($query, "s", ["Votant"]); }
    public function getUserVoteCount($userId) { $query = "SELECT COUNT(*) as vote_count FROM votes WHERE id_user = ?"; $result = $this->ds->select($query, "i", [$userId]); return $result[0]['vote_count'] ?? 0; }

    // ===================================================================
    // NOUVELLES FONCTIONS AJOUTÉES POUR LES DÉTAILS ET LA SUPPRESSION
    // ===================================================================

    public function getUserVoteDetails($userId)
    {
        $query = "SELECT m.text, v.vote, v.explication FROM votes v JOIN message m ON v.id_message = m.id WHERE v.id_user = ?";
        return $this->ds->select($query, "i", [$userId]);
    }

    public function getUserVoteStats($userId)
    {
        $query = "SELECT 
                    SUM(CASE WHEN vote = 'haineux' THEN 1 ELSE 0 END) as haineux_count,
                    SUM(CASE WHEN vote = 'non_haineux' THEN 1 ELSE 0 END) as non_haineux_count,
                    SUM(CASE WHEN vote = 'hesite' THEN 1 ELSE 0 END) as hesite_count
                  FROM votes WHERE id_user = ?";
        $result = $this->ds->select($query, "i", [$userId]);
        return $result[0] ?? ['haineux_count' => 0, 'non_haineux_count' => 0, 'hesite_count' => 0];
    }
    
    public function deleteUserAndRevertVotes($userId)
    {
        $conn = $this->ds->getConnection();
        $conn->begin_transaction();
        try {
            // 1. Récupérer les votes de l'utilisateur
            $stmt_get_votes = $conn->prepare("SELECT id_message, vote FROM votes WHERE id_user = ?");
            $stmt_get_votes->bind_param('i', $userId);
            $stmt_get_votes->execute();
            $user_votes = $stmt_get_votes->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt_get_votes->close();

            // 2. Annuler chaque vote
            foreach ($user_votes as $vote) {
                $message_id = $vote['id_message'];
                $vote_type = $vote['vote'];
                if (in_array($vote_type, ['haineux', 'non_haineux', 'hesite'])) {
                    // Décrémenter les compteurs
                    $update_sql = "UPDATE message SET total_votes = total_votes - 1, $vote_type = $vote_type - 1 WHERE id = ?";
                    $stmt_update = $conn->prepare($update_sql);
                    $stmt_update->bind_param('i', $message_id);
                    $stmt_update->execute();
                    $stmt_update->close();
                    
                    // Mettre à jour le vote final directement dans la transaction
                    $q_final = 'UPDATE message SET vote_final = (CASE
                        WHEN total_votes > 0 AND haineux >= non_haineux AND haineux >= hesite THEN "haineux"
                        WHEN total_votes > 0 AND non_haineux > haineux AND non_haineux > hesite THEN "non_haineux"
                        WHEN total_votes > 0 AND hesite > non_haineux AND hesite > haineux THEN "hesite"
                        ELSE NULL END) WHERE id = ?';
                    $stmt_final = $conn->prepare($q_final);
                    $stmt_final->bind_param('i', $message_id);
                    $stmt_final->execute();
                    $stmt_final->close();
                }
            }

            // 3. Supprimer les enregistrements de votes
            $stmt_delete_votes = $conn->prepare("DELETE FROM votes WHERE id_user = ?");
            $stmt_delete_votes->bind_param('i', $userId);
            $stmt_delete_votes->execute();
            $stmt_delete_votes->close();

            // 4. Supprimer l'utilisateur
            $stmt_delete_user = $conn->prepare("DELETE FROM utilisateur WHERE id = ?");
            $stmt_delete_user->bind_param('i', $userId);
            $stmt_delete_user->execute();
            $stmt_delete_user->close();
            
            // 5. Valider
            $conn->commit();
            return ["status" => "success", "message" => "Utilisateur et ses votes ont été supprimés avec succès."];
        } catch (\Exception $e) {
            $conn->rollback();
            return ["status" => "error", "message" => "Erreur de suppression: " . $e->getMessage()];
        }
    }
}
?>