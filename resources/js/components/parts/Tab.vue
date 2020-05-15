<template>
    <div
            v-if="isVisible"
            :id="computedId"
            class="tab-pane"
            :class="{ 'active': isActive }"
            role="tabpanel"
    >
        <slot />
    </div>
</template>

<script>
    export default {
        name: "Tab",
        props: {
            id: { default: '', type: String },
            name: { required: true, type: String },
            isDisabled:{ default: false, type: Boolean },
        },
        data: () => ({
            isActive: false,
            isVisible: true,
        }),
        computed: {
            computedId() {
                return this.id ? this.id : this.name.toLowerCase().replace(/ /g, '-');
            },
            hash() {
                if (this.isDisabled) {
                    return '#';
                }
                return '#' + this.computedId;
            },
        }
    }
</script>
