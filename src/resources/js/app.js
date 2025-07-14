/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
import { createApp } from 'vue';

/**
 * Next, we will create a fresh Vue application instance. You may then begin
 * registering components with the application instance so they are ready
 * to use in your application's views. An example is included for you.
 */

const app = createApp({});

import LoginComponent from './components/Login.vue';
import HomeComponent from './components/Home.vue';
import TravelRequestComponent from './components/TravelRequest.vue';

app.component('login-component', LoginComponent);
app.component('home-component', HomeComponent);
app.component('travel-request-component', TravelRequestComponent);

app.mount('#app');
