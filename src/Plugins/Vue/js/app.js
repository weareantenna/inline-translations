const VInlineTranslations                        = {
    install(Vue, options) {
        Vue.prototype.$parentTranslationFunction = Vue.options.methods[options.methodName];

        Vue.mixin({
            methods: {
                [options.methodName]: function (key, ...values) {
                    const translation = this.$parentTranslationFunction(key, ...values);

                    if (window.translationModeActive !== true) {
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
