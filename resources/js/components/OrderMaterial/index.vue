<template>
  <main>
    <alert className="success" v-if="result.html" v-html="result.html"></alert>
    <alert v-for="(error, key) in errors" :key="key" v-html="error"></alert>
    <div class="card mt-3">
      <preloader v-if="isLoading"></preloader>
      <h4 class="card-header">Заявка на получение материалов от других РДКРЭ</h4>
      <div class="card-body table-warehouse">
        <v-client-table :data="table.data" :columns="table.columns" :options="table.options">
          <template v-slot:afterLimit>
            <!--            <button class="btn btn-success" @click="modal.upload.show = true">Показать невостребованные</button>-->
          </template>
          <template v-slot:actions="props">
            <button class="btn btn-danger warehouse-document-click"
                    @click="open(props.row.id)"
                    v-if="data.id !== props.row.id && props.row.unused < props.row.quantity">Отдать
            </button>
            <div class="form-confirm warehouse-document-click" v-if="data.id === props.row.id">
              <div class="form-confirm__input">
                <input type="text" v-prevent-number="" v-model="data.value">
              </div>
              <div class="form-confirm__control">
                <a href="" class="form-confirm__link" @click.prevent="confirm(props.row.id)">
                  <unicon name="check" fill="green"/>
                </a>
                <a href="" class="form-confirm__link" @click.prevent="cancel(props.row.id)">
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
          id: null,
          value: null
        },
        isLoading: false,
        errors: [],
        result: {
          html: '',
          timer: null
        }
      }
    },
    props: {
      initialData: {
        type: Array,
        required: false
      }
    },
    methods: {
      validate() {
        if (!this.data.value) {
          this.errors = [ 'Введите необходимое количество для передачи' ];
          return false;
        }

        if (this.data.value < 0) {
          this.errors = [ 'Количество невостребованных материалов не может быть отрицательным числом' ];
          return false;
        }

        const quantity = this.initialData.filter(item => item.id === this.data.id)[0].quantity;

        if (quantity < this.data.value) {
          this.errors = [ 'Количество для передачи не может превышать количество на складе' ];
          return false;
        }

        return true;
      },
      /* Открыть окно по вводу информации */
      open(id) {
        this.data = {
          id,
          value: null
        };

        this.errors = [];
      },
      /* Подтвердить передачу материала */
      confirm(id) {
        if (this.validate()) {
          this.isLoading = true;
          this.$store.dispatch('material/push', {
            id: this.data.id,
            value: this.data.value.replace(',', '.')
          }).then(res => {
            this.errors = res.errors;
            this.isLoading = false;

            if (!res.errors) {
              this.setResult('<strong>Успешно изменено!</strong>');
              this.cancel();
            }
          });
        }
      },
      /**/
      cancel(id) {
        this.data = {
          id: null,
          value: null
        };
        this.errors = [];
      },
      /* Выводим результат */
      setResult(html) {
        clearTimeout(this.result.timer);
        this.result.html = html;
        this.result.timer = setTimeout(() => {
          this.result.html = '';
        }, 3000);
      }
    },
    computed: {
      /* Таблица событий */
      table() {
        const data = this.$store.getters['material/getMaterials'].map(item => {
          return {
            id: item.id,
            executor: item.material.dkre.name,
            customer: item.dkre.name,
            code: item.material.code,
            name: item.material.name,
            size: item.material.size,
            gost: item.material.gost,
            type: item.material.type,
            unit: item.material.unit,
            quantity: Number(item.quantity).toFixed(3),
            price: Number(item.material.price).toFixed(3),
            total: Number(item.quantity * item.material.price).toFixed(3),
            date: this.formatDateHelper(item.updated_at),
          }
        });

        const headings = {
          executor: 'Передающий',
          customer: 'Получатель',
          code: 'Код СКМТР',
          name: 'Наименование',
          size: 'Размеры',
          gost: 'ГОСТ, ОСТ, ТУ',
          type: 'Тип',
          unit: 'ЕИ',
          quantity: 'Кол-во',
          price: 'Цена, руб.',
          total: 'Сумма, руб.',
          date: 'Дата заявка',
        };

        return { data, options: { headings, _data: data }, columns: Object.keys(headings) };
      }
    },
    mounted() {
      document.addEventListener('click', (e) => {
        if (!e.target.closest('.warehouse-document-click')) {
          this.data = {
            id: null,
            value: null
          };
          this.errors = [];
        }
      });

      this.$store.commit('material/setMaterials', this.initialData);
    }
  }
</script>
<style lang="scss">
  .table-warehouse table tr {

    th:first-child {
      width: 10px;
    }

    th:last-child {
      min-width: 130px;
    }
  }

</style>
