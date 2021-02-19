<template>
  <main>
    <alert className="success" v-if="result.html" v-html="result.html"></alert>
    <alert v-for="(error, key) in errors" :key="key" v-html="error"></alert>
    <div class="card mt-3">
      <preloader v-if="isLoading"></preloader>
      <h4 class="card-header">Складские запасы (в том числе невостребованные)</h4>
      <div class="card-body table-warehouse">
        <v-client-table :data="table.data" :columns="table.columns" :options="table.options">
          <template v-slot:afterLimit>
            <div class="form-group mb-0">
              <select class="form-control" id="article" v-model="article" @change="changeArticle">
                <option value="null" disabled>Выберите статью</option>
                <option :value="article.id" v-for="(article, key) in computedArticles" :key="key">{{ article.name }}
                </option>
              </select>
            </div>
          </template>
          <template v-slot:actions="props">
            <button class="btn btn-danger warehouse-document-click"
                    @click="open(props.row.id)"
                    v-if="data.id !== props.row.id && Number(props.row.unused) < Number(props.row.quantity)">Отдать
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
          value: null,
        },
        article: null,
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
        type: Object,
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

        const quantity = this.initialData.materials.filter(item => item.id === this.data.id)[0].quantity;

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
      /* Меняем статью платежника */
      changeArticle() {
        this.isLoading = true;
        this.$store.dispatch('material/updateMaterials', { article_id: this.article }).then(res => {
          this.errors = res.errors;
          this.isLoading = false;
        });
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
            dkre: item.dkre.name,
            code: item.code,
            name: item.name,
            size: item.size,
            gost: item.gost,
            type: item.type,
            unit: item.unit,
            quantity: Number(item.quantity).toFixed(3),
            price: Number(item.price).toFixed(3),
            total: Number(item.total).toFixed(3),
            unused: Number(item.unused).toFixed(3),
            actions: ''
          }
        });

        const headings = {
          dkre: 'РДКРЭ',
          code: 'Код СКМТР',
          name: 'Наименование',
          size: 'Размеры',
          gost: 'ГОСТ, ОСТ, ТУ',
          type: 'Тип',
          unit: 'ЕИ',
          quantity: 'Кол-во',
          price: 'Цена, руб.',
          total: 'Сумма, руб.',
          unused: 'Неликвид, кол-во',
          actions: 'Действия'
        };

        return { data, options: { headings, _data: data }, columns: Object.keys(headings) };
      },
      /* Перчень статей */
      computedArticles() {
        console.log(this.initialData.articles)
        return this.initialData.articles;
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

      this.$store.commit('material/setMaterials', this.initialData.materials);
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
