<template>
  <transition name="popup" mode="in-out">
    <div class="popup-wrapper" @click="close" v-if="show"
         :style="{textAlign: image ? 'center' : 'left', zIndex: zIndex}">
      <div class="popup-container" :class="{ 'd-inline-block': image }" :style="{ width: width }">
        <preloader v-if="isLoading"></preloader>
        <button type="button" class="popup-close" @click="close" title="Закрыть">
          <unicon name="multiply" fill="royalblue"/>
        </button>
        <div class="popup-header" v-if="$slots.hasOwnProperty('header')">
          <h4 class="popup-title m-0">
            <slot name="header"></slot>
          </h4>
          <div class="popup-sub-title mt-2" v-if="$slots.hasOwnProperty('subheader')">
            <slot name="subheader"></slot>
          </div>
        </div>
        <div class="popup-body" v-if="$slots.hasOwnProperty('body')">
          <slot name="body"></slot>
        </div>
        <div class="popup-footer" v-if="$slots.hasOwnProperty('footer')">
          <slot name="footer"></slot>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
  export default {
    data() {
      return {
        show: false
      }
    },
    props: {
      isLoading: {
        type: Boolean,
        required: false,
        default: false
      },
      image: {
        type: Boolean,
        required: false
      },
      width: {
        type: [ String, Number ],
        required: false
      },
      zIndex: {
        type: Number,
        required: false,
        default: 100
      }
    },
    methods: {
      close(e) {
        if (!e.target.closest('.popup-container') || e.target.closest('.popup-close')) {
          document.body.classList.remove('is-popped');
          this.$emit('close');
        }
      }
    },
    created() {
      setTimeout(() => this.show = true, 100);
    },
    destroyed() {
      document.body.classList.remove('is-popped');
    },
    mounted() {
      document.body.classList.add('is-popped');
      document.addEventListener('keydown', e => {
        if (e.code === 'Escape') {
          document.body.classList.remove('is-popped');
          this.$emit('close');
        }
      });
    }
  }
</script>

<style lang="scss" scoped>
  .popup-enter {
    opacity: 0;
  }

  .popup-leave-active {
    opacity: 0;
  }

  .popup-enter .popup-container,
  .popup-leave-active .popup-container {
    top: -100px;
  }
</style>
