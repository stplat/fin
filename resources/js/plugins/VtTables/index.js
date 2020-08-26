import Vue from 'vue';
import { ServerTable, ClientTable, Event } from 'vue-tables-2';
import VtDataTable from './VtDataTable';
import VtSortControl from './VtSortControl';

Vue.use(ClientTable,
  {
    sortByColumn: true, texts: {
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
    }
  }, false, 'bootstrap4', {
    dataTable: VtDataTable,
    sortControl: VtSortControl
  });

export default ClientTable;
