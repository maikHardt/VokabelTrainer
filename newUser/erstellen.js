document.addEventListener('DOMContentLoaded', function() {
    const dropdown = document.getElementById('sprachenliste');

    function fetchLanguages() {
        fetch('../Laden/get_sprachen.php') 
            .then(response => response.text())
            .then(text => {
                const data = JSON.parse(text);
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
        fetch(`../Laden/set_sprache.php?sprache=${sprache}`) // Anfrage an den Server, um die Sprache zu setzen
            .then(() => {
                window.location.reload(); // Seite neu laden, um die neue Sprache anzuwenden
            })
            .catch(error => console.error('Fehler beim Setzen der Sprache:', error));
    });          
})
const submitButton = document.getElementById('submit_button'); // Sicherstellen, dass der Button korrekt ausgewählt wird
const vokabelUserInput = document.getElementsByClassName('inputFieldVokabel');
const satzUserInput = document.getElementsByClassName('inputFieldSatz');

submitButton.addEventListener('click', function(event) {        
    event.preventDefault(); // Verhindert den Standard-Formular-Submit

    let vokabelValid = true;
    let satzValid = true;

    // Überprüfen, ob die Vokabel-Felder leer sind
    Array.from(vokabelUserInput).forEach((input) => {
        if (input.value.trim() === "") {
            input.placeholder = "Bitte fülle dieses Feld aus";
            input.classList.add('error');
            vokabelValid = false;
        } else {
            input.classList.remove('error');
        }
    });

    // Überprüfen, ob die Satz-Felder leer sind
    Array.from(satzUserInput).forEach((input) => {
        if (input.value.trim() === "") {
            input.placeholder = "Bitte fülle dieses Feld aus";
            input.classList.add('error');
            satzValid = false;
        } else {
            input.classList.remove('error');
        }
    });

    // Nur weiterfahren, wenn alle Felder valide sind
    if (vokabelValid && satzValid) {
        const mainblock = document.querySelectorAll('.main_new_inbox');
        const sprachenliste = document.getElementById('sprachenliste');
        const spracheKuerzel = sprachenliste.options[sprachenliste.selectedIndex].getAttribute('data-kuerzel'); 

        // Funktion zum Übersetzen von Text
        function translateText(text, targetLang) {
            const url = `https://translate.googleapis.com/translate_a/single?client=gtx&sl=auto&tl=${targetLang}&dt=t&q=${encodeURIComponent(text)}`;
            return fetch(url)
                .then(response => response.json())
                .then(data => data[0][0][0])
        }

        // Übersetzungen anfordern
        const translatePromises = Array.from(mainblock).map(block => {
            const vokabelInput = block.querySelector('.leftblock .inputFieldVokabel');
            const satzInputs = block.querySelectorAll('.rightblock .inputFieldSatz');
            const vokabel = vokabelInput.value;
            const saetze = Array.from(satzInputs).map(input => input.value);

            const vokabelPromise = translateText(vokabel, spracheKuerzel);
            const saetzePromises = saetze.map(satz => translateText(satz, spracheKuerzel));

            return Promise.all([vokabelPromise, Promise.all(saetzePromises)])
                .then(([translatedVokabel, translatedSaetze]) => {
                    return {
                        vokabel: vokabel,
                        translatedVokabel: translatedVokabel,
                        saetze: saetze,
                        translatedSaetze: translatedSaetze
                    };
                });
        });

        // Alle Übersetzungen sammeln
        Promise.all(translatePromises)
    .then(translatedVokabeln => {
        let requestData = {
            userInput: translatedVokabeln
        };

        // Sende die Daten an den Server
        return fetch('hinzufuegen.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(requestData)
        });
    })
    .then(response => response.text())
    .then(() => {
        // Weiterleitung zur Index-Seite
        window.location.href = "../index.php";
    })
    .catch(error => {
        console.error('Fehler:', error);
    });
    }
});  

let j = 3; // Anzahl der initialen Vokabelblöcke
let vokabelVergabe = 1; // Startwert für die Benennung der Vokabelfelder
let l = 3; // Anzahl der Satzblöcke pro Vokabel
let plusVergabe = 4; // Startwert für die Benennung der Satzfelder
let minusVergabe = 3; // Startwert für die Benennung der Satzfelder innerhalb der Schleife

const main_new_box = document.getElementById('main_new_box');

function createInputBlock() {    

    const main_new_inbox = document.createElement('div');
    main_new_inbox.className = 'main_new_inbox';

    // Erstellung der Vokabelblöcke
    const leftblock = document.createElement('div');
    leftblock.className = 'leftblock';

    const inputFieldVokabel = document.createElement('input');
    inputFieldVokabel.className = 'inputFieldVokabel';
    inputFieldVokabel.type = 'text';
    inputFieldVokabel.name = `inputFieldVokabel${vokabelVergabe}`;
    inputFieldVokabel.placeholder = 'Vokabel...';
    leftblock.appendChild(inputFieldVokabel);
    vokabelVergabe++;

    // Erstelle den rechten Block für die Sätze
    const rightblock = document.createElement('div');
    rightblock.className = 'rightblock';
    for (let k = 0; k < l; k++) {
        const inputFieldSatz = document.createElement('input');
        inputFieldSatz.className = 'inputFieldSatz';
        inputFieldSatz.type = 'text';
        inputFieldSatz.name = `${minusVergabe}`;
        minusVergabe--;
        inputFieldSatz.placeholder = 'Bitte gebe ein Satz ein... Hinweis: Das Vokabel kann im Satz enthalten sein!';
        rightblock.appendChild(inputFieldSatz);
    }
    minusVergabe = 3; // Reset für die nächste Vokabel

    // Erstelle den Button zum Hinzufügen weiterer Sätze
    const buttonSatz = document.createElement('button');        
    buttonSatz.className = 'buttonSatz';
    buttonSatz.textContent = 'weitere Sätze hinzufügen';
    buttonSatz.addEventListener("click", function(event) {
        event.preventDefault();
        const inputFieldSatz = document.createElement('input');
        inputFieldSatz.className = 'inputFieldSatz';
        inputFieldSatz.type = 'text';
        inputFieldSatz.name = `inputFieldSatz${plusVergabe}`;
        plusVergabe++;
        inputFieldSatz.placeholder = 'Bitte gebe ein Satz ein... Hinweis: Das Vokabel kann im Satz enthalten sein!';
        rightblock.insertBefore(inputFieldSatz, rightblock.lastChild);
    });
    rightblock.appendChild(buttonSatz);

    // Füge die Blocks zum Haupt-Container hinzu
    main_new_inbox.appendChild(leftblock);
    main_new_inbox.appendChild(rightblock);

    main_new_box.appendChild(main_new_inbox);
}

// Erstelle die anfänglichen Vokabelblöcke
for (let i = 0; i < j; i++) {
    createInputBlock();
}
// Event-Listener für den Button zum Hinzufügen neuer Vokabeln
const addButton = document.getElementById('Vokabel_hinzufuegen');
if (addButton) {
    addButton.addEventListener('click', function(event) {
        event.preventDefault();
        j++;
        createInputBlock(); // Füge einen neuen Vokabelblock hinzu
    });
} else {
    console.error('Button mit ID "Vokabel_hinzufuegen" wurde nicht gefunden.');
}