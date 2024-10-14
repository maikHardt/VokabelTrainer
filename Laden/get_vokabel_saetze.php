<?php
require_once '../sql/db.php';
session_start();

$benutzer = $_SESSION['nutzer']->benutzer_id;

$voffset = isset($_GET['voffset']) ? (int)$_GET['voffset'] : 0;
$vlimit = isset($_GET['vlimit']) ? (int)$_GET['vlimit'] : 5;

$soffset = isset($_GET['soffset']) ? (int)$_GET['soffset'] : 0;
$slimit = isset($_GET['slimit']) ? (int)$_GET['slimit'] : 3;

$sprache = $_SESSION['sprache'];
// Vokabeln abfragen
$vsql = "SELECT * FROM `vokabel` WHERE sprache_id = ? AND benutzer_id = ? LIMIT ? OFFSET ?";
$vstmt = $db->prepare($vsql);
$vstmt->bind_param('iiii', $sprache, $benutzer, $vlimit, $voffset);
$vstmt->execute();
$vresult = $vstmt->get_result();

$Liste = [];

if ($vresult->num_rows > 0) {
    while ($row = $vresult->fetch_assoc()) {
        $vokabel_id = $row['vokabel_id']; // Vokabel-ID holen
        
        // Beispielsätze für die jeweilige Vokabel abfragen
        $ssql = "SELECT * FROM `beispielsatz` WHERE vokabel_id = ? LIMIT ? OFFSET ?";
        $sstmt = $db->prepare($ssql);
        $sstmt->bind_param('iii', $vokabel_id, $slimit, $soffset);
        $sstmt->execute();
        $sresult = $sstmt->get_result();

        $saetze = [];
        if ($sresult->num_rows > 0) {
            while ($satz = $sresult->fetch_assoc()) {
                $saetze[] = $satz['satz'];
            }
        }

        // Vokabel und die dazugehörigen Beispielsätze in die Liste einfügen
        $Liste[] = [
            'vokabelwort' => $row['vokabelwort'],
            'saetze' => $saetze
        ];
    }
} else {
    echo json_encode(["error" => "Keine Vokabeln gefunden."]);
    header('Location: ../index.php');
    exit;
}

header('Content-Type: application/json');
echo json_encode($Liste);
?>
