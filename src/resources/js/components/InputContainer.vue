<!-- InputContainerComponent.vue -->
<template>
  <div class="mb-3">
    <label :for="id" class="form-label">{{ title }}</label>
    <slot></slot>
    <div v-if="textHelp" :id="idHelp" class="form-text">{{ textHelp }}</div>
  </div>
</template>

<script>
export default {
    name: 'InputContainerComponent',
    props: {
        id: { type: String, required: true },
        title: { type: String, required: true },
        idHelp: { type: String, default: '' },
        textHelp: { type: String, default: '' }
    }
}
</script>
