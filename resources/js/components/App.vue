<template>
    <div class="translator-ui">
        <div class="trans-ui-row">
            <div>
                <select name="key" class="select-list" :size="pageTranslations.length" v-model="activeTranslation" @change="scrollKeyIntoView(activeTranslation.key)">
                    <option v-for="translation in pageTranslations" :value="translation">{{ translation.key }}</option>
                </select>
            </div>
            <div>
                {{ activeTranslation.value }}
            </div>
            <div>
                <tabs v-if="activeTranslationValues" :activeTab="activeLanguage">
                    <tab v-for="(value, language) in activeTranslationValues" :name="language" :id="language">
                        <textarea>{{ value }}</textarea>
                        <button>submit</button>
                    </tab>
                </tabs>
            </div>
        </div>
    </div>
</template>

<script>
    import replacer from "../replacer";
    import Tabs from "./parts/Tabs";
    import Tab from "./parts/Tab";

    export default {
        name: "App",
        components: { Tabs, Tab },
        data: () => ({
            pageTranslations: {},
            activeTranslation: {key: null, value: null },
            allTranslations: {}
        }),
        mounted() {
            this.pageTranslations = replacer(this.pageTranslations);

            //TODO: this should be variable depending on config
            fetch('/inline-translations/all').then(response => {
                response.json().then(json => {
                    this.allTranslations = json;
                });
            });

            // event needs to be on document for this to work
            document.addEventListener('click', (e) => {
                for (let target = e.target; target && target != this && target !== document; target = target.parentNode) {
                    if (target.matches('.trans-ui-element i')) {
                        this.activeTranslation = this.pageTranslations.find(
                            trans => trans.key === target.parentElement.getAttribute('data-translation-key')
                        );

                        break;
                    }
                }
            }, false);
        },
        computed: {
          activeTranslationValues() {
              return this.allTranslations[this.activeTranslation.key];
          },
          activeLanguage() {
            return Object.keys(this.activeTranslationValues)
                .find(language => this.activeTranslationValues[language] === this.activeTranslation.value);
          }
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
