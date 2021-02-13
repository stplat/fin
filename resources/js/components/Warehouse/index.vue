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
            <button class="btn btn-danger" @click="modal.upload.show = true">Отдать</button>
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
          regions: [],
          periods: [ 2 ],
          version: 2,
          version_involvement: 1
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
    },
  }
</script>
