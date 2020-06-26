<template>
    <div>
        <input class="form-control" v-model="filters.search.value" placeholder="Search..." />

        <v-table
                :data="allTranslations"
                :filters="filters"
                :currentPage.sync="currentPage"
                :pageSize="50"
                @totalPagesChanged="totalPages = $event"
        >
            <thead slot="head">
                <tr>
                    <v-th sort-key="key">Key</v-th>
                    <v-th :sort-key="locale" v-for="locale in supportedLocales" :key="locale">{{ locale }}</v-th>
                </tr>
            </thead>
            <tbody slot="body" slot-scope="{displayData}">
                <tr v-for="translation in displayData" :key="translation.key">
                    <td>{{ translation.key }}</td>
                    <td v-for="locale in supportedLocales">
                        <span @click="editKey(translation.key, locale)" v-if="!isEditing(translation, locale)">
                            {{ translation[locale] }}
                        </span>
                        <span v-else>
                            <input type="text" v-model="translation[locale]" />
                            <a @click="saveUpdate(translation.key, locale, translation[locale])">submit</a>
                        </span>
                    </td>
                </tr>
            </tbody>
        </v-table>

        <smart-pagination
            :currentPage.sync="currentPage"
            :totalPages="totalPages"
            :maxPageLinks="10"
        />
    </div>
</template>
<script>
    export default {
        name: "List",
        data: () => ({
            config: {},
            allTranslations: [],
            loaded: false,
            supportedLocales: [],
            editing: {},
            filters: { search: { value: '', keys: [] }},
            currentPage: 1,
            totalPages: 0,
            submittedSuccessfully: false
        }),
        mounted() {
            this.config = window.config;
            this.supportedLocales = this.config['supported-locales'];
            this.fetchAllTranslations();
            this.filters.search.keys = ['key'].concat(this.supportedLocales);
        },
        methods: {
            isEditing(translation, locale) {
                return translation.key === this.editing.key && locale === this.editing.locale;
            },
            editKey(key, locale) {
                this.editing = {
                    'key': key,
                    'locale': locale
                };
            },
            saveUpdate(key, locale, newValue) {
                let postData = new FormData();
                postData.append('key', key);
                postData.append('value', newValue);
                postData.append('language', locale);
                postData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                fetch('/' + this.config.routes.prefix + '/upsert', {
                    method: 'POST',
                    body: postData
                }).then(response => response.json())
                        .then(json => {
                            if (json.result) {
                                this.submittedSuccessfully = true;
                                fetch('/' + this.config.routes.prefix + '/trigger-event/update').then(() => {
                                    window.setInterval(()=>{
                                        this.submittedSuccessfully = false;
                                    }, 3000);
                                });
                            }
                        });

                this.editing = {};
            },
            fetchAllTranslations() {
                fetch('/' + this.config.routes.prefix + '/all')
                    .then(response => response.json())
                    .then(json => {
                        this.allTranslations = this.completeTranslations(json);
                        this.loaded = true;
                    });
            },
            completeTranslations(allTranslations) {
                for (let j in allTranslations) {
                    allTranslations[j].key = j;
                    for(let i = 0; i < this.config['supported-locales'].length; i++) {
                        let locale = this.config['supported-locales'][i];
                        if (!allTranslations[j].hasOwnProperty(locale)) {
                            allTranslations[j][locale] = null;
                        }
                    }
                }

                return Object.values(allTranslations);
            }
        }
    }
</script>
