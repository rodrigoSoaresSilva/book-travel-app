<template>
    <div class="container">
        <loading-overlay-component v-if="showLoading"></loading-overlay-component>
        <toast-container-component ref="toastContainer"></toast-container-component>

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
                            :approve="{visible: true}"
                            :cancel="{visible: true}"
                            :onApprove="handleApprove"
                            :onCancel="handleCancel"
                            :isLoading="showLoading"
                            :isApproving="isApproving"
                            :isCanceling="isCanceling"
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
                            :disabled="showLoading"
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
                                :disabled="showLoading"
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
                            :isLoading="showLoading"
                        />
                    </div>
                    <div class="col-12">
                        <date-filter-component
                            title="Data de Volta"
                            id="return_date"
                            v-model="search.return_date"
                            :isLoading="showLoading"
                        />
                    </div>
                    <div class="col-12">
                        <date-filter-component
                            title="Data do Pedido"
                            id="created_at"
                            v-model="search.created_at"
                            :disabled="showLoading"
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

        <!-- início modal criação -->
        <modal-component id="modalCreateTravelRequest" title="Solicitar nava viagem">
            <template v-slot:alerts>
                <alert-component type="success" title="Operação realizada com sucesso!" :details="travelRequest.statusTransaction" v-if="travelRequest.statusTransaction.status == 'success'"></alert-component>
                <alert-component type="danger" title="Falha ao executar nova solicitação!" :details="travelRequest.statusTransaction" v-if="travelRequest.statusTransaction.status == 'error'"></alert-component>
            </template>

            <template v-slot:content>
                <div class="form-group"></div>
                <div class="row">
                    <div class="col">
                        <input-container-component title="Destino">
                            <input type="text" class="form-control" v-model="travelRequest.destination" :disabled="showLoading">
                        </input-container-component>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <input-container-component title="Data de ida">
                            <input type="date" class="form-control" v-model="travelRequest.departure_date" :disabled="showLoading">
                        </input-container-component>
                    </div>
                    <div class="col-6">
                        <input-container-component title="Data de retorno">
                            <input type="date" class="form-control" v-model="travelRequest.return_date" :disabled="showLoading">
                        </input-container-component>
                    </div>
                </div>
            </template>
            <template v-slot:footer>
                <button class="btn btn-success" @click="createTravelRequest" :disabled="showLoading">
                    <span v-if="showLoading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <span v-if="!showLoading">Salvar</span>
                    <span v-else> Salvando...</span>
                </button>

                <button class="btn btn-danger" data-bs-dismiss="modal" :disabled="showLoading">Fechar</button>
            </template>
        </modal-component>
        <!-- fim modal criação -->
        
        <!-- início modal editar -->
        <modal-component id="modalUpdateTravelRequest" title="Editar Pedido">
            <template v-slot:alerts>
                <alert-component type="success" title="Operação realizada com sucesso!" :details="$store.state.transaction" v-if="$store.state.transaction.status == 'success'"></alert-component>
                <alert-component type="danger" title="Falha ao executar atualização!" :details="$store.state.transaction" v-if="$store.state.transaction.status == 'error'"></alert-component>
            </template>

            <template v-slot:content>
                <div class="form-group"></div>
                <div class="row">
                    <div class="col">
                        <input-container-component title="Destino">
                            <input type="text" class="form-control" v-model="$store.state.item.destination" :disabled="showLoading">
                        </input-container-component>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <input-container-component title="Data de ida">
                            <input type="date" class="form-control" v-model="departureDateFormatted" :disabled="showLoading">
                        </input-container-component>
                    </div>
                    <div class="col-6">
                        <input-container-component title="Data de retorno">
                            <input type="date" class="form-control" v-model="returnDateFormatted" :disabled="showLoading">
                        </input-container-component>
                    </div>
                </div>
            </template>
            <template v-slot:footer>
                <button class="btn btn-success" @click="updateTravelRequest" :disabled="showLoading">
                    <span v-if="showLoading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <span v-if="!showLoading">Salvar</span>
                    <span v-else> Salvando...</span>
                </button>

                <button class="btn btn-danger" data-bs-dismiss="modal" :disabled="showLoading">Fechar</button>
            </template>
        </modal-component>
        <!-- fim modal editar -->

        <!-- início modal remover -->
        <modal-component id="modalRemoveTravelRequest" title="Remover Pedido">
            <template v-slot:alerts>
                <alert-component type="success" title="Operação realizada com sucesso!" :details="$store.state.transaction" v-if="$store.state.transaction.status == 'success'"></alert-component>
                <alert-component type="danger" title="Falha ao executar exclusão!" :details="$store.state.transaction" v-if="$store.state.transaction.status == 'error'"></alert-component>
            </template>
            <template v-slot:content v-if="$store.state.transaction.status != 'success'">
                <div class="row">
                    <div class="col-3">
                        <input-container-component title="ID">
                            <input type="text" class="form-control" :value="$store.state.item.id" disabled>
                        </input-container-component>
                    </div>
                    <div class="col-9">
                        <input-container-component title="Status">
                            <input type="text" class="form-control" :value="statuses[$store.state.item.status]" disabled>
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
            </template>
            <template v-slot:footer>
                <button class="btn btn-primary" @click="remove" :disabled="showLoading" v-if="$store.state.transaction.status != 'success'">
                    <span v-if="showLoading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <span v-if="!showLoading">Remover</span>
                    <span v-else> Removendo...</span>
                </button>

                <button class="btn btn-danger" data-bs-dismiss="modal" :disabled="showLoading">Fechar</button>
            </template>
        </modal-component>
        <!-- fim modal remover -->
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
                isApproving: false,
                isCanceling: false,
                travelRequest: {
                    destination: '', 
                    departure_date: '',
                    return_date: '',
                    statusTransaction: {},
                },
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
        computed: {
            departureDateFormatted: {
                get() {
                    return formatToDateInput(this.$store.state.item.departure_date);
                },
                set(value) {
                    this.$store.state.item.departure_date = value;
                }
            },
            returnDateFormatted: {
                get() {
                    return formatToDateInput(this.$store.state.item.return_date);
                },
                set(value) {
                    this.$store.state.item.return_date = value;
                }
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
            clearTransaction(){
                this.$store.state.transaction.status = '';                
                this.$store.state.transaction.message = '';
                this.$store.state.transaction.data = [];
            },
            resetModalCreateTravelRequest() {
                this.travelRequest.destination = '';
                this.travelRequest.departure_date = '';
                this.travelRequest.return_date = '';
                this.travelRequest.statusTransaction = {};
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
            createTravelRequest(){
                this.showLoading = true;

                let formData = new FormData();
                formData.append('destination', this.travelRequest.destination);
                formData.append('departure_date', this.travelRequest.departure_date);
                formData.append('return_date', this.travelRequest.return_date);

                this.travelRequest.statusTransaction.data = [];

                axios.post(this.urlBase, formData)
                    .then(response => {
                        this.travelRequest.statusTransaction.status = 'success';
                        this.travelRequest.statusTransaction.message = 'A sua solicitação de viagem foi criada!';
                        
                        this.getTravelRequests();
                    })
                    .catch(errors => {
                        console.log('Error:', errors);
                        this.travelRequest.statusTransaction = {
                            status: 'error',
                            data: errors.response.data.errors,
                            message: errors.response.data.message
                        }
                    })
                    .finally(() => {
                        this.showLoading = false;
                    });
            },
            updateTravelRequest(){
                this.showLoading = true;
                this.clearTransaction();

                let url = this.urlBase + '/' + this.$store.state.item.id;

                let formData = new FormData();
                formData.append('_method', 'patch');
                formData.append('destination', this.$store.state.item.destination);
                formData.append('departure_date', this.$store.state.item.departure_date);
                formData.append('return_date', this.$store.state.item.return_date);

                axios.post(url, formData)
                    .then(response => {
                        this.$store.state.transaction.status = 'success';
                        this.$store.state.transaction.message = 'A sua solicitação de viagem foi atualizada!';

                        const index = this.travelRequests.data.findIndex(i => i.id === response.data.result.id);
                        if (index !== -1) {
                            this.travelRequests.data[index] = response.data.result;
                        }
                    })
                    .catch(errors => {
                        console.log('Error:', errors);
                        this.$store.state.transaction = {
                            status: 'error',
                            data: errors.response.data.errors,
                            message: errors.response.data.message
                        }
                    })
                    .finally(() => {
                        this.showLoading = false;
                    });
            },
            remove(){
                let confirmation = confirm('Deseja mesmo apagar esse registro?');
                
                if(!confirmation) return false;

                this.showLoading = true;

                let url = this.urlBase + '/' + this.$store.state.item.id;

                let formData = new FormData();
                formData.append('_method', 'delete');

                axios.post(url, formData)
                    .then(response => {
                        this.$store.state.transaction.status = 'success';
                        this.$store.state.transaction.message = 'Solicitação de viagem removida com sucesso!';

                        this.getTravelRequests();
                    })
                    .catch(errors => {
                        console.log('Error:', errors);
                        this.$store.state.transaction = {
                            status: 'error',
                            data: errors.response.data.errors,
                            message: errors.response.data.message
                        }
                    })
                    .finally(() => {
                        this.showLoading = false;
                    });
            },
            handleApprove(){
                let confirmation = confirm('Deseja mesmo aprovar esse pedido?');
                
                if(!confirmation) return false;

                this.showLoading = true;
                this.isApproving = true;

                let url = this.urlBase + '/' + this.$store.state.item.id + '/approve';

                axios.post(url)
                    .then(response => {
                        this.showToast('success', 'Solicitação de viagem aprovada com sucesso!', 'Aprovação');
                    })
                    .catch(errors => {
                        console.log('Error:', errors);
                        this.showToast('danger', errors.response.data.message, 'Erro na requisição');
                    })
                    .finally(() => {
                        this.showLoading = false;
                        this.isApproving = false;
                    });
            },
            handleCancel(){
                let confirmation = confirm('Deseja mesmo cancelar esse pedido?');
                if(!confirmation) return false;

                this.showLoading = true;
                this.isCanceling = true;

                let url = this.urlBase + '/' + this.$store.state.item.id + '/approve';

                axios.post(url)
                    .then(response => {
                        this.showToast('success', 'Solicitação de viagem cancelada com sucesso!', 'Cancelamento');
                    })
                    .catch(errors => {
                        console.log('Error:', errors);
                        this.showToast('danger', errors.response.data.message, 'Erro na requisição');
                    })
                    .finally(() => {
                        this.showLoading = false;
                        this.isCanceling = false;
                    });
            },
            clearFilter(){
                this.urlFilter = '';
                this.getTravelRequests();
            },
            showToast(type = 'success', message = '', title = 'Notificação') {
                this.$refs.toastContainer.addToast({
                    title,
                    message,
                    variant: type
                });
            }
        },
        mounted(){
            this.getTravelRequests();

            const modal = document.getElementById('modalCreateTravelRequest');
            if (modal) {
                modal.addEventListener('hidden.bs.modal', this.resetModalCreateTravelRequest);
            }
        }
    }
</script>
