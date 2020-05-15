import findAndReplaceDOMText from 'findandreplacedomtext';

export default function() {
    let translationStrings = {};
    const matches = document.body.innerHTML.matchAll(/[^"](\~\~\#([a-zA-Z0-9\.\-\_]*)\#\~\#(.*?)\#\~\~)[^"]/g);
    for (let match of matches) {
        translationStrings[match[2]] = match[3];

        findAndReplaceDOMText(document, {
            find: match[1],
            replace: function(portion) {
                let element = document.createElement('trans-ui');
                element.setAttribute('data-translation-key', match[2]);
                element.textContent = match[3];
                return element;
            }
        });

        /*
        document.body.innerHTML = document.body.innerHTML.replace(
            match[1],
            '<span data-translation-key="' + match[2] + '">' + match[3] + '</span>'
        );
         */
    }

    return translationStrings;
};
