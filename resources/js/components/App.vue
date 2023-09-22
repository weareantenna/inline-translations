<template>
    <div class="translator-wrapper" :style="{ 'width': (config.widget_width || 350) + 'px' }">
        <div class="show-hide-btn" @click="toggleShow">
            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="29.981" viewBox="0 0 30 29.981">
                <g id="Group_2701" data-name="Group 2701" transform="translate(-1 -1.045)">
                    <path id="Path_43739" data-name="Path 43739" d="M31,12A11,11,0,0,0,9.43,9H4a3,3,0,0,0-3,3V24a3,3,0,0,0,2.72,3L3,29.76a1,1,0,0,0,1.5,1.11L11.27,27H21a3,3,0,0,0,3-3V22.24A11,11,0,0,0,31,12ZM21,20.67V18h1.92A5.816,5.816,0,0,1,21,20.67ZM15.83,20a9.056,9.056,0,0,1-2.51-2H15A12.047,12.047,0,0,0,15.83,20ZM12,8h2.4a19.186,19.186,0,0,0-.4,3H11A8.877,8.877,0,0,1,12,8Zm17,3H26a19.051,19.051,0,0,0-.38-3H28a8.873,8.873,0,0,1,.93,3Zm-5,0H21V8h2.56A16.976,16.976,0,0,1,24,11Zm-5,0H16a16.958,16.958,0,0,1,.42-3H19Zm0,2v3H16.44A16.976,16.976,0,0,1,16,13Zm-5,0a19.051,19.051,0,0,0,.38,3H12a8.886,8.886,0,0,1-.89-3Zm5,5v2.67A5.816,5.816,0,0,1,17.08,18Zm2-2V13h3a16.958,16.958,0,0,1-.42,3Zm5-3h3a8.877,8.877,0,0,1-1,3H25.6A19.186,19.186,0,0,0,26,13Zm.7-7H25a12.072,12.072,0,0,0-.88-2,9.057,9.057,0,0,1,2.55,2ZM22.92,6H21V3.33A5.816,5.816,0,0,1,22.92,6ZM19,3.33V6H17.08A5.816,5.816,0,0,1,19,3.33ZM15.83,4A12.047,12.047,0,0,0,15,6H13.33A9.063,9.063,0,0,1,15.83,4ZM22,24a1,1,0,0,1-1,1H11a1.006,1.006,0,0,0-.5.13L5.54,28,6,26.24a1,1,0,0,0-.73-1.211A.981.981,0,0,0,5,25H4a1,1,0,0,1-1-1V12a1,1,0,0,1,1-1H9.05c0,.33-.05.66-.05,1A11,11,0,0,0,22,22.81Zm2.17-4A12.047,12.047,0,0,0,25,18h1.63A9.065,9.065,0,0,1,24.17,20Z" fill="#212529"/>
                </g>
            </svg>
        </div>
        <div class="translator-ui">
            <div class="trans-ui-row">
                <div class="translations-list">
                    <table>
                        <thead>
                            <tr>
                                <th @click="sorting = !sorting" class="sortable">
                                    <span v-if="sorting">
                                        <svg viewBox="-32 0 426 427" xmlns="http://www.w3.org/2000/svg"><path d="M91 427c-9 0-16-7-16-16V16a16 16 0 0132 0v395c0 9-7 16-16 16zm0 0"/><path d="M91 427c-4 0-8-2-11-5L5 347a16 16 0 0123-22l63 63 63-63a16 16 0 0123 22l-75 75c-3 3-7 5-11 5zm0 0M347 427h-75a16 16 0 01-14-24l62-115h-48a16 16 0 010-32h75a16 16 0 0114 24l-62 115h48a16 16 0 010 32zm0 0M347 171c-9 0-16-8-16-16V53a21 21 0 00-43 0v102a16 16 0 01-32 0V53a53 53 0 01107 0v102c0 9-7 16-16 16zm0 0"/><path d="M347 112h-75a16 16 0 010-32h75a16 16 0 010 32zm0 0"/></svg>
                                    </span>
                                    <span v-else>
                                    <svg viewBox="-21 0 426 427" xmlns="http://www.w3.org/2000/svg"><path d="M91 427c-9 0-16-7-16-16V16a16 16 0 0132 0v395c0 9-7 16-16 16zm0 0"/><path d="M91 427c-4 0-8-2-11-5L5 347a16 16 0 0123-22l63 63 63-63a16 16 0 0123 22l-75 75c-3 3-7 5-11 5zm0 0M331 171c-9 0-16-8-16-16V32h-21a16 16 0 010-32h37c9 0 16 7 16 16v139c0 9-7 16-16 16zm0 0M347 427h-53a16 16 0 010-32h53c3 0 5-3 5-6v-26h-37c-21 0-37-17-37-38v-32c0-20 16-37 37-37h32c21 0 37 17 37 37v96c0 21-16 38-37 38zm-32-139c-3 0-5 2-5 5v32c0 3 2 6 5 6h37v-38c0-3-2-5-5-5zm0 0"/><path d="M368 171h-74a16 16 0 010-32h74a16 16 0 010 32zm0 0"/></svg>
                                </span> key
                                </th>
                                <th v-for="locale in config['supported-locales']">{{ locale }}</th>
                            </tr>
                        </thead>
                        <tbody name="key" class="select-list">
                            <tr
                                :key="translation.key"
                                v-for="translation in sortedPageTranslations"
                                @click="scrollKeyIntoView(translation.key); activeTranslation = translation"
                                :class="{'active': activeTranslation === translation}"
                                class="select-list--key"
                                :data-key="translation.key"
                            >
                                <td>{{ translation.key }}</td>
                                <td v-for="locale in config['supported-locales']">
                                    <template v-if="isTranslated(translation.key, locale)">
                                        <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="check" class="svg-inline--fa fa-check fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M173.898 439.404l-166.4-166.4c-9.997-9.997-9.997-26.206 0-36.204l36.203-36.204c9.997-9.998 26.207-9.998 36.204 0L192 312.69 432.095 72.596c9.997-9.997 26.207-9.997 36.204 0l36.203 36.204c9.997 9.997 9.997 26.206 0 36.204l-294.4 294.401c-9.998 9.997-26.207 9.997-36.204-.001z"></path></svg>
                                    </template>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div>
                    <tabs
                            v-if="activeTranslationValues"
                            :activeTabName="activeLanguage"
                    >
                        <tab
                                v-for="(language, key) in config['supported-locales']"
                                :key="key"
                                :name="language"
                                :id="language"
                        >
                            <div>
                                <textarea v-model="activeTranslationValues[language]"></textarea>
                            </div>
                            <div>
                                <button class="round" @click="submitTranslation(activeTranslation.key, activeTranslationValues[language], language)">
                                    submit
                                </button>
                                <span class="ml-4 success-message" v-if="submittedSuccessfully" v-html="'Success!'" />
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
            show: true,
            submittedSuccessfully: false,
            sorting: false
        }),
        mounted() {
            this.config = JSON.parse(document.getElementById('antenna-inline-translator').getAttribute('data-config'));
            this.observeDomForNewTranslations();
            this.fetchAllTranslations();
            this.addInlineClickEventListener();
            document.body.classList.add('inline-translations-active');
        },
        computed: {
            basePath() {
                return window.translationModeBaseUrl || '';
            },
            activeLanguage() {
                return this.config.current_language;
            },
            sortedPageTranslations() {
                if (this.sorting) {
                    return this.sortedAlphabeticPageTranslations;
                }

                return this.pageTranslations;
            },
            sortedAlphabeticPageTranslations() {
                function compare(a, b) {
                    if (a.key < b.key)
                        return -1;
                    if (a.key > b.key)
                        return 1;
                    return 0;
                }

                return [...this.pageTranslations].sort(compare);
            }
        },
        watch: {
            activeTranslation: function (activeTranslation) {
                let active = document.querySelectorAll('var.trans-ui-element--active').forEach(element => {
                    element.classList.remove('trans-ui-element--active');
                });
                this.activeTranslationValues = this.allTranslations[activeTranslation.key] || {};

                document.querySelectorAll(`var[data-translation-key="${activeTranslation.key}"]`).forEach(
                    location => {
                        location.classList.add('trans-ui-element--active');
                    }
                );
            },
            pageTranslations: function(pageTranslations) {
                let existingKeys = Object.keys(this.allTranslations);
                let newTranslations = pageTranslations.filter(v => !existingKeys.includes(v.key));
                for (let i = 0; i < newTranslations.length; i++) {
                    this.allTranslations[newTranslations[i].key] = {};
                }

                this.completeTranslations();
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
                fetch(this.basePath + '/' + this.config.routes.prefix + '/all')
                    .then(response => response.json())
                    .then(json => {
                        this.allTranslations = json;
                        this.completeTranslations();
                    });
            },
            completeTranslations() {
                for (let j in this.allTranslations) {
                    for(let i = 0; i < this.config['supported-locales'].length; i++) {
                        let locale = this.config['supported-locales'][i];
                        if (!this.allTranslations[j].hasOwnProperty(locale)) {
                            this.allTranslations[j][locale] = null;
                        }
                    }
                }
            },
            addInlineClickEventListener() {
                // event needs to be on document for this to work
                document.addEventListener('click', (e) => {
                    for (let target = e.target; target && target != this && target !== document; target = target.parentNode) {
                        if (target.matches('.trans-ui-element i')) {
                            e.preventDefault();
                            e.stopPropagation();
                            let key = target.parentElement.getAttribute('data-translation-key');
                            this.activeTranslation = this.pageTranslations.find(
                                trans => trans.key === key
                            );
                            this.scrollSelectListToKey(key);

                            break;
                        }
                    }
                }, false);
            },
            scrollKeyIntoView(key) {
                const element = document.querySelector(`var[data-translation-key="${key}"]`);
                if (element) {
                    element.scrollIntoView({ block: 'center' });
                }
            },
            scrollSelectListToKey(key) {
                const element = document.querySelector(`.select-list--key[data-key="${key}"`);
                if (element) {
                    element.scrollIntoView({ block: 'center', inline: 'start' });
                }
            },
            submitTranslation(key, value, language) {
                if (language === this.activeLanguage) {
                    document.querySelectorAll(`var[data-translation-key="${key}"]`).forEach(element => {
                        element.innerHTML = value + '<i></i>';
                    });
                }

                let postData = new FormData();
                postData.append('key', key);
                postData.append('value', value);
                postData.append('language', language);
                postData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                fetch(this.basePath + '/' + this.config.routes.prefix + '/upsert', {
                    method: 'POST',
                    body: postData
                }).then(response => response.json())
                    .then(json => {
                        if (json.result) {
                            this.submittedSuccessfully = true;
                            fetch(this.basePath + '/' + this.config.routes.prefix + '/trigger-event/update').then(() => {
                                window.setTimeout(()=>{
                                    this.submittedSuccessfully = false;
                                }, 1000);
                            });
                        }
                    });
            },
            toggleShow() {
                this.show = !this.show;
                document.querySelector(".translator-wrapper").classList.toggle("animate");
            },
            isTranslated(key, locale) {
                if (typeof this.allTranslations[key] === 'undefined') {
                    return false;
                }

                return this.allTranslations[key][locale] !== null;
            }
        }
    }
</script>
