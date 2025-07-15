<template>
  <div class="col">
    <input-container-component
      :title="title"
      :id="idOperator"
      :id-help="idHelp"
      :text-help="textHelp"
    >
      <select
        class="form-control mb-2"
        :id="idOperator"
        :aria-describedby="idHelp"
        v-model="model.operator"
      >
        <option value="">Selecione o operador</option>
        <option value="equal">Igual</option>
        <option value="between">Entre</option>
      </select>

      <input
        v-if="model.operator === 'equal'"
        type="date"
        class="form-control"
        :id="idDate"
        v-model="model.date"
      />

      <div v-if="model.operator === 'between'" class="d-flex gap-2">
        <input
          type="date"
          class="form-control"
          :id="idDateStart"
          v-model="model.date_start"
        />
        <input
          type="date"
          class="form-control"
          :id="idDateEnd"
          v-model="model.date_end"
        />
      </div>
    </input-container-component>
  </div>
</template>

<script>
export default {
  props: {
    title: String,
    id: String,
    modelValue: Object,
    idHelp: {
      type: String,
      default: ''
    },
    textHelp: {
      type: String,
      default: ''
    }
  },
  computed: {
    model: {
      get() {
        return this.modelValue;
      },
      set(value) {
        this.$emit('update:modelValue', value);
      }
    },
    idOperator() {
      return `${this.id}_operator`;
    },
    idDate() {
      return `${this.id}`;
    },
    idDateStart() {
      return `${this.id}_start`;
    },
    idDateEnd() {
      return `${this.id}_end`;
    }
  }
}
</script>
