<template>
    <div class="translator-ui">
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
        }
    }
</script>
