<style lang="scss">
  .table-responsive {
    overflow-x: visible !important;
  }

  .VueTables__table {
    overflow: auto !important;
  }

  .VueTables__row td {
    max-width: 300px;
    vertical-align: middle !important;

    .vuetable__icons {
      display: flex;
      margin: 0 -2px;

      a {
        padding: 3px;
        margin: 0 2px;
      }
    }
  }

  .VueTables__search,
  .VueTables__limit {
    display: inline-flex;
  }

  .VueTables__search {
    margin-left: auto;
  }

  .VueTables__search-field,
  .VueTables__limit-field {
    display: flex;
    white-space: nowrap;
    align-items: center;

    & > label {
      margin: 0 10px 0 0 !important;
    }
  }
</style>

<template>

  <div :class="`VueTables VueTables--${props.source}`" slot-scope="props">

    <div :class="props.theme.row">
      <div :class="props.theme.column">
        <div class="d-flex">
          <div :class="`${props.theme.field} VueTables__limit`">
            <template v-if="props.opts._data.length >= 1">
              <vnodes :vnodes="props.slots.beforeLimit"/>
              <vt-per-page-selector class="mr-3"/>
              <button class="btn btn-primary mr-3" @click="download" v-if="props.opts._data && props.opts.isNeedDownloadButton === undefined">Экспорт</button>
            </template>
            <vnodes :vnodes="props.slots.afterLimit"/>
          </div>

          <div v-if="!props.opts.filterByColumn && props.opts.filterable"
               :class="`${props.theme.field} VueTables__search`">
            <vnodes :vnodes="props.slots.beforeFilter"/>
            <vt-generic-filter/>
            <vnodes :vnodes="props.slots.afterFilter"/>
          </div>
          <vnodes :vnodes="props.slots.afterFilterWrapper"/>

          <div class="VueTables__pagination-wrapper" v-if="props.opts.pagination.dropdown && props.totalPages > 1">
            <div :class="`${props.theme.field} ${props.theme.inline} ${props.theme.right} VueTables__dropdown-pagination`">
              <vt-dropdown-pagination/>
            </div>
          </div>

          <div v-if="props.opts.columnsDropdown"
               :class="`VueTables__columns-dropdown-wrapper ${props.theme.right} ${props.theme.dropdown.container}`">
            <vt-columns-dropdown/>
          </div>
        </div>
      </div>
    </div>

    <vnodes :vnodes="props.slots.beforeTable"/>
    <div class="table-responsive">
      <vt-table ref="vt_table"/>
    </div>
    <vnodes :vnodes="props.slots.afterTable"/>

    <vt-pagination/>
  </div>
</template>

<script>
  import VtColumnsDropdown from 'vue-tables-2/compiled/components/VtColumnsDropdown'
  import VtDropdownPagination from 'vue-tables-2/compiled/components/VtDropdownPagination'
  import VtGenericFilter from 'vue-tables-2/compiled/components/VtGenericFilter'
  import VtPerPageSelector from 'vue-tables-2/compiled/components/VtPerPageSelector';
  import VtPagination from 'vue-tables-2/compiled/components/VtPagination'
  import VtTable from 'vue-tables-2/compiled/components/VtTable';

  export default {
    name: 'MyDataTable',
    props: {
      props: Object
    },
    methods: {
      /*
      * Выгрузка таблицы в xlsx
      *
      * */
      async download() {
        const data = { data: this.props.opts._data, headings: this.props.opts.headings };
        const res = await axios.post('/table/export', data).catch(error => console.log(error));

        const a = document.createElement('a');
        a.href = await res.data;
        a.click();
      }
    },
    components: {
      VtGenericFilter,
      VtPerPageSelector,
      VtColumnsDropdown,
      VtDropdownPagination,
      VtTable,
      VtPagination,
      vnodes: {
        functional: true,
        render: (h, ctx) => ctx.props.vnodes
      }
    }
  }
</script>
