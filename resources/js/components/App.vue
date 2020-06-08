<template>
    <div class="translator-wrapper">
        <div class="show-hide-btn" @click="toggleShow">
            <svg height="512" width="512" x="0px" y="0px" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path d="M349.6 162.5a175 175 0 10-187.1 187A175.2 175.2 0 00337 512c96.5 0 175-78.5 175-175 0-92.3-71.8-168-162.4-174.5zM30 175a145 145 0 01289.5-12.1c-35.8 3.5-68.5 18-94.7 40L190.2 99.2a15 15 0 00-28.4 0l-48 144a15 15 0 0028.4 9.4l7.3-21.7h48.4a174 174 0 00-35 88.5A145 145 0 0130 175zm162.5 26h-33l16.5-49.6zM337 482c-80 0-145-65-145-145s65-145 145-145 145 65 145 145-65 145-145 145z"/><path d="M312 265h-40a15 15 0 000 30h24.2c-2.3 25.7-10.7 67-33.6 85.3a15 15 0 1018.8 23.4c45-36 45.6-120.1 45.6-123.7a15 15 0 00-15-15zM400 305h-17v-41a15 15 0 00-30 0v144a15 15 0 0030 0v-73h17a15 15 0 000-30zM384 63a65 65 0 0165 65 15 15 0 0030 0c0-52.4-42.6-95-95-95a15 15 0 000 30zM128 449a65 65 0 01-65-65 15 15 0 00-30 0c0 52.4 42.6 95 95 95a15 15 0 000-30z"/></svg>
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
