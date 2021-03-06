import findAndReplaceDOMText from 'findandreplacedomtext';

export default function(target) {
    let translationStrings = [];
    const matches = target.innerHTML.matchAll(/(\~\~\#([a-zA-Z0-9._-]*)\#\~\#(.*?)\#\~\~)/gs);
    for (let match of matches) {
        translationStrings.push({
            key: match[2],
            value: match[3]
        });
    }

    findAndReplaceDOMText(target, {
        find: /\~\~\#([a-zA-Z0-9._-]*)\#\~\#(.*?)\#\~\~/gs,
        replace: function(node, match) {
            if (node.text !== match[0]) {
                return node.text;
            }

            let element = document.createElement('var');
            element.setAttribute('data-translation-key', match[1]);
            element.classList.add('trans-ui-element');
            element.innerHTML = match[2];

            element.appendChild(document.createElement('i'));
            return element;
        }
    });

    return translationStrings;
};
