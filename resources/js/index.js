import Vue from 'vue';
import App from './components/App.vue';

require('../sass/app.scss');

new Vue({
    render: h => h(App)
}).$mount('#antenna-inline-translator-app')
