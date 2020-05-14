window.translationStrings = {};
window.inlineTranslations = (function() {
    var matches = document.body.innerHTML.matchAll(/[^"](\~\~\#([a-zA-Z0-9\.\-\_]*)\#\~\#(.*?)\#\~\~)[^"]/gs);
    for (var match of matches) {
        window.translationStrings[match[2]] = match[3];
        document.body.innerHTML = document.body.innerHTML.replace(
            match[1],
            '<span data-translation-key="' + match[2] + '">' + match[3] + '</span>'
        );
    }
})();
