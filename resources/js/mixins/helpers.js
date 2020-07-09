export default {
  methods: {
    addWithEmptyHelper(array) {
      return array.filter(Boolean)
        .reduce((carry, item) => carry + item, 0).toFixed(3);
    }
  },
  filters: {
    converVdHelper(value) {
      let string;
      switch (value) {
        case '1':
          string = 'ПЕР';
          break;
        case '2':
          string = 'ПВД';
          break;
        case '3':
          string = 'КВ';
          break;
        case '4':
          string = 'ПРО';
          break;
      }
      return string;
    }
  },
}
