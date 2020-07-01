<template>
    <div class="translation-slot">
        <span @click="editing = true">
            {{ translation[locale] || '--- Not Translated ---' }}
        </span>
        <template v-if="editing">
            <div class="edit-overlay" @click="editing = false"></div>
            <div class="edit-popup">
                <textarea v-model="translation[locale]"></textarea>
                <a @click="saveUpdate(translation.key, locale, translation[locale])">submit</a>
            </div>
        </template>
        <span class="loading-spinner" v-if="saving">
            <svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid"><circle cx="50" cy="50" fill="none" stroke="#06f9ae" stroke-width="15" r="15" stroke-dasharray="70.68583470577033 25.561944901923447" transform="rotate(221.97 50 50)"><animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1s" values="0 50 50;360 50 50" keyTimes="0;1"/></circle></svg>
        </span>
    </div>
</template>

<script>
    export default {
        name: "TranslationCell",
        props: {
            locale: { default: null, type: String },
            translation: { default: null, type: Object },
            config: { default: null, type: Object }
        },
        data: () => ({
            editing: false,
            saving: false
        }),
        methods: {
            saveUpdate(key, locale, newValue) {
                this.saving = true;
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
                                window.setTimeout(()=>{
                                    this.submittedSuccessfully = false;
                                    this.saving = false;
                                }, 1000);
                            });
                        }
                    });

                this.editing = false;
            },
        }
    }
</script>
