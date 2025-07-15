<template>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th v-for="title, key in titles" :key="key" scope="col">{{ title.title }}</th>
                <th v-if="view.visible || update.visible || remove.visible"></th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(obj, index) in data" :key="index">
                <td v-for="(value, key) in titles" :key="value.title + '-' + key">
                    <span v-if="value.type == 'text'">
                        {{ value.cast ? value.cast[obj[key]] ?? obj[key] : obj[key] }}
                    </span>
                    <span v-if="value.type == 'date'">
                        {{ formatDateTime(obj[key]) }}
                    </span>
                </td>
                <td v-if="view.visible || update.visible || remove.visible || approve.visible || cancel.visible">
                    <div class="float-end">
                        <button v-if="view.visible" class="btn btn-outline-success btn-sm ms-2" :data-bs-toggle="view.dataToggle" :data-bs-target="view.dataTarget" @click="setStore(obj)" :disabled="isLoading">Ver</button>
                        <button v-if="!this.$store.state.user.admin && update.visible" class="btn btn-outline-primary btn-sm ms-2" :data-bs-toggle="update.dataToggle" :data-bs-target="update.dataTarget" @click="setStore(obj)" :disabled="isLoading">Editar</button>
                        <button v-if="!this.$store.state.user.admin && remove.visible" class="btn btn-outline-danger btn-sm ms-2" :data-bs-toggle="remove.dataToggle" :data-bs-target="remove.dataTarget" @click="setStore(obj)" :disabled="isLoading">Remover</button>
                        <button v-if="this.$store.state.user.admin && approve.visible" class="btn btn-outline-primary btn-sm ms-2" @click="handleAction('approve', obj)" :disabled="isLoading || obj.status != 'S'">
                            <span v-if="isApproving && $store.state.item.id == obj.id" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            <span v-if="isApproving && $store.state.item.id == obj.id"> Aprovando...</span>
                            <span v-else>Aprovar</span>
                        </button>
                        <button v-if="this.$store.state.user.admin && cancel.visible" class="btn btn-outline-danger btn-sm ms-2" @click="handleAction('cancel', obj)" :disabled="isLoading || obj.status != 'S'">
                            <span v-if="isCanceling && $store.state.item.id == obj.id" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            <span v-if="isCanceling && $store.state.item.id == obj.id"> Cancelando...</span>
                            <span v-else>Cancelar</span>
                        </button>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</template>

<script setup>
    import { formatDateTime } from '@/utils/date';
</script>

<script>
    export default {
        props: ['data', 'titles', 'view', 'update', 'remove', 'approve', 'cancel', 'onApprove', 'onCancel', 'isLoading', 'isApproving', 'isCanceling'],
        methods: {
            setStore(obj) {
                this.$store.state.transaction.status = '';
                this.$store.state.transaction.message = '';
                this.$store.state.transaction.data = '';
                this.$store.state.item = obj;
            },
            handleAction(action, obj) {
                this.setStore(obj)
                if (action === 'approve' && this.onApprove) {
                    this.onApprove()
                }
                if (action === 'cancel' && this.onCancel) {
                    this.onCancel()
                }
            }
        }
    }
</script>
