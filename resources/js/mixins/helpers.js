export default {
  methods: {
    signClassNameHelper(type) {
      switch (type) {
        case 'АВ':
          return 'lk-sign';
        case 'Т':
          return 'lk-sign lk-sign--yellow';
        case 'ТБ':
          return 'lk-sign lk-sign--blue';
        case 'М':
          return 'lk-sign lk-sign--purple';
      }
    }
  },
}
