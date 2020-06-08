<template>
    <div class="translator-wrapper">
        <div class="show-hide-btn" @click="toggleShow">
            <span v-if="show">Hide</span>
            <span v-else>Show</span>
        </div>
        <div class="translator-ui">
            <div class="trans-ui-row">
                <div class="translations-list">
                    <select name="key" class="select-list" :size="pageTranslations.length"
                            v-model="activeTranslation" @change="scrollKeyIntoView(activeTranslation.key)">
                        <option :key="translation.key" v-for="translation in pageTranslations" :value="translation">
                            {{ translation.key }}
                        </option>
                    </select>
                </div>
                <div>
                    <tabs v-if="activeTranslationValues" :activeTabName="activeLanguage">
                        <tab :key="language" v-for="(value, language) in activeTranslationValues" :name="language"
                             :id="language">
                            <div>
                                <textarea v-model="activeTranslationValues[language]"></textarea>
                            </div>
                            <div>
                                <button @click="submitTranslation(activeTranslation.key, activeTranslationValues[language], language)">
                                    submit
                                </button>
                            </div>
                        </tab>
                    </tabs>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import replacer from "../replacer";
    import Tabs from "./parts/Tabs";
    import Tab from "./parts/Tab";
    import uniqueKeyFinder from "../uniqueKeyFinder";

    export default {
        name: "App",
        components: {Tabs, Tab},
        data: () => ({
            config: {},
            pageTranslations: [],
            activeTranslation: {key: null, value: null},
            allTranslations: {},
            translationValue: null,
            activeTranslationValues: [],
            observerConfig: {childList: true, subtree: true},
            show: true
        }),
        mounted() {
            this.config = JSON.parse(document.getElementById('antenna-inline-translator').getAttribute('data-config'));
            this.observeDomForNewTranslations();
            this.fetchAllTranslations();
            this.addInlineClickEventListener();
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

                this.activeTranslationValues = this.allTranslations[activeTranslation.key] || {};
                for(let i = 0; i < this.config['supported-locales'].length; i++) {
                    let locale = this.config['supported-locales'][i];
                    if (!this.activeTranslationValues.hasOwnProperty(locale)) {
                        this.activeTranslationValues[locale] = null;
                    }
                }

                document.querySelectorAll(`var[data-translation-key="${activeTranslation.key}"]`).forEach(
                    location => {
                        location.classList.add('trans-ui-element--active');
                    }
                );
            }
        },
        methods: {
            observeDomForNewTranslations() {
                const observer = new MutationObserver((mutationsList, observer) => {
                    observer.disconnect();
                    for (let mutation of mutationsList) {
                        if (mutation.type === 'childList') {
                            this.pageTranslations = uniqueKeyFinder([...this.pageTranslations, ...replacer(mutation.target)]);
                        }
                    }
                    observer.observe(document, this.observerConfig);
                });
                observer.observe(document, this.observerConfig);
            },
            fetchAllTranslations() {
                fetch('/' + this.config.prefix + '/all')
                    .then(response => response.json())
                    .then(json => {
                        this.allTranslations = json;
                    });
            },
            addInlineClickEventListener() {
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
            scrollKeyIntoView(key) {
                const element = document.querySelector(`var[data-translation-key="${key}"]`);
                if (element) {
                    element.scrollIntoView();
                    window.scrollBy(0, -20);
                }
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
            },
            toggleShow() {
                this.show = !this.show;
                document.querySelector(".translator-wrapper").classList.toggle("animate");
            }
        }
    }
</script>
