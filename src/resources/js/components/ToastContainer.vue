<template>
  <div
    class="toast-container position-fixed top-0 end-0 p-3"
    style="z-index: 1100"
  >
    <div
      v-for="toast in toasts"
      :key="toast.id"
      class="toast show text-white border-0 mb-2"
      :class="`bg-${toast.variant}`"
      role="alert"
      aria-live="polite"
      aria-atomic="true"
    >
      <div class="d-flex">
        <div class="toast-body">
          <strong>{{ toast.title }}</strong>
          <div>{{ toast.message }}</div>
        </div>
        <button
          type="button"
          class="btn-close btn-close-white me-2 m-auto"
          @click="removeToast(toast.id)"
        ></button>
      </div>
    </div>
  </div>
</template>

<script>
  let toastId = 0

  export default {
    data() {
      return {
        toasts: []
      }
    },
    methods: {
      addToast({ title, message, variant = 'primary', duration = 5000 }) {
        const id = ++toastId
        this.toasts.push({ id, title, message, variant })

        if (duration > 0) {
          setTimeout(() => this.removeToast(id), duration)
        }
      },
      removeToast(id) {
        this.toasts = this.toasts.filter(t => t.id !== id)
      }
    }
  }
</script>
