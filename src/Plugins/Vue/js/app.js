const VInlineTranslations = {
    install(Vue, options) {
        Vue.mixin({
            methods: {
                [options.methodName]: function (key) {
                    const translation = this.$t(key);

                    if (window.translationModeActive === 0) {
                        return translation;
                    }

                    if (translation) {
                        return `~~#${key}#~#${htmlEntities(translation)}#~~`;
                    }

                    return `~~#${key}#~~`;
                }
            }
        });
    }
};

function htmlEntities(str)
{
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

export default VInlineTranslations;
