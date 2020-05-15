<template>
    <div class="translator-ui">
        <div class="trans-ui-row">
            <div>
                <select name="key" class="select-list" :size="translations.length" v-model="activeTranslation">
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
        }
    }
</script>
