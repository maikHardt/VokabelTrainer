<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Automatische Übersetzung in 25 Sprachen</title>
</head>
<body>
    <h2>Textübersetzer</h2>
    <textarea id="inputText" rows="4" cols="50" placeholder="Gib hier den Text ein..."></textarea>
    <br>
    <button onclick="translateText()">Übersetzen</button>

    <h3>Übersetzungen:</h3>
    <div id="translations"></div>

    <script>
        function translateText() {
            const text = document.getElementById('inputText').value;

            // Liste der 25 beliebtesten Sprachen (ISO 639-1 Sprachcodes)
            const languages = [
                'en', // Englisch
                'es', // Spanisch
                'zh', // Chinesisch
                'hi', // Hindi
                'ar', // Arabisch
                'pt', // Portugiesisch
                'ru', // Russisch
                'ja', // Japanisch
                'de', // Deutsch
                'fr', // Französisch
                'it', // Italienisch
                'ko', // Koreanisch
                'tr', // Türkisch
                'vi', // Vietnamesisch
                'pl', // Polnisch
                'nl', // Niederländisch
                'el', // Griechisch
                'he', // Hebräisch
                'th', // Thailändisch
                'sv', // Schwedisch
                'fi', // Finnisch
                'da', // Dänisch
                'no', // Norwegisch
                'hu', // Ungarisch
                'ro'  // Rumänisch
            ];

            const translations = {};
            const translationsDiv = document.getElementById('translations');
            translationsDiv.innerHTML = ''; // Leeren des Divs vor der neuen Ausgabe

            languages.forEach(lang => {
                const url = `https://translate.googleapis.com/translate_a/single?client=gtx&sl=auto&tl=${lang}&dt=t&q=${encodeURIComponent(text)}`;

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        translations[lang] = data[0][0][0];
                        const p = document.createElement('p');
                        p.innerHTML = `<strong>${lang.toUpperCase()}:</strong> ${translations[lang]}`;
                        translationsDiv.appendChild(p);
                    })
                    .catch(error => console.error('Fehler beim Übersetzen:', error));
            });
        }
    </script>
</body>
</html>
