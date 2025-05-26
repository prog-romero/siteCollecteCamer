<?php

namespace Phppot;
require_once __DIR__ . '/lib/DataSource.php'; // Inclusion du fichier de connexion

$db = new DataSource();

// Sélectionner uniquement les messages avec un vote_final défini (déjà votés)
$result = $db->select("SELECT text, vote_final FROM message WHERE vote_final IS NOT NULL");

if (!empty($result)) {
    $delimiter = ",";
    $filename = "dataMessages.csv";

    // Ouvre un fichier temporaire en mémoire
    $f = fopen('php://memory', 'w');

    // En-têtes du fichier CSV
    $fields = array('Text', 'Vote_Final');
    fputcsv($f, $fields, $delimiter);

    // Écriture des lignes du résultat
    foreach ($result as $re) {
        $lineData = array($re['text'], $re['vote_final']);
        fputcsv($f, $lineData, $delimiter);
    }

    // Repositionne le curseur en début de fichier mémoire
    fseek($f, 0);

    // En-têtes HTTP pour forcer le téléchargement
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '";');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Envoie le contenu CSV
    fpassthru($f);
}
exit;

?>
