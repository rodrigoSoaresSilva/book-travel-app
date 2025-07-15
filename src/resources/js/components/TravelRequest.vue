<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 mb-3">
                <card-component title="Buscar pedidos">
                    <template v-slot:content>
                        <div class="row g-3 mb-3">
                            <input-container-component
                            title="Destino"
                            id="inputDestino"
                            id-help="destinoHelp"
                            text-help="Destino da viagem"
                            >
                            <input
                                type="text"
                                class="form-control"
                                id="inputDestino"
                                aria-describedby="destinoHelp"
                                placeholder="Destino"
                                v-model="search.destination"
                            >
                            </input-container-component>
                        </div>
                        <div class="row g-3 mb-3">
                            <date-filter-component
                                title="Data de Ida"
                                id="departure_date"
                                v-model="search.departure_date"
                            />
                        </div>
                        <div class="row g-3 mb-3">
                            <date-filter-component
                                title="Data de Volta"
                                id="return_date"
                                v-model="search.return_date"
                            />
                        </div>
                        <div class="row g-3 mb-3">
                            <date-filter-component
                                title="Data do Pedido"
                                id="created_at"
                                v-model="search.created_at"
                            />
                        </div>
                    </template>
                    <template v-slot:footer>
                        <button type="submit" class="btn btn-primary btn-sm float-end" @click="searchTravelRequest()">Search</button>
                    </template>
                </card-component>

                <card-component title="Pedidos">
                    <template v-slot:content>
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
                                    cast: {
                                        S: 'Solicitado',
                                        A: 'Aprovado',
                                        C: 'Cancelado',
                                    }
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
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        data() {
            return {
                urlBase: 'http://127.0.0.1:8000/api/v1/travel-requests',
                urlPagination: '',
                urlFilter: '',
                travelRequests: {data: []},
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
            }
        },
        methods: {
            getTravelRequests(){
                let url = this.urlBase + '?' + this.urlPagination + this.urlFilter;

                axios.get(url)
                .then(response => {
                    this.travelRequests = response.data;
                    console.log(response.data);
                })
                .catch(errors => {console.log(errors)})
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
        },
        mounted(){
            this.getTravelRequests();
        }
    }
</script>
