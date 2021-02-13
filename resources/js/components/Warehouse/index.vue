<template>
  <main>
    <alert className="success" v-if="result" v-html="result"></alert>
    <div class="card mt-3">
      <preloader v-if="isLoading"></preloader>
      <h4 class="card-header">Складские запасы (в том числе невостребованные)</h4>
      <div class="card-body">
        <v-client-table :data="table.data" :columns="table.columns" :options="table.options">
          <template v-slot:afterLimit>
            <button class="btn btn-success" @click="modal.upload.show = true">Показать невостребованные</button>
          </template>
          <template v-slot:actions="props">
            <button class="btn btn-danger" @click="data.id = props.row.id" v v-if="data.id !== props.row.id">Отдать</button>
            <div class="form-confirm" v-if="data.id === props.row.id">
              <div class="form-confirm__input">
                <input type="text" v-prevent-number="">
              </div>
              <div class="form-confirm__control">
                <a href="" class="form-confirm__link" @click.prevent="">
                  <unicon name="check" fill="green"/>
                </a>
                <a href="" class="form-confirm__link" @click.prevent="data.id = null">
                  <unicon name="times" fill="red"/>
                </a>
              </div>
            </div>
          </template>
        </v-client-table>
      </div>
    </div>
  </main>
</template>
<script>
  export default {
    components: {},
    data() {
      return {
        data: {
          id: null
        },
        isLoading: false,
        errors: [],
        result: ''
      }
    },
    props: {
      initialData: {
        type: Object,
        required: false
      }
    },
    computed: {
      /* Таблица событий */
      table() {
        const data = [
          { id: 1, code: 'aa' },
          { id: 2, code: 'ss' }
        ];

        const headings = {
          code: 'Код СКМТР',
          size: 'Размеры',
          gost: 'ГОСТ, ОСТ, ТУ',
          type: 'Тип',
          quantity: 'Количество',
          price: 'Цена, руб.',
          total: 'Сумма, руб.',
          desire: 'Невостребованные, кол-во',
          actions: ''
        };

        return { data, options: { headings, _data: data }, columns: Object.keys(headings) };
      }
    }
  }
</script>
<style lang="scss" scoped>
  table {
    th:last-child {
      width: 120px;
    }
  }
</style>
