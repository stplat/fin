export default {
  methods: {
    /* Суммируем значения с учетом пустых */
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
    roundHelper(value) {
      return value && Number(value) !== 0 ? Number(value).toFixed(3) : '';
    }
  },
  directives: {
    'prevent-number': {
      bind(el, binding) {
        const inputKeyPressHandler = function(e) {
          const ch = String.fromCharCode(e.which);
          const regexp = new RegExp(/[A-Za-zА-Яа-яЁё]/);
          if (ch.match(regexp)) {
            e.preventDefault();
          }
        };

        el.addEventListener('keypress', inputKeyPressHandler);
      }
    }
  }
};

export function serialize(array, name) {
  return `${ name }[]=${ array.join(',') }`;
}

