import './bootstrap';

import { createApp, h } from 'vue'

import jQuery from 'jquery';
window.$ = jQuery;

import {createInertiaApp, Head, Link} from '@inertiajs/vue3'
import Layout from "@/Shared/Layout.vue";

createInertiaApp({
	resolve: async name => {
		//For separated js
		const pages = import.meta.glob('./Pages/**/*.vue')
		let page = await pages[`./Pages/${name}.vue`]();
		
		//Set default layout for all the pages
		if(page.default.layout === undefined) {
			page.default.layout = Layout;
		}
		
		return page;
	},
	setup({ el, App, props, plugin }) {
		createApp({ render: () => h(App, props) })
		.use(plugin)
		.component('Link', Link)
		.component('Head', Head)
		.mount(el)
	},
})