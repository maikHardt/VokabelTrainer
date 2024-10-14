document.addEventListener('DOMContentLoaded', function() {
    const dropdown = document.getElementById('sprachenliste');

        function fetchLanguages() {
            fetch('Laden/get_sprachen.php') 
                .then(response => response.json())
                .then(data => {
                    data.forEach(sprache => {
                        const option = document.createElement('option');
                        option.value = sprache.id; // ID der Sprache setzen
                        option.textContent = sprache.name;
                        option.setAttribute('data-kuerzel', sprache.kuerzel);
                        // Überprüfen, ob diese Sprache die gespeicherte ist und als selected setzen
                        if (sprache.id == selectedLanguage) {
                            option.selected = true;
                        }

                        dropdown.appendChild(option);
                    });
                })
                .catch(error => console.error('Fehler beim Abrufen der Sprachen:', error));
        }
        fetchLanguages();
        
        document.getElementById('sprachenliste').addEventListener('change', function() {
            const sprache = this.value; // Die ausgewählte Sprache
            fetch(`Laden/set_sprache.php?sprache=${sprache}`) // Anfrage an den Server, um die Sprache zu setzen
                .then(() => {
                    window.location.reload(); // Seite neu laden, um die neue Sprache anzuwenden
                })
                .catch(error => console.error('Fehler beim Setzen der Sprache:', error));
        });
    const statstr = document.getElementById('stats');
    
    function fetchStats() {
        fetch('Laden/get_stats.php') 
            .then(response => response.json())
            .then(data => {
                
                
                data.forEach(item => {
                    // Übungsname festlegen
                    let uebung = "Vokabel übersetzen";
                    if(item.uebung_id == 1) {
                        uebung = "Satz übersetzen";
                    }
                    const trcontainer = document.createElement('tr')
                    trcontainer.className = 'stats';
                    // Neue div-Elemente für die Statistiken erstellen
                    const uebungDiv = document.createElement('td');
                    uebungDiv.className = 'uebungDiv';
                    uebungDiv.textContent = uebung;
                    trcontainer.appendChild(uebungDiv);

                    const punktzahlDiv = document.createElement('td');
                    punktzahlDiv.className = 'punktzahlDiv';
                    punktzahlDiv.textContent = item.punktzahl;
                    trcontainer.appendChild(punktzahlDiv);

                    const zeitDiv = document.createElement('td');
                    zeitDiv.className = 'zeitDiv';
                    const date = new Date(item.zeitstempel);
                    const day = String(date.getDate()).padStart(2, '0');
                    const month = String(date.getMonth() + 1).padStart(2, '0'); // Monate sind 0-basiert
                    const year = date.getFullYear();
                    const formattedDate = `${day}/${month}/${year}`;
                    zeitDiv.textContent = formattedDate;
                    trcontainer.appendChild(zeitDiv);

                    statstr.appendChild(trcontainer);
                });
            })
            .catch(error => console.error('Fehler beim Abrufen der Statistiken:', error));
    }
    fetchStats();
});
function fetchVokabeln() {
    fetch(`Laden/get_vokabel_saetze.php`)
        .then(response => response.json())
        .then(data => {

            if (data.error) {
                console.error(data.error);
                
                // Wenn keine Vokabeln gefunden wurden, Benutzer als 'neuernutzer' setzen
                if (data.error === "Keine Vokabeln gefunden.") {
                    window.location.href = 'index.php?neuernutzer=0'; // Weiterleitung zu index.php
                }
                return;
            }
            const content_balken = document.getElementById('content_balken');
            data.forEach(item => {                
                
                const content_div = document.createElement('div');                
                content_div.className = 'content_div';

                const vokabelDiv = document.createElement('div');
                vokabelDiv.className = 'vokabelDiv';
                
                const vokabel = document.createElement('div');
                vokabel.className = 'vokabel';
                vokabel.textContent = item.vokabelwort;

                const saetzeladenDiv = document.createElement('div');
                saetzeladenDiv.className = 'saetzeladen';

                item.saetze.forEach(satz => {
                    const satzDiv = document.createElement('div');
                    satzDiv.className = 'satz';
                    satzDiv.textContent = satz;
                    saetzeladenDiv.appendChild(satzDiv);
                    const button_div = document.createElement('div');
                    button_div.className = 'buttondiv';

                    const deletebutton = document.createElement('button');
                    deletebutton.textContent = 'Löschen';
                    deletebutton.className = 'deletebutton';
                    deletebutton.addEventListener('click', () => {
                        deletemode();
                    });
                    const editbutton = document.createElement('button');
                    editbutton.textContent = 'Bearbeiten';
                    editbutton.className = 'editbutton';
                    editbutton.addEventListener('click', () => {
                        editmode();
                    });
                    button_div.appendChild(editbutton);
                    button_div.appendChild(deletebutton);
                    satzDiv.appendChild(button_div);
                });

                const satzbutton = document.createElement('button');
                satzbutton.className = 'Saetze_laden';
                satzbutton.textContent = 'Weitere Sätze laden';
                satzbutton.addEventListener('click', () => {
                    const satzInput_Div = document.createElement('div');
                    satzInput_Div.className = 'satzInputDiv';
                    const satzInput = document.createElement('div');
                    satzInput.type = 'text';
                    satzInput_Div.appendChild(satzInput);
                    saetzeladenDiv.appendChild(satzInput_Div);
                });
                

                vokabelDiv.appendChild(vokabel);
                content_div.appendChild(vokabelDiv);
                saetzeladenDiv.appendChild(satzbutton);
                content_div.appendChild(saetzeladenDiv);
                content_balken.appendChild(content_div);
            });
            const vokabelbutton = document.createElement('button');
            vokabelbutton.id = 'vokabel_button';
            vokabelbutton.textContent = 'Weitere Vokabel laden';
            vokabelbutton.addEventListener('click', () => {
                const vokabel = document.createElement('div');
                vokabel.className = 'vokabel';                
                if (item.vokabelwort) {
                    vokabel.textContent = 'Keine Weiteren Vokabel vorhanden';
                } else {
                    vokabel.textContent = item.vokabelwort;
                }               
                vokabelDiv.appendChild(vokabel);
                fetchVokabeln();
            });

            const addbutton = document.createElement('button');
            addbutton.id = 'add_button';
            addbutton.textContent = 'Weitere Hinzufügen';
            addbutton.addEventListener('click', () => {
                window.location.href = "newUser/erstellen.php";
            });
            
            
            content_bottom.appendChild(vokabelbutton);
            content_bottom.appendChild(addbutton);
        })
        .catch(error => console.error('Fehler beim Abrufen der Vokabeln:', error));
}

// Erste Vokabeln laden
fetchVokabeln();



