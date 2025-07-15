<template>
  <div
    class="toast-container position-fixed top-0 end-0 p-3"
    style="z-index: 1100"
  >
    <div
      class="toast show align-items-center text-white border-0"
      :class="`bg-${variant}`"
      role="alert"
      :aria-live="autohide ? 'polite' : 'assertive'"
      aria-atomic="true"
      ref="toastEl"
    >
      <div class="d-flex">
        <div class="toast-body">
          <strong class="me-2">{{ title }}</strong>
          <div>{{ message }}</div>
        </div>
        <button
          type="button"
          class="btn-close btn-close-white me-2 m-auto"
          data-bs-dismiss="toast"
          aria-label="Close"
          @click="hideToast"
        ></button>
      </div>
    </div>
  </div>
</template>

<script>
    export default {
        props: {
            title: {
            type: String,
            required: true
            },
            message: {
            type: String,
            required: true
            },
            variant: {
            type: String,
            default: 'primary'
            },
            autohide: {
            type: Boolean,
            default: true
            },
            delay: {
            type: Number,
            default: 5000
            }
        },
        data() {
            return {
            bsToast: null
            }
        },
        mounted() {
            import('bootstrap').then((bootstrap) => {
            this.bsToast = new bootstrap.Toast(this.$refs.toastEl, {
                autohide: this.autohide,
                delay: this.delay
            })
            this.bsToast.show()
            })
        },
        methods: {
            hideToast() {
            if (this.bsToast) {
                this.bsToast.hide()
            }
            }
        }
    }
</script>

<style scoped>
    .toast-container {
        pointer-events: none;
    }
    .toast {
        pointer-events: auto;
    }
</style>
