/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
import { createApp } from 'vue';
import { createStore } from 'vuex';

const store = createStore({
  state () {
    return {
      item: {},
      transaction: {status: '', message: '', data: ''},
      user: window.AuthUser,
    }
  },
})

/**
 * Next, we will create a fresh Vue application instance. You may then begin
 * registering components with the application instance so they are ready
 * to use in your application's views. An example is included for you.
 */

const app = createApp({});

import LoginComponent from './components/Login.vue';
import HomeComponent from './components/Home.vue';
import TravelRequestComponent from './components/TravelRequest.vue';
import InputContainerComponent from './components/InputContainer.vue';
import TableComponent from './components/Table.vue';
import CardComponent from './components/Card.vue';
import ModalComponent from './components/Modal.vue';
import AlertComponent from './components/Alert.vue';
import PaginationComponent from './components/Pagination.vue';
import DateFilterComponent from './components/DateFilter.vue';
import LoadingOverlayComponent from './components/LoadingOverlay.vue';
import ToastComponent from './components/Toast.vue';
import ToastContainerComponent from './components/ToastContainer.vue';

app.component('login-component', LoginComponent);
app.component('home-component', HomeComponent);
app.component('travel-request-component', TravelRequestComponent);
app.component('input-container-component', InputContainerComponent);
app.component('table-component', TableComponent);
app.component('card-component', CardComponent);
app.component('modal-component', ModalComponent);
app.component('alert-component', AlertComponent);
app.component('pagination-component', PaginationComponent);
app.component('date-filter-component', DateFilterComponent);
app.component('loading-overlay-component', LoadingOverlayComponent);
app.component('toast-component', ToastComponent);
app.component('toast-container-component', ToastContainerComponent);

app.use(store);

app.mount('#app');
