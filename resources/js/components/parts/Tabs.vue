<template>
    <div class="translation-ui-tabs-wrapper">
        <header>
            <ul
                    class="translation-ui-tabs-nav"
                    role="tablist"
            >
                <li
                        v-for="(tab, i) in visibleTabs"
                        :key="i"
                        role="presentation"
                >
                    <a
                            :class="{ 'active': tab.isActive }"
                            data-toggle="tab"
                            role="tab"
                            @click="setTabActive(tab)"
                    >{{ tab.name }}</a>
                </li>
            </ul>
        </header>
        <div class="translation-ui-tab-content">
            <slot />
        </div>
    </div>
</template>

<script>
    export default {
        name: "Tabs",
        props: {
            activeTab: { default: '', type: String }
        },
        data: () => ({
            tabs: [],
        }),
        computed: {
            visibleTabs() {
                return this.tabs.filter(tab => tab.isVisible);
            }
        },
        created() {
            this.tabs = this.$children;
        },
        mounted() {
            if (this.activeTab) {
                this.tabs.find((tab) => (tab.id === this.activeTab)).isActive = true;
            } else {
                this.tabs[0].isActive = true;
            }
        },
        methods: {
            setTabActive(tab) {
                this.tabs.forEach((tab) => { tab.isActive = false; });

                tab.isActive = true;
                this.activeTab = tab;
            }
        }
    }
</script>
