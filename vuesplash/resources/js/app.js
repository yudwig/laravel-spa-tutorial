import './bootstrap'
import Vue from 'vue';
import router from './router.js';
import store from './store';
import App from './App.vue';

new Vue({
	el: '#app',
	router,
    store,
	components: {App},
	template: '<App />'
});

