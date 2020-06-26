import Vue from 'vue';
import List from './components/List.vue';
import SmartTable from 'vuejs-smart-table'

require('../sass/list.scss');

Vue.use(SmartTable);
new Vue({
    render: h => h(List)
}).$mount('#antenna-inline-translator-list')
