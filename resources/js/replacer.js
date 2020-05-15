import findAndReplaceDOMText from 'findandreplacedomtext';

export default function() {
    let translationStrings = [];
    const matches = document.body.innerHTML.matchAll(/[^"](\~\~\#([a-zA-Z0-9\.\-\_]*)\#\~\#(.*?)\#\~\~)[^"]/g);
    for (let match of matches) {
        translationStrings.push({
            key: match[2],
            value: match[3]
        });

        findAndReplaceDOMText(document, {
            find: match[1],
            replace: function(portion) {
                let element = document.createElement('var');
                element.setAttribute('data-translation-key', match[2]);
                element.style.fontStyle = 'inherit';
                element.textContent = match[3];
                return element;
            }
        });
    }

    return translationStrings;
};
