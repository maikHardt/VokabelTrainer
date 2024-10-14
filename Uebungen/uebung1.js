const container = document.getElementById('main_box');
let isSubmitHandlerAttached = false; // Flag zur Überprüfung

// Funktion zum Erstellen der Blöcke
function createBlocks(vokabel) {
    const vokabelAnzahl = Math.min(vokabel.length, 10);
    const maininnerDiv = document.createElement('div');
    maininnerDiv.id = 'maininnerDiv';
    container.appendChild(maininnerDiv);

    for (let i = 0; i < vokabelAnzahl; i++) {
        const vokabelDiv = document.createElement('div');
        vokabelDiv.className = 'vokabelDiv';

        const vokabelinnerDiv = document.createElement('div');
        vokabelinnerDiv.classList.add('vokabelinnerDiv');
        vokabelinnerDiv.textContent = vokabel[i];

        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = `vokabel${i + 1}`;
        hiddenInput.value = vokabel[i];

        const inputText = document.createElement('input');
        inputText.type = 'text';
        inputText.className = 'antwort';
        inputText.name = `antwort${i + 1}`;
        inputText.placeholder = `Gebe eine Antwort an!`;

        vokabelDiv.appendChild(vokabelinnerDiv);
        vokabelDiv.appendChild(hiddenInput);
        vokabelDiv.appendChild(inputText);
        maininnerDiv.appendChild(vokabelDiv);
    }

    const submitDiv = document.createElement('div');
    submitDiv.id = 'submitDiv';
    maininnerDiv.appendChild(submitDiv);

    const submitButton = document.createElement('button');
    submitButton.type = 'submit';
    submitButton.id = 'main_form_button';
    submitButton.textContent = 'Absenden';
    submitDiv.appendChild(submitButton);
}

// Funktion zum Abrufen der Vokabeln
function fetchSaetze() {
    fetch('Uebung1.php?action=fetch')
        .then(response => response.json())
        .then(vokabel => {
            if (vokabel.length > 0) {
                const zufaelligeVokabel = vokabel.sort(() => Math.random() - 0.5).slice(0, 10);
                createBlocks(zufaelligeVokabel);
                attachSubmitHandler(); // Hier den Event-Listener anhängen
            } else {
                console.error('Keine Vokabel gefunden.');
            }
        })
        .catch(error => console.error('Fehler beim Abrufen der Vokabel:', error));
}

// Event-Listener für das Formular hinzufügen
function attachSubmitHandler() {
    if (isSubmitHandlerAttached) return; // Überprüfen, ob der Handler bereits angehängt ist

    document.getElementById('main_form').addEventListener('submit', function (event) {
        event.preventDefault();
        

        const formData = new FormData(this);
        fetch('ueberpruefung1.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            updateVokabelStyles(data.antworten);
            const redirectButton = document.createElement('button');
            redirectButton.textContent = 'Zurück zur Startseite';
            redirectButton.id = 'redirect_button';
            redirectButton.addEventListener('click', function() {
                window.location.href = '../index.php';
            });
            document.getElementById('main_form_button').remove();            
            document.getElementById('submitDiv').appendChild(redirectButton);
        })
        .catch(error => console.error('Fehler beim Absenden des Formulars:', error));
    });
    isSubmitHandlerAttached = true; // Flag setzen, dass der Handler angehängt ist
}

// Funktion zum Anpassen der Hintergrundfarben der Vokabel-Divs
function updateVokabelStyles(antworten) {
    const vokabelDivs = document.querySelectorAll('.vokabelDiv');
    vokabelDivs.forEach((div, index) => {
        const isCorrect = antworten[index].korrekt;
        div.style.backgroundColor = isCorrect ? 'green' : 'red';
    });
}

// Vokabeln abrufen
fetchSaetze();
