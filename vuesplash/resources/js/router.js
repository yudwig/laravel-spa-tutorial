import Vue from 'vue';
import VueRouter from 'vue-router';
import PhotoList from './pages/PhotoList.vue';
import Login from './pages/Login.vue';
import store from './store';

Vue.use(VueRouter);

const routes = [
	{
		path: '/',
		component: PhotoList
	},
	{
		path: '/login',
		component: Login,
        beforeEnter(to, from, next) {
		    // ログイン済みなのにURL直指定でログイン画面にきた場合はトップページに移動させる。
		    if (store.getters['auth/check']) {
		        next('/');
            } else {
		        next();
            }
        }
	}
];

const router = new VueRouter({
	mode: 'history',
	routes
});

export default router
