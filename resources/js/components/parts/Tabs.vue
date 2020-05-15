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
            activeTabName: { default: null, type: String }
        },
        data: () => ({
            tabs: [],
            activeTab: null
        }),
        computed: {
            visibleTabs() {
                return this.tabs.filter(tab => tab.isVisible);
            }
        },
        created() {
            this.tabs = this.$children;
        },
        watch: {
            tabs: function(tabs) {
                this.setTabActive(this.tabs.find((tab) => (tab.id === this.activeTabName)));
            }
        },
        methods: {
            setTabActive(tab) {
                if (!tab) {
                    return;
                }

                this.tabs.forEach((tab) => { tab.isActive = false; });

                tab.isActive = true;
                this.activeTab = tab;
            }
        }
    }
</script>
