export default function() {
    let translationStrings = {};
    const matches = document.body.innerHTML.matchAll(/[^"](\~\~\#([a-zA-Z0-9\.\-\_]*)\#\~\#(.*?)\#\~\~)[^"]/g);
    for (let match of matches) {
        console.log(match);
        translationStrings[match[2]] = match[3];
        document.body.innerHTML = document.body.innerHTML.replace(
            match[1],
            '<span data-translation-key="' + match[2] + '">' + match[3] + '</span>'
        );
    }

    return translationStrings;
};
