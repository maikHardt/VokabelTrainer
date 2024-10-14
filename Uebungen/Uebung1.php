<?php
require_once '../sql/db.php';
session_start();

$sprache_id = isset($_SESSION['sprache']) ? $_SESSION['sprache'] : 0;
$nutzerID = isset($_SESSION['nutzer']->benutzer_id) ? $_SESSION['nutzer']->benutzer_id : 0;
$sql = "SELECT sprache FROM sprachen WHERE sprache_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('i', $sprache_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $Sprache = $result->fetch_assoc();

if (isset($_GET['action']) && $_GET['action'] === 'fetch') {    
    $Liste = [];

    $vsql = "SELECT vokabelwort FROM `vokabel` WHERE sprache_id = ? AND benutzer_id = ?";
    $vstmt = $db->prepare($vsql);
    $vstmt->bind_param('ii', $sprache_id, $nutzerID);
    $vstmt->execute();
    $vresult = $vstmt->get_result();
    
    if ($vresult->num_rows > 0) {
        while ($row = $vresult->fetch_assoc()) {
            $Liste[] = $row['vokabelwort'];
        }
    }
    echo json_encode($Liste);
    exit;
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Übung</title>
    <link rel="stylesheet" href="uebung1.css">
    <script src="uebung1.js" defer></script>
<body>
    <div id="header">
        <h2>Vokabel übersetzen in <?= $Sprache['sprache'] ?></h2>
    </div>
    <form id="main_form" action="ueberpruefung1.php" method="POST" enctype="multipart/form-data" accept-charset="UFT-8">
        <div id="main_box">
        <!-- Die Übung wird über JS erstellt -->
        </div> 
    </form>
</body>
</html>