<template>
    <div>
      <div class="header">
        <input class="search-input" v-model="filters.search.value" placeholder="Search..." />
        <div @click="makeStateClean" class="refresh-button">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor"><path d="M493.815 70.629c-11.001-1.003-20.73 7.102-21.733 18.102l-2.65 29.069C424.473 47.194 346.429 0 256 0 158.719 0 72.988 55.522 30.43 138.854c-5.024 9.837-1.122 21.884 8.715 26.908 9.839 5.024 21.884 1.123 26.908-8.715C102.07 86.523 174.397 40 256 40c74.377 0 141.499 38.731 179.953 99.408l-28.517-20.367c-8.989-6.419-21.48-4.337-27.899 4.651-6.419 8.989-4.337 21.479 4.651 27.899l86.475 61.761c12.674 9.035 30.155.764 31.541-14.459l9.711-106.53c1.004-11.001-7.1-20.731-18.1-21.734zM472.855 346.238c-9.838-5.023-21.884-1.122-26.908 8.715C409.93 425.477 337.603 472 256 472c-74.377 0-141.499-38.731-179.953-99.408l28.517 20.367c8.989 6.419 21.479 4.337 27.899-4.651 6.419-8.989 4.337-21.479-4.651-27.899l-86.475-61.761c-12.519-8.944-30.141-.921-31.541 14.459L.085 419.637c-1.003 11 7.102 20.73 18.101 21.733 11.014 1.001 20.731-7.112 21.733-18.102l2.65-29.069C87.527 464.806 165.571 512 256 512c97.281 0 183.012-55.522 225.57-138.854 5.024-9.837 1.122-21.884-8.715-26.908z"/></svg>
        </div>
      </div>

        <v-table
                :data="filteredTranslations"
                :filters="filters"
                :currentPage.sync="currentPage"
                :pageSize="50"
                @totalPagesChanged="totalPages = $event"
        >
            <thead slot="head">
                <tr>
                    <v-th sort-key="key">Key</v-th>
                    <v-th :sort-key="locale" v-for="locale in supportedLocales" :key="locale">
                      {{ locale }}<br>
                      <span :for="`only_empty_${locale}`" class="empty-show" @click.stop="changeEmpty(locale)">
                        <input type="checkbox" :id="`only_empty_${locale}`" :checked="onlyEmpty.includes(locale)" /> only show empty
                      </span>
                    </v-th>
                </tr>
            </thead>
            <tbody slot="body" slot-scope="{displayData}">
                <tr v-for="translation in displayData" :key="translation.key">
                    <td>{{ translation.key }}</td>
                    <td v-for="locale in supportedLocales">
                        <translation-cell :locale="locale" :translation="translation" :config="config" @saved="cleanState = false" />
                    </td>
                </tr>
            </tbody>
        </v-table>

        <smart-pagination
            :currentPage.sync="currentPage"
            :totalPages="totalPages"
            :maxPageLinks="10"
        />

      <div class="fullscreen-loader" v-if="!loaded">
        <span class="loading-spinner">
            <svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid"><circle cx="50" cy="50" fill="none" stroke="#06f9ae" stroke-width="15" r="15" stroke-dasharray="70.68583470577033 25.561944901923447" transform="rotate(221.97 50 50)"><animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1s" values="0 50 50;360 50 50" keyTimes="0;1"/></circle></svg>
        </span>
      </div>
      <div class="export-import-wrapper">
        <input name="translations" @change="filePicked" ref="translationCsvPick" type="file" hidden>
        <button class="button" @click="pickFile">Import</button>
        <a class="button" :href="config.exportUrl">Export</a>
      </div>
    </div>
</template>
<script>
    import TranslationCell from "./parts/TranslationCell";
    export default {
        name: "List",
        components: {TranslationCell},
        data() {
          return {
            config: {},
            allTranslations: [],
            loaded: false,
            supportedLocales: [],
            filters: {
              search: { value: '', keys: [] },
              incomplete: { value: false, custom: this.emptyFilter }
            },
            currentPage: 1,
            totalPages: 0,
            submittedSuccessfully: false,
            cleanState: true,
            onlyEmpty: [],
          };
        },
        mounted() {
            this.config = window.config;
            this.supportedLocales = this.config['supported-locales'];
            this.fetchAllTranslations();
            this.filters.search.keys = ['key'].concat(this.supportedLocales.map(v => '_old_' + v));
        },
        computed: {
          filteredTranslations() {
            if (this.onlyEmpty.length === 0) {
              return this.allTranslations;
            }

            return this.allTranslations.filter(row => {
              for(let i = 0; i < this.onlyEmpty.length; i++) {
                let locale = this.onlyEmpty[i];
                if (row['_old_' + locale] === null) {
                  return true;
                }
              }

              return false;
            });
          }
        },
        methods: {
            pickFile() {
              this.$refs.translationCsvPick.click();
            },
            filePicked(e) {
              this.loaded = false;
              const file = e.target.files[0];
              const formData = new FormData();
              formData.append('translations', file);

              fetch('/' + this.config.routes.prefix + '/import', {
                method: 'POST',
                body: formData
              })
                .then(response => {
                  this.$refs.translationCsvPick.value = '';
                  return response;
                })
                .then(response => response.json())
                .then(json => {
                  if (json.result !== 'success') {
                    console.error(json);
                  }

                  setTimeout(() => {
                        this.fetchAllTranslations();
                      }
                      ,3000);
                });

            },
            changeEmpty(locale) {
              const index = this.onlyEmpty.indexOf(locale);
              if (index > -1) {
                this.onlyEmpty.splice(index, 1);
              } else {
                this.onlyEmpty.push(locale);
              }
            },
            fetchAllTranslations() {
                fetch('/' + this.config.routes.prefix + '/all')
                    .then(response => response.json())
                    .then(json => {
                        this.allTranslations = this.completeTranslations(json);
                        this.loaded = true;
                    });
            },
            makeStateClean() {
              for (let j in this.allTranslations) {
                for(let i = 0; i < this.config['supported-locales'].length; i++) {
                  let locale = this.config['supported-locales'][i];
                  this.allTranslations[j]['_old_' + locale] = this.allTranslations[j][locale];
                }
              }
              this.cleanState = true;
            },
            completeTranslations(allTranslations) {
                for (let j in allTranslations) {
                    allTranslations[j].key = j;
                    for(let i = 0; i < this.config['supported-locales'].length; i++) {
                        let locale = this.config['supported-locales'][i];
                        if (!allTranslations[j].hasOwnProperty(locale)) {
                            allTranslations[j][locale] = null;
                        }

                        allTranslations[j]['_old_' + locale] = allTranslations[j][locale];
                    }
                }

                return Object.values(allTranslations);
            }
        }
    }
</script>
