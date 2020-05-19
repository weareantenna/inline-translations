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
                        return `~~#${key}#~#${translation}#~~`;
                    }

                    return `~~#${key}#~~`;
                }
            }
        });
    }
};

export default VInlineTranslations;
