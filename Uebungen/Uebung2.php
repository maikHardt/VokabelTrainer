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

    $ssql = "SELECT satz FROM `beispielsatz` WHERE sprache_id = ? AND benutzer_id = ?";
    $sstmt = $db->prepare($ssql);
    $sstmt->bind_param('ii', $sprache_id, $nutzerID);
    $sstmt->execute();
    $sresult = $sstmt->get_result();
    
    if ($sresult->num_rows > 0) {
        while ($row = $sresult->fetch_assoc()) {
            $Liste[] = $row['satz'];
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
    <title>Übung 2</title>
    <link rel="stylesheet" href="uebung2.css">
    <script src="uebung2.js" defer></script>
<body>
    <div id="header">
        <h2>Sätze übersetzen in <?= $Sprache['sprache'] ?></h2>
    </div>
    <form id="main_form" action="ueberpruefung2.php" method="POST" enctype="multipart/form-data" accept-charset="UFT-8">
    <div id="main_box">
    <!-- Die Übung wird über JS erstellt -->
    </div> 
    </form>
</body>
</html>