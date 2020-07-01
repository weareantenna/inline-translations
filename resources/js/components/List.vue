<template>
    <div>
        <input class="search-input" v-model="filters.search.value" placeholder="Search..." />

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
                        <translation-cell :locale="locale" :translation="translation" :config="config" />
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
    import TranslationCell from "./parts/TranslationCell";
    export default {
        name: "List",
        components: {TranslationCell},
        data: () => ({
            config: {},
            allTranslations: [],
            loaded: false,
            supportedLocales: [],
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
