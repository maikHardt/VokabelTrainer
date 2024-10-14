const container = document.getElementById('main_box');

// Funktion zum Erstellen der Blöcke
function createBlocks(saetze) {
    const saetzeAnzahl = Math.min(saetze.length, 10); // Maximal 10 Fragen
    const maininnerDiv = document.createElement('div');
    maininnerDiv.id = 'maininnerDiv';
    container.appendChild(maininnerDiv);
    
    for (let i = 0; i < saetzeAnzahl; i++) {
        const satzDiv = document.createElement('div');
        satzDiv.className = 'satzDiv';

        const satzinnerDiv = document.createElement('div');
        satzinnerDiv.classList.add('satzinnerDiv');
        satzinnerDiv.textContent = saetze[i];

        // Verstecktes Eingabefeld für die Vokabel
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = `saetze${i + 1}`;
        hiddenInput.value = saetze[i];

        const inputText = document.createElement('input');
        inputText.type = 'text';
        inputText.className = 'antwort';
        inputText.name = `antwort${i + 1}`;
        inputText.placeholder = `Gebe eine Antwort an!`;

        satzDiv.appendChild(satzinnerDiv);
        satzDiv.appendChild(hiddenInput); // Verstecktes Eingabefeld hinzufügen
        satzDiv.appendChild(inputText);
        maininnerDiv.appendChild(satzDiv);
    }

    const submitDiv = document.createElement('div');
    submitDiv.id = 'submitDiv';
    maininnerDiv.appendChild(submitDiv);
    
    // Erstellen eines Submit-Buttons am Ende
    const submitButton = document.createElement('button');
    submitButton.type = 'submit'; // Button-Typ auf 'submit' setzen
    submitButton.id = 'main_form_button';
    submitButton.textContent = 'Absenden';
    submitDiv.appendChild(submitButton);
}

// Funktion zum Abrufen der Vokabeln
function fetchSaetze() {
    fetch('Uebung2.php?action=fetch')
        .then(response => response.json())
        .then(saetze => {
            if (saetze.length > 0) {
                const zufaelligeSaetze = saetze.sort(() => Math.random() - 0.5).slice(0, 10);
                createBlocks(zufaelligeSaetze);
            } else {
                console.error('Keine Sätze gefunden.');
            }
        })
        .catch(error => console.error('Fehler beim Abrufen der Sätze:', error));
}

// Formulardaten beim Submit versenden
document.getElementById('main_form').addEventListener('submit', function (event) {
    event.preventDefault();
    const formData = new FormData(this);

    fetch('ueberpruefung2.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        updateVokabelStyles(data.antworten); // Ergebnisse verarbeiten und Farben anpassen
        // Button für die Weiterleitung erstellen
        const redirectButton = document.createElement('button');
        redirectButton.textContent = 'Zurück zur Startseite';
        redirectButton.id = 'redirect_button';
        redirectButton.addEventListener('click', function() {
            window.location.href = '../index.php'; // Weiterleitung zur index.php
        });
        document.getElementById('main_form_button').remove();
        document.getElementById('submitDiv').appendChild(redirectButton);
    })
    .catch(error => console.error('Fehler beim Absenden des Formulars:', error));
});

function updateVokabelStyles(antworten) {
    const satzDivs = document.querySelectorAll('.satzDiv');

    satzDivs.forEach((div, index) => {
        const isCorrect = antworten[index].korrekt; // True oder False basierend auf der Prüfung
        div.style.backgroundColor = isCorrect ? 'green' : 'red'; // Hintergrundfarbe anpassen
    });
}

// Sätze abrufen
fetchSaetze();
