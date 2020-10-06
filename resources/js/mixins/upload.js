export default {
  data() {
    return {
      modals: {
        upload: false
      },
      result: '',
    }
  },
  methods: {
    setResult(str) {
      this.result = str;
      setTimeout(() => this.result = '', 3000);
    }
  }

}
