<template>
    <div class="translator-ui">
        <div class="trans-ui-row">
            <div>
                <select name="key" class="select-list" :size="translations.length" v-model="activeTranslation" @change="scrollKeyIntoView(activeTranslation.key)">
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

            // event needs to be on document for this to work
            document.addEventListener('click', (e) => {
                for (let target = e.target; target && target != this && target !== document; target = target.parentNode) {
                    if (target.matches('.trans-ui-element i')) {
                        this.activeTranslation = this.translations.find(
                            trans => trans.key === target.parentElement.getAttribute('data-translation-key')
                        );

                        break;
                    }
                }
            }, false);
        },
        watch: {
            activeTranslation: (activeTranslation) => {
                let active = document.querySelectorAll('var.trans-ui-element--active').forEach(element => {
                    element.classList.remove('trans-ui-element--active');
                });



                document.querySelectorAll(`var[data-translation-key="${activeTranslation.key}"]`).forEach(
                    location => {
                        location.classList.add('trans-ui-element--active');
                    }
                );
            }
        },
        methods: {
            scrollKeyIntoView(key) {
                document.querySelector(`var[data-translation-key="${key}"]`).scrollIntoView();
                window.scrollBy(0, -20);
            }
        }
    }
</script>
