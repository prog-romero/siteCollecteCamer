<?php
namespace Phppot;

class Message
{
    private $ds;

    function __construct()
    {
        require_once __DIR__ . '/../lib/DataSource.php';
        $this->ds = new DataSource();
    }

    public function isTextMessageExists($text)
    {
        $query = 'SELECT * FROM message WHERE text = ?';
        $paramType = 's';
        $paramValue = array($text);
        $resultArray = $this->ds->select($query, $paramType, $paramValue);
        return is_array($resultArray) && count($resultArray) > 0;
    }

    public function registerMessage()
    {
        $isTextMessageExists = $this->isTextMessageExists($_POST["text"]);
        $valid = (htmlspecialchars($_SESSION["admin"]) == 1) ? 1 : 0;
        $response = 0;

        if ($isTextMessageExists) {
            return array("status" => "error", "message" => "text already exists.");
        }

        $query = 'INSERT INTO message (text, id_user, valide) VALUES (?, ?, ?)';
        $paramType = 'ssi';
        $paramValue = array(
            htmlspecialchars($_POST["text"]),
            htmlspecialchars($_SESSION["id"]),
            $valid
        );
        $messageId = $this->ds->insert($query, $paramType, $paramValue);

        if (!empty($messageId)) {
            $response = array("status" => "success", "message" => "Message enregistré");
        }

        return $response;
    }

    public function RandomGetMessage($id_message)
    {
        $query = 'SELECT text FROM message WHERE id = ?';
        $paramType = 's';
        $paramValue = array($id_message);
        return $this->ds->select($query, $paramType, $paramValue);
    }

