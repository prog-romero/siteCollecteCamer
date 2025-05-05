<?php

namespace Phppot;
require_once __DIR__ . '/lib/DataSource.php'; // correction du chemin "./lib/DataSource.php" -> "/lib/DataSource.php"

$db = new DataSource();
$result = $db->select("SELECT text,vote,explication FROM message,votes,utilisateur WHERE message.id = votes.id_message AND utilisateur.id = votes.id_user");

if (!empty($result)) {
    $delimiter = ",";
    $filename = "dataVotes.csv";
    
    // Ouvre un fichier temporaire en mémoire
    $f = fopen('php://memory', 'w');
    
    // En-têtes CSV
    $fields = array('Text', 'Vote_Utilisateur', 'Explication_Utilisateur');
    fputcsv($f, $fields, $delimiter);
    
    // Lignes de données
    foreach ($result as $re) {
        $lineData = array($re['text'], $re['vote'], $re['explication']);
        fputcsv($f, $lineData, $delimiter);
    }
    
    // Revenir au début du fichier mémoire
    fseek($f, 0);
    
    // En-têtes HTTP pour forcer le téléchargement
    header('Content-Type: text/csv'); // ✅ ajout de l'en-tête manquant
    header('Content-Disposition: attachment; filename="' . $filename . '";');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Envoie du contenu CSV
    fpassthru($f);
}
exit;
?>
