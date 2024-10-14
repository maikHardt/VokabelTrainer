<?php
require_once '../sql/db.php';

header('Content-Type: application/json');

$sql = 'SELECT * FROM lernstatistik'; // Ersetze diese Query mit der tatsächlichen Query für deine Tabelle
$result = $db->query($sql);

$stats = [];
while ($row = $result->fetch_assoc()) {
    $stats[] = [
       'benutzer_id' => $row['benutzer_id'],
       'sprache_id' => $row['sprache_id'],
       'uebung_id' => $row['uebung_id'],
       'zeitstempel' => $row['zeitstempel'],
       'punktzahl' => $row['punktzahl']
    ];
}

echo json_encode($stats);
?>