    public function updateVote($id)
    {
        $q = 'UPDATE message SET vote_final = (CASE
                            WHEN haineux >= non_haineux AND haineux >= hesite THEN "haineux"
                            WHEN non_haineux > haineux AND non_haineux > hesite THEN "non_haineux"
                            WHEN hesite > non_haineux AND hesite > haineux THEN "hesite"
                        END)
              WHERE id = ?';
        $paramType = 's';
        $paramValue = array($id);
        $this->ds->update($q, $paramType, $paramValue);
    }

    public function voteMessage($id)
    {
        $response = array("status" => "error", "message" => "vous avez déjà voté pour ce message");
        $vote = "";

        // Vérifier si le message a déjà reçu 3 votes
        $query = 'SELECT total_votes FROM message WHERE id = ?';
        $paramType = 's';
        $paramValue = array($id);
        $voteCheck = $this->ds->select($query, $paramType, $paramValue);

        if (!empty($voteCheck) && $voteCheck[0]['total_votes'] >= 3) {
            return array("status" => "error", "message" => "Ce message a déjà reçu 3 votes.");
        }

        // Vérifier si l'utilisateur a déjà voté ce message
        $query = 'SELECT * FROM votes WHERE id_message = ? AND id_user = ?';
        $paramType = 'ss';
        $paramValue = array($id, $_SESSION["id"]);
        $voteRecord = $this->ds->select($query, $paramType, $paramValue);

        if (empty($voteRecord)) {
            if (htmlspecialchars($_POST["vote"]) == "1") {
                $vote = "haineux";
                $q = 'UPDATE message SET haineux = haineux + 1, total_votes = total_votes + 1 WHERE id = ?';
            } elseif (htmlspecialchars($_POST["vote"]) == "0") {
                $vote = "non_haineux";
                $q = 'UPDATE message SET non_haineux = non_haineux + 1, total_votes = total_votes + 1 WHERE id = ?';
            } elseif (htmlspecialchars($_POST["vote"]) == "2") {
                $vote = "hesite";
                $q = 'UPDATE message SET hesite = hesite + 1, total_votes = total_votes + 1 WHERE id = ?';
            }

            $paramType = 's';
            $paramValue = array($id);
            $this->ds->update($q, $paramType, $paramValue);

            $this->updateVote($id);

            // Insérer le vote
            $query = 'INSERT INTO votes (id_user, id_message, vote, explication) VALUES (?, ?, ?, ?)';
            $paramType = 'ssss';
            $paramValue = array(
                $_SESSION["id"],
                $id,
                $vote,
                htmlspecialchars($_POST["explication"])
            );
            $voteId = $this->ds->insert($query, $paramType, $paramValue);

            if (!empty($voteId)) {
                $response = array("status" => "success", "message" => "vote bien pris en compte");
            }
        }

        return $response;
    }

    public function saveCSV()
    {
        $doublon = 0;
        $insert = 0;

        if ($_FILES["file"]["error"] > 0) {
            return array("status" => "error", "message" => "Return Code: " . $_FILES["file"]["error"]);
        }

        $file = fopen($_FILES["file"]["tmp_name"], "r");
        $extension = pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);

        if ($extension != "csv") {
            return array("status" => "error", "message" => "fichier non pris en compte");
        }

        $headers = fgetcsv($file);
        $id = array_search("id", $headers);
        $text = array_search("text", $headers);

        if ($id === false || $text === false) {
            return array("status" => "error", "message" => "fichier avec une structure non requise revoir le fichier");
        }

        $valid = (htmlspecialchars($_SESSION["admin"]) == 1) ? 1 : 0;

        while (($data = fgetcsv($file)) !== FALSE) {
            if (!$this->isTextMessageExists($data[$text])) {
                $query = 'INSERT INTO message (text, id_user, id_source, source, valide) VALUES (?, ?, ?, ?, ?)';
                $paramType = 'ssssi';
                $paramValue = array(
                    $data[$text],
                    (int)htmlspecialchars($_SESSION["id"]),
                    $data[$id],
                    (int)htmlspecialchars($_POST["source"]),
                    $valid
                );
                $messageId = $this->ds->insert($query, $paramType, $paramValue);
                if (!empty($messageId)) {
                    $insert++;
                }
            } else {
                $doublon++;
            }
        }

        return array(
            "status" => "success",
            "message" => "fichier enregistré avec succès</br>$insert nouvelles insertions</br>$doublon doublons",
            "doublon" => $doublon,
            "insert" => $insert
        );
    }

    public function GetNextMessage($id)
    {
        $query = 'SELECT * FROM message WHERE id NOT IN (
                      SELECT id_message FROM votes WHERE id_user = ?) 
                  AND total_votes < 3 ORDER BY RAND() LIMIT 1';
        $paramType = 's';
        $paramValue = array($id);
        return $this->ds->select($query, $paramType, $paramValue);
    }

    public function GetAllMessages($id)
    {
        $query = 'SELECT DISTINCT message.id, username, text, valide 
                  FROM message, utilisateur 
                  WHERE message.id_user = utilisateur.id 
                  AND message.id NOT IN (
                      SELECT votes.id_message FROM votes WHERE votes.id_user = ?
                  )
                  AND total_votes < 3';
        $paramType = 's';
        $paramValue = array($id);
        $resultArray = $this->ds->select($query, $paramType, $paramValue);

        $result = '<table id="example" class="display" style="width:100%">
         <thead>
            <tr>
                <th style = "text-align : center;">Username</th>
                <th style = "text-align : center;">Text</th>
                <th style = "text-align : center;display:none;">validé</th>
				<th style = "text-align : center;">Voter</th>
            </tr> 
        </thead>';

        foreach ($resultArray as $data) {
            $result .= '<tr>
            <td style = "text-align : center;">'.$data['username'].'</td>
            <td>'.$data['text'].'</td>';
            $checkbox = '<td style = "text-align : center;display:none;">'.
                "<input class='mycheckbox' type='checkbox' name='check{$data['id']}' id='{$data['id']}' value='{$data['id']}' ".($data['valide'] == 1 ? 'checked' : '').">".
                '</td>';
            $result .= $checkbox;
            $result .= "<td style = 'text-align : center;font-weight:bold;'><a href='home2.php?id_message={$data['id']}'>Voter</a></td></tr>";
        }

        $result .= '</table>';
        return $result;
    }

    public function GetAllMessages_Adm()
    {
        $query = 'SELECT DISTINCT message.id, username, text, valide, vote_final 
                  FROM message, utilisateur 
                  WHERE message.id_user = utilisateur.id';
        $resultArray = $this->ds->select($query, '', array());

        $result = '<table id="example" class="display" style="width:100%">
         <thead>
            <tr>
                <th style = "text-align : center;">Username</th>
                <th style = "text-align : center;">Text</th>
                <th style = "text-align : center;display:none;">validé</th>
				<th style = "text-align : center;">Label</th>
            </tr> 
        </thead>';

        foreach ($resultArray as $data) {
            $result .= '<tr>
            <td style = "text-align : center;">'.$data['username'].'</td>
            <td>'.$data['text'].'</td>';
            $checkbox = '<td style = "text-align : center;display:none;">'.
                "<input class='mycheckbox' type='checkbox' name='check{$data['id']}' id='{$data['id']}' value='{$data['id']}' ".($data['valide'] == 1 ? 'checked' : '').">".
                '</td>';
            $result .= $checkbox;
            $result .= "<td style = 'text-align : center;font-weight:bold;'>{$data['vote_final']}</td></tr>";
        }

        $result .= '</table>';
        return $result;
    }
}
