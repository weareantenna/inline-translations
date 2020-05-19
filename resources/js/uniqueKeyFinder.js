export default function (translationStrings) {
    let uniqueTranslationStrings = [];
    for(let i = 0; i < translationStrings.length; i++) {
        if(!uniqueTranslationStrings.map(v => v.key).includes(translationStrings[i].key)) {
            uniqueTranslationStrings.push(translationStrings[i]);
        }
    }

    return uniqueTranslationStrings;
}
