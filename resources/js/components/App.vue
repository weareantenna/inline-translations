<template>
    <div class="translator-ui">
        <div class="trans-ui-row">
            <div>
                <select name="key" class="select-list" :size="pageTranslations.length" v-model="activeTranslation" @change="scrollKeyIntoView(activeTranslation.key)">
                    <option v-for="translation in pageTranslations" :value="translation">{{ translation.key }}</option>
                </select>
            </div>
            <div class="trans-ui-translation">
                {{ activeTranslation.value }}
            </div>
            <div>
                <tabs v-if="activeTranslationValues" :activeTabName="activeLanguage">
                    <tab v-for="(value, language) in activeTranslationValues" :name="language" :id="language">
                        <div>
                            <textarea v-model="activeTranslationValues[language]"></textarea>
                        </div>
                        <div>
                            <button @click="submitTranslation(activeTranslation.key, activeTranslationValues[language], language)">submit</button>
                        </div>
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
            config: {},
            pageTranslations: {},
            activeTranslation: {key: null, value: null },
            allTranslations: {},
            translationValue: null,
            activeTranslationValues: []
        }),
        mounted() {
            this.pageTranslations = replacer(this.pageTranslations);
            this.config = JSON.parse(document.getElementById('antenna-inline-translator').getAttribute('data-config'));

            fetch('/' + this.config.prefix + '/all')
                    .then(response => response.json())
                    .then(json => { this.allTranslations = json; });

            // event needs to be on document for this to work
            document.addEventListener('click', (e) => {
                for (let target = e.target; target && target != this && target !== document; target = target.parentNode) {
                    if (target.matches('.trans-ui-element i')) {
                        e.preventDefault();
                        this.activeTranslation = this.pageTranslations.find(
                            trans => trans.key === target.parentElement.getAttribute('data-translation-key')
                        );

                        break;
                    }
                }
            }, false);
        },
        computed: {
          activeLanguage() {
            return this.config.current_language;
          }
        },
        watch: {
            activeTranslation: function (activeTranslation) {
                let active = document.querySelectorAll('var.trans-ui-element--active').forEach(element => {
                    element.classList.remove('trans-ui-element--active');
                });

                this.activeTranslationValues = this.allTranslations[activeTranslation.key];

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
            },
            submitTranslation(key, value, language) {
                if (language = this.activeLanguage) {
                    document.querySelectorAll(`var[data-translation-key="${key}"]`).forEach(element => {
                        element.innerHTML = value + '<i></i>';
                    });
                }

                let postData = new FormData();
                postData.append('key', key);
                postData.append('value', value);
                postData.append('language', language);
                fetch('/' + this.config.prefix + '/upsert', {
                    method: 'POST',
                    body: postData
                }).then(response => response.json())
                .then(json => {
                    if (json.result) {
                        window.alert('success');
                    }
                });
            }
        }
    }
</script>
