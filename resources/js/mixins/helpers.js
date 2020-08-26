export default {
  methods: {
    addWithEmptyHelper(array) {
      return array.filter(Boolean)
        .reduce((carry, item) => carry + item, 0).toFixed(3);
    },
    /* Формат дня недели */
    formatDateHelper(string) {
      const date = new Date(string);
      const year = date.getFullYear();
      const month = date.getMonth() < 9 ? '0' + Number(date.getMonth() + 1) : Number(date.getDate() + 1);
      const day = date.getDate() < 10 ? '0' + date.getDate() : date.getDate();

      return `${ year }-${ month }-${ day }`;
    },

    /* Формат времени */
    formatTimeHelper(date) {
      const hours = date.getHours() < 10 ? '0' + date.getHours() : date.getHours();
      const minutes = date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes();
      const seconds = date.getSeconds() < 10 ? '0' + date.getSeconds() : date.getSeconds();

      return `${ hours }:${ minutes }`;
    }
  },
  filters: {
    convertVdHelper(value) {
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
