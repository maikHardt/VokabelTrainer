<?php
require_once '../sql/db.php';

header('Content-Type: application/json');

$sql = 'SELECT * FROM sprachen'; // Abfrage für ID und Sprache
$result = $db->query($sql);

$languages = [];
$languages[] = ['id' => 0, 'name' => 'Bitte Sprache auswählen']; // ID für die Auswahloption
while ($row = $result->fetch_assoc()) {
    $languages[] = [
        'id' => $row['sprache_id'], // ID der Sprache
        'name' => $row['sprache'],
        'kuerzel' => $row['sprache_kuerzel']
    ];
}
echo json_encode($languages);
?>