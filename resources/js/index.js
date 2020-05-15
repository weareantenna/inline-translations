import Vue from 'vue'
import AppComponent from './components/App.vue'

require('../sass/app.scss');
require('./replacer.js');

new Vue({
    render: h => h(AppComponent)
}).$mount('#antenna-inline-translator-app')
