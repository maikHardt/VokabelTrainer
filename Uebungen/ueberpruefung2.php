<?php
require_once '../sql/db.php';
session_start();
$sprache_id = isset($_SESSION['sprache']) ? $_SESSION['sprache'] : 0;
$nutzerID = isset($_SESSION['nutzer']->benutzer_id) ? $_SESSION['nutzer']->benutzer_id : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $punkte = 0;
    $uebung_id = 2;
    $antworten = [];
    $korrekteVokabeln = [];
    $uebersetzungen = [];

    // 1. Holen der korrekten Vokabeln und deren Übersetzungen
    foreach ($_POST as $key => $antwort) {
        if (strpos($key, 'saetze') !== false) {
            $index = (int) filter_var($key, FILTER_SANITIZE_NUMBER_INT) - 1;
            $korrekteSaetze[$index] = $antwort;

            // Hole die englische Übersetzung aus der Datenbank
            $satz = $antwort; // Aktuelle Vokabel
            $sql = "SELECT uebersetzter_satz FROM beispielsatz WHERE satz = ? AND sprache_id = ? AND benutzer_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param('sii', $satz, $sprache_id, $nutzerID);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                $uebersetzungen[$index] = $row['uebersetzter_satz']; // Englische Übersetzung speichern
            }
        }
        if (strpos($key, 'antwort') !== false) {
            $index = (int) filter_var($key, FILTER_SANITIZE_NUMBER_INT) - 1;
            $userAntwort = trim($antwort);
            $antworten[$index] = $userAntwort;

            // 2. Überprüfen, ob die Antwort korrekt ist
            if (isset($uebersetzungen[$index]) && strcasecmp($userAntwort, $uebersetzungen[$index]) === 0) {
                $punkte += 10; // 10 Punkte für die richtige Antwort
            }
        }
    }

    // Ergebnisse in die Datenbank einfügen
    $sql = "INSERT INTO lernstatistik (benutzer_id, sprache_id, uebung_id, zeitstempel, punktzahl) VALUES (?, ?, ?, NOW(), ?)";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('iiii', $nutzerID, $sprache_id, $uebung_id, $punkte);
    $stmt->execute();

    // Rückgabe der Ergebnisse als JSON
    $response = [
        'punkte' => $punkte,
        'antworten' => []
    ];

    foreach ($antworten as $index => $antwort) {
        $response['antworten'][] = [
            'satz' => $korrekteSaetze[$index],
            'userAntwort' => $antwort,
            'korrekt' => isset($uebersetzungen[$index]) && strcasecmp($antwort, $uebersetzungen[$index]) === 0
        ];
    }
    
    echo json_encode($response);
    exit;
}
?>
