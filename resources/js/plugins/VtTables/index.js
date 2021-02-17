import Vue from 'vue';
import { ServerTable, ClientTable, Event } from 'vue-tables-2';
import VtDataTable from './VtDataTable';
import VtSortControl from './VtSortControl';
import VtTableHeading from './VtTableHeading';


Vue.use(ClientTable,
  {
    sortByColumn: true,
    sortable: [ 'name', 'size', 'gost', 'quantity', 'price', 'unused', 'total' ],
    resizableColumns: false,
    texts: {
      count: 'Показано с {from} по {to} из {count} записей|Записей: {count}|Одна запись',
      first: 'Первый',
      last: 'Последний',
      filter: 'Поиск:',
      filterPlaceholder: '',
      limit: 'Записей на странице:',
      page: 'Страница:',
      noResults: 'Совпадений не найдено',
      filterBy: 'Поиск по {column}',
      loading: 'Загружаю...',
      defaultOption: 'Выбрано {column}',
      columns: 'Столбцы'
    },
    customSorting: {
      price: function (ascending) {
        return function (a, b) {
          if (ascending) {
            return Number(a.price) <= Number(b.price) ? 1 : -1;
          } else {
            return Number(a.price) > Number(b.price) ? 1 : -1;
          }
        }
      },
      total: function (ascending) {
        return function (a, b) {
          if (ascending) {
            return Number(a.total) <= Number(b.total) ? 1 : -1;
          } else {
            return Number(a.total) > Number(b.total) ? 1 : -1;
          }
        }
      }
    }
  }, false, 'bootstrap4', {
    dataTable: VtDataTable,
    sortControl: VtSortControl,
    tableHeading: VtTableHeading,
  });


export default ClientTable;
