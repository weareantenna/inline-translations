<template>
    <div class="translator-ui">
        <div class="trans-ui-row">
            <div>
                <select name="key" class="select-list" :size="translations.length" v-model="activeTranslation">
                    <option v-for="translation in translations" :value="translation">{{ translation.key }}</option>
                </select>
            </div>
            <div>
                {{ activeTranslation.value }}
            </div>
            <div>
            </div>
        </div>
    </div>
</template>

<script>
    import replacer from "../replacer";
    export default {
        name: "App",
        data: () => ({
            translations: {},
            activeTranslation: {key: null, value: null }
        }),
        mounted() {
            this.translations = replacer(this.translations);
        },
        watch: {
            activeTranslation: (activeTranslation) => {
                let active = document.querySelectorAll('var.trans-ui-element--active');
                for (let i = 0; i < active.length; i++) {
                    active[i].classList.remove('trans-ui-element--active');
                }

                let locations = document.querySelectorAll(`var[data-translation-key="${activeTranslation.key}"]`);
                for (let i = 0; i < locations.length; i++) {
                    locations[i].classList.add('trans-ui-element--active');
                }
            }
        }
    }
</script>
