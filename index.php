<?php
@session_start();

require_once 'sql/db.php';
if(!isset($_SESSION['nutzer'])) {
    header('Location:Loginfunktion/loginformular.php');
    exit();
}
$sprache = isset($_SESSION['sprache']) ? ($_SESSION['sprache']) : 0;
if($sprache != 0) {

    $benutzer_id = $_SESSION['nutzer']->benutzer_id;
    $stmtVokabel = $db->prepare("SELECT * FROM `vokabel` WHERE benutzer_id = ? AND sprache_id = ?");
    $stmtVokabel->bind_param('ii', $benutzer_id, $sprache);
    $stmtVokabel->execute();
    $resultVokabel = $stmtVokabel->get_result();
    if ($resultVokabel->num_rows > 0) {
        $redirect = false;
        while ($vokabel = $resultVokabel->fetch_object()) {
            $vokabelID = $vokabel->vokabel_id; 
            $stmtSatz = $db->prepare("SELECT * FROM `beispielsatz` WHERE benutzer_id = ? AND vokabel_id = ? AND sprache_id = ?");
            $stmtSatz->bind_param('iii', $benutzer_id, $vokabelID, $sprache);
            $stmtSatz->execute();
            $resultSatz = $stmtSatz->get_result();
            if ($resultSatz->num_rows < 0) {
                $redirect = true;
                break;
            }
        }    
        // Leite nur weiter, wenn die Bedingung zutrifft
        if ($redirect) {
            header('Location: NewUser/erstellen.php');
            exit();
        }
    } else {
        // Falls keine Vokabeln existieren, kann hier eine Weiterleitung hinzugefügt werden
        header('Location: NewUser/erstellen.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css">
    <title>VokabelTrainer</title>
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
                <form action="Loginfunktion/logout.php">
                    <input type="submit" id="logout_text" value="Ausloggen" method="POST">
                </form>
            </div>            
        </div> 
    </div>
    <div id="content_sprachauswahl">
        <select id="sprachenliste">
            <!-- Sprach Optionen werden durch JavaScript eingefügt -->
        </select>
        <script>
        const selectedLanguage = <?= json_encode($sprache) ?>;
        </script>
        <script src="index.js"></script>
    </div>
    <?php if ($sprache != 0) {?>
    <div id="content">        
        <div id="content_left">
            <div id="content_header">
                <h4 style="margin-left:1ch; margin-top:1ch; margin-bottom:0;">Auswahl der Übung:</h4>
            </div>            
            <div id="content_auswahl">
                <div>
                    <form action="Uebungen/Uebung1.php" method="POST">
                        <input type="submit" class="uebung" value="Vokabel übersetzen">
                    </form>
                </div>
                <div>
                    <form action="Uebungen/Uebung2.php" method="POST">
                        <input type="submit" class="uebung" value="Satz übersetzen">
                    </form>
                </div>
            </div>            
        </div>
        <div id="content_right">
            <div>
                <h4 style="margin: 2.5%; margin-bottom:4px;">Deine Statistiken</h4>
            </div>
            <div id="stats_div">
                <table id="stats">
                    <tr>
                        <th id="stats_name">Übung</th>
                        <th id="stats_zeit">Punktzahl</th>
                        <th id="stats_punkte">Zeit</th>
                    </tr>                    
                </table>
            </div>            
        </div>        
    </div>
    <div id="content_bottom">
        <div id="content_balken">
        <!-- Hier werden die Vokabel und Sätze erstellt + bearbeitungsmöglichkeiten -->
        </div>                  
    </div>   
    <?php } ?>
    </body>
</html>