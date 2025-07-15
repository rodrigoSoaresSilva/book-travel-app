<template>
    <div class="container">
        <loading-overlay-component v-if="showLoading"></loading-overlay-component>

        <div class="row justify-content-center">
            <div class="col-md-8 mb-3">
                <card-component title="Solicitações de viagem">
                    <template v-slot:content>

                        <button v-if="urlFilter != ''" type="button" @click="clearFilter()" class="btn btn-primary btn-sm ms-2 float-end">Limpar filtros</button>
                        <button type="button" class="btn btn-primary btn-sm float-end" data-bs-toggle="modal" data-bs-target="#modalSearchTravelRequest">Filtrar</button>

                        <table-component
                            :data="travelRequests.data"
                            :view="{visible: true, dataToggle: 'modal', dataTarget: '#modalViewTravelRequest'}"
                            :update="{visible: true, dataToggle: 'modal', dataTarget: '#modalUpdateTravelRequest'}"
                            :remove="{visible: true, dataToggle: 'modal', dataTarget: '#modalRemoveTravelRequest'}"
                            :titles="{
                                id: {title: 'ID', type: 'text'},
                                created_at: {title: 'Data Pedido', type: 'date'},
                                destination: {title: 'Destino', type: 'text'},
                                departure_date: {title: 'Saída', type: 'date'},
                                return_date: {title: 'Retorno', type: 'date'},
                                status: {
                                    title: 'Status', 
                                    type: 'text', 
                                    cast: statuses
                                },
                            }">
                        </table-component>
                    </template>
                    <template v-slot:footer>
                        <div class="row">
                            <div class="col-10">
                                <pagination-component>
                                    <li v-for="link, key in travelRequests.links" :key="key" :class="link.active ? 'page-item active' : 'page-item'" @click="paginate(link)">
                                        <a class="page-link" v-html="link.label"></a>
                                    </li>
                                </pagination-component>
                            </div>
                            <div class="col">
                                <button type="button" class="btn btn-primary btn-sm float-end" data-bs-toggle="modal" data-bs-target="#modalCreateTravelRequest">+ Novo Pedido</button>
                            </div>
                        </div>
                    </template>
                </card-component>
            </div>
        </div>

        <!-- início modal buscar -->
        <modal-component id="modalSearchTravelRequest" title="Filtrar pedidos">

            <template v-slot:content>
                <div class="row g-3 mb-3">
                    <div class="col-8">
                        <input-container-component
                        title="Destino"
                        id="inputDestino"
                        >
                        <input
                            type="text"
                            class="form-control"
                            id="inputDestino"
                            placeholder="Destino"
                            v-model="search.destination"
                        >
                        </input-container-component>
                    </div>
                    <div class="col-4">
                        <input-container-component
                            title="Status"
                            id="inputStatus"
                        >
                            <select
                                class="form-control"
                                id="inputStatus"
                                v-model="search.status"
                            >
                                <option value="">Todos</option>
                                <option v-for="(label, value) in statuses" :value="value" :key="value">
                                     {{ label }}
                                </option>
                            </select>
                        </input-container-component>
                    </div>
                    <div class="col-12">
                        <date-filter-component
                            title="Data de Ida"
                            id="departure_date"
                            v-model="search.departure_date"
                        />
                    </div>
                    <div class="col-12">
                        <date-filter-component
                            title="Data de Volta"
                            id="return_date"
                            v-model="search.return_date"
                        />
                    </div>
                    <div class="col-12">
                        <date-filter-component
                            title="Data do Pedido"
                            id="created_at"
                            v-model="search.created_at"
                        />
                    </div>
                </div>
            </template>
            <template v-slot:footer>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal" @click="searchTravelRequest()">Buscar</button>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fechar</button>
            </template>
        </modal-component>
        <!-- fim modal buscar -->

        <!-- início modal visualizar -->
        <modal-component id="modalViewTravelRequest" title="Detalhes do Pedido">
            <template v-slot:content>
                <div class="row">
                    <div class="col-4">
                        <input-container-component title="ID">
                            <input type="text" class="form-control" :value="$store.state.item.id" disabled>
                        </input-container-component>
                    </div>
                    <div class="col-8">
                        <input-container-component title="Data do pedido">
                            <input type="date" class="form-control" :value="formatToDateInput($store.state.item.created_at)" disabled>
                        </input-container-component>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <input-container-component title="Destino">
                            <input type="text" class="form-control" :value="$store.state.item.destination" disabled>
                        </input-container-component>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <input-container-component title="Data de ida">
                            <input type="date" class="form-control" :value="formatToDateInput($store.state.item.departure_date)" disabled>
                        </input-container-component>
                    </div>
                    <div class="col-6">
                        <input-container-component title="Data de retorno">
                            <input type="date" class="form-control" :value="formatToDateInput($store.state.item.return_date)" disabled>
                        </input-container-component>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <input-container-component title="Status">
                            <input type="text" class="form-control" :value="statuses[$store.state.item.status]" disabled>
                        </input-container-component>
                    </div>
                </div>
            </template>
            <template v-slot:footer>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fechar</button>
            </template>
        </modal-component>
        <!-- fim modal visualizar -->
    </div>
</template>

<script setup>
    import { formatToDateInput } from '@/utils/date';
</script>

<script>
    import axios from 'axios';

    export default {
        data() {
            return {
                urlBase: 'http://127.0.0.1:8000/api/v1/travel-requests',
                urlPagination: '',
                urlFilter: '',
                travelRequests: {data: []},
                showLoading: false,
                search: {
                    destination: '', 
                    departure_date: {
                        operator: '',
                        date: '',
                        date_start: '',
                        date_end: '',
                    },
                    return_date: {
                        operator: '',
                        date: '',
                        date_start: '',
                        date_end: '',
                    },
                    created_at: {
                        operator: '',
                        date: '',
                        date_start: '',
                        date_end: '',
                    },
                 },
                 statuses: {
                    S: 'Solicitado',
                    A: 'Aprovado',
                    C: 'Cancelado'
                },
            }
        },
        methods: {
            getTravelRequests(){
                this.showLoading = true;

                let url = this.urlBase + '?' + this.urlPagination + this.urlFilter;

                axios.get(url)
                .then(response => {
                    this.travelRequests = response.data;
                })
                .catch(errors => {console.log(errors)})
                .finally(() => {
                    this.showLoading = false;
                });
            },
            paginate(link){
                if(link.url){
                    this.urlPagination = link.url.split('?')[1];
                    this.getTravelRequests();
                }
            },
            searchTravelRequest(){
                let filter = '';

                for (let key in this.search) {
                    const value = this.search[key];

                    if (typeof value === 'object' && value !== null) {
                        const operator = value.operator;

                        if (!operator) continue;

                        if (operator === 'equal' && value.date) {
                            filter += `${key}=${value.date}&${key}_operator=equal&`;
                        }

                        if (operator === 'between' && value.date_start && value.date_end) {
                            filter += `${key}_start=${value.date_start}&${key}_end=${value.date_end}&${key}_operator=between&`;
                        }
                    } else if (value) {
                        filter += `${key}=${value}&`;
                    }
                }

                if (filter.endsWith('&')) {
                    filter = filter.slice(0, -1);
                }

                if(filter != ''){
                    this.urlPagination = 'page=1';
                    this.urlFilter = '&' + filter;
                } else {
                    this.urlFilter = '';
                }

                this.getTravelRequests();
            },
            clearFilter(){
                this.urlFilter = '';
                this.getTravelRequests();
            },
        },
        mounted(){
            this.getTravelRequests();
        }
    }
</script>
