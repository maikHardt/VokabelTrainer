<?php 
session_start();
require_once '../sql/db.php';
$sprache = isset($_SESSION['sprache']) ? ($_SESSION['sprache']) : 0;
if ($sprache != 0) {
    $benutzer_id = $_SESSION['nutzer']->benutzer_id;
    
    // Überprüfen, ob Vokabeln existieren
    /*$stmtVokabel = $db->prepare("SELECT * FROM `vokabel` WHERE benutzer_id = ? AND sprache_id = ?");
    $stmtVokabel->bind_param('ii', $benutzer_id, $sprache);
    $stmtVokabel->execute();
    $resultVokabel = $stmtVokabel->get_result();

    if ($resultVokabel->num_rows > 0) {
        $redirect = true;          
        if ($redirect) {
            header('Location: ../index.php');
            exit();
        }
    }
    */
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="erstellen.css">
    <title>Vokabel und Sätze Erstellen</title>
</head>
<body>
    <div id="head">        
        <div id="headerbottom">
            <h1 id="ueberschrift">VokabelTrainer</h1>
        </div> 
    </div>
    <div id="profil">
        <div id="top">
            <div id="nutzer">
                <h3 id="nutzer_text">Guten Tag, <?= $_SESSION['nutzer']->nutzername ?></h3>
            </div>
            <div id="logout">
                <form action="../Loginfunktion/logout.php">
                    <input type="submit" id="logout_text" value="Ausloggen" method="POST">
                </form>
            </div>            
        </div> 
    </div>
    <div id="main_new">
        <form id="daten">
            <div id="main_new_top">            
                <div id="content_sprachauswahl">
                    <select id="sprachenliste">
                        <!-- Sprach Optionen werden durch JavaScript eingefügt -->
                    </select>
                    <script>
                        const selectedLanguage = <?= json_encode($sprache) ?>;
                    </script>                                   
                </div>        
                <div id="submit_button_div">
                    <input type="submit" value="Hinzufügen" id="submit_button">
                </div>
            </div>
            <?php if($sprache != 0) { ?>            
            <div id="main_new_box">
                <!-- Hier werden die Div Boxen erstellt für Vokabel und Sätze 1/3 von Javascript -->           
            </div>
            <div id="main_new_vokabel">
                <div id=Vokabel_div>
                    <button id="Vokabel_hinzufuegen">Weitere Vokabel Hinzufügen</button>
                </div>               
            </div>
            <?php } ?>         
        </form>  
    </div>
    <script src="erstellen.js"></script>
</body>
</html>