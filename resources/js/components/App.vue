<template>
    <div class="translator-ui">
        <div class="trans-ui-row">
            <div>
                <select name="key">
                    <option v-for="(key, translation) in translations"> {{ key }}</option>
                </select>
            </div>
            <div>
                <div v-for="translation in translations">
                    {{ translation }}
                </div>
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
            translations: {}
        }),
        mounted() {
            this.translations = replacer(this.translations);
            console.log(this.translations);
        }
    }
</script>
