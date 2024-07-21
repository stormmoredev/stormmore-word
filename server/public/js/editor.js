function onAboutMeTextChanged(e) {
    return;
    const allowedKeys = [8, 46, 37, 38, 39, 40];
    const input = e.target;
    const maxWords = input.getAttribute('data-maxwords');
    let stats = getTextStatistics(input.value);
    if (stats.words === maxWords) {
        const selectedChars = input.selectionEnd - input.selectionStart;

        /*
        if (!allowedKeys.includes(e.keyCode) && selectedChars == 0) {
             e.preventDefault();
             e.stopPropagation();
        }
         */
    }
    console.log(input.value);

    document.getElementById('about-me-count-words').innerText = stats.words;
}

function getTextStatistics(text) {
    let prev = ' ';
    let characters = '';
    let length = text.length;
    let stats = {
        words: 0
    };

    for(let i = 0; i < length; i++) {
        if (text[i] !== ' ' && prev === ' ') {
            characters += text[i];
            stats.words++;
        } else {
            characters += text[i]
        }

        prev = text[i];
    }

    return stats;
}