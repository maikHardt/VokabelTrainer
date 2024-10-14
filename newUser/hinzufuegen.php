<?php
/* Momentane Bugs/Fehler die nicht sein sollten 

    - Sätze/Vokabel können doppelt hinzugefügt werden
    
*/
require_once '../sql/db.php';
session_start();

// Empfange die POST-Daten
$data = file_get_contents('php://input');
$requestData = json_decode($data, true);
$sprache_id = isset($_SESSION['sprache']) ? $_SESSION['sprache'] : 0;
$nutzerID = $_SESSION['nutzer']->benutzer_id;

header('Content-Type: application/json');

if (isset($requestData['userInput'])) {
    $vokabeln = $requestData['userInput'];
    
    $db->begin_transaction();
    try {        
        $allSuccess = true; // Flag für den Erfolg aller Operationen

        foreach ($vokabeln as $item) {
            if ($item['vokabel'] != null) {
                $vokabel = $item['vokabel'];
                $uppervokabel = strtoupper($item['vokabel']);
    
                $vokabelStmt = $db->prepare('SELECT * FROM `vokabel` WHERE UPPER(`vokabelwort`) = ? AND `benutzer_id` = ? AND `sprache_id` = ?');
                $vokabelStmt->bind_param('sii', $uppervokabel, $nutzerID, $sprache_id);
                $vokabelStmt->execute();
                $result = $vokabelStmt->get_result();
                $Vokabel = $result->fetch_assoc();
            
                if ($Vokabel) {
                    $vokabelId = $Vokabel['vokabel_id'];
                    foreach ($item['saetze'] as $index => $satz) {
                        if ($satz != null) {
                            $upperSatz = strtoupper($satz);

                            $satzStmt = $db->prepare('SELECT `satz` FROM `beispielsatz` WHERE UPPER(`satz`) = ? AND `sprache_id` = ? `vokabel_id` = ?');
                            $satzStmt->bind_param('sii', $upperSatz, $sprache_id, $vobabelId);
                            $satzStmt->execute();
                            $result = $satzStmt->get_result();
                            $existingSatz = $result->fetch_assoc();

                            if (!$existingSatz) {
                                $satzStmt = $db->prepare("INSERT INTO beispielsatz (benutzer_id, vokabel_id, sprache_id, satz, uebersetzter_satz) VALUES (?, ?, ?, ?, ?)");
                                $translatedSatz = $item['translatedSaetze'][$index];                            
                                $satzStmt->bind_param('iiiss', $nutzerID, $vokabelId, $sprache_id, $satz, $translatedSatz);
                                $satzStmt->execute();                        
                            }
                        } else {
                            $allSuccess = false; // Fehler beim Hinzufügen von Sätzen
                        }                     
                    }
                } else {                    
                    $vokabelStmt = $db->prepare("INSERT INTO vokabel (benutzer_id, sprache_id, vokabelwort, uebersetzung) VALUES (?, ?, ?, ?)");
                    $translatedVokabel = $item['translatedVokabel'];

                    $vokabelStmt->bind_param('iiss', $nutzerID, $sprache_id, $vokabel, $translatedVokabel);
                    $vokabelStmt->execute();
                   
                    $vocabStmt = $db->prepare('SELECT * FROM `vokabel` WHERE UPPER(`vokabelwort`) = ? AND `benutzer_id` = ? AND `sprache_id` = ?');
                    $vocabStmt->bind_param('sii', $uppervokabel, $nutzerID, $sprache_id);
                    $vocabStmt->execute();
                    $result = $vocabStmt->get_result();
                    $Vokabel = $result->fetch_assoc();
                    $vokabelId = $Vokabel['vokabel_id'];
                    
                    foreach ($item['saetze'] as $index => $satz) {
                        if ($satz != null) {
                            $satzStmt = $db->prepare('INSERT INTO beispielsatz (benutzer_id, vokabel_id, sprache_id, satz, uebersetzter_satz) VALUES (?, ?, ?, ?, ?)');
                            $translatedSatz = $item['translatedSaetze'][$index];                            
                            $satzStmt->bind_param('iiiss', $nutzerID, $vokabelId, $sprache_id, $satz, $translatedSatz);
                            $satzStmt->execute();
                        }
                    }
                }
            }            
        }
        $db->commit();
        exit;      
    } catch (Exception $e) {
        $db->rollback();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit; // Stellt sicher, dass das Skript hier endet
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Ungültige Daten']);
}
?>
