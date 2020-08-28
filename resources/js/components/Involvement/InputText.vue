<template>
  <input :value="value | roundHelper"
         :data-dkre="dkre"
         :data-article="article"
         :data-activity="activity"
         :disabled="disabled"
         v-prevent-number=""
         @change="$emit('change', value)">
</template>

<script>
  import InputText from './InputText';
  export default {
    data() {
      return {
        isLoading: false
      }
    },
    props: {
      value: {
        type: Number,
        required: true
      },
      dkre: {
        type: Number,
        required: true
      },
      article: {
        type: Number,
        required: true
      },
      activity: {
        type: Number,
        required: true
      },
      disabled: {
        type: Boolean,
        default: false
      }
    },

    mounted() {
    }
  }
</script>
