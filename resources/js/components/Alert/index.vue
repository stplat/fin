<template>
  <transition name="alert-list" mode="out-in">
    <div class="alert" v-if="show" :class="'alert-' + className" role="alert">
      <slot></slot>
    </div>
  </transition>
</template>

<script>
  export default {
    data() {
      return {
        show: false,
        timer: 0
      }
    },
    props: {
      className: {
        type: String,
        required: false,
        default: 'danger'
      },
    },
    created() {
      setTimeout(() => this.show = true, 100);
    },
  }
</script>

<style lang="scss" scoped>
  .list-enter-active, .list-leave-active,
  .alert-list-enter-active, .alert-list-leave-active {
    transition: all 0.1s;
  }

  .list-enter, .list-leave-to {
    opacity: 0;
    transform: translateY(30px);
  }

  .alert-list-enter, .alert-list-leave-to {
    opacity: 0;
    transform: translateY(-30px);
  }

  .alert {
    line-height: 1.3rem !important;
  }
</style>
