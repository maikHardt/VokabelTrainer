<?php
session_start();

if (isset($_GET['sprache'])) {
    $sprache = (int)$_GET['sprache'];

    $_SESSION['sprache'] = $sprache;
    
} else {
    // Fehler, falls keine Sprache übergeben wurde
    echo json_encode(['success' => false, 'message' => 'Keine Sprache übergeben.']);
}
?>