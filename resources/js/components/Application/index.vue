<template>
  <main>
    <alert className="success" v-if="result" v-html="result"></alert>
    <div class="card mb-1">
      <h4 class="card-header">Выбор параметров</h4>
      <div class="card-body">
        <div class="row">
          <div class="col-md-12">
            <alert v-for="(error, i) in errors" :key="i" v-html="error"/>
          </div>
        </div>
        <div class="row">
          <div class="col-md-2">
            <div class="form-group">
              <label for="period" class="text-muted"><strong>Период:</strong></label>
              <select class="form-control" id="period" multiple v-model="data.periods">
                <option :value="period.id" v-for="(period, key) in periods" :key="key">{{ period.name }}</option>
              </select>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label for="article" class="text-muted"><strong>Статья ПБ:</strong></label>
              <select class="form-control" id="article" v-model="data.article">
                <option disabled value>Выберите один из вариантов</option>
                <option :value="article.id" v-for="(article, key) in articles" :key="key">{{ article.name }}</option>
              </select>
            </div>
            <button class="btn btn-primary mr-3" @click="confirm">Применить</button>
            <button class="btn btn-secondary float-right" @click="modals.upload = true">Импорт</button>
            <button class="btn btn-secondary float-right mr-1" @click="download">Экспорт</button>
          </div>
          <div class="col-md-3" v-if="consolidateToggle">
            <button class="btn btn-danger mt-4" @click="consolidate">Консолидировать квартал</button>
          </div>
          <!--          <div class="col-md-3">-->
          <!--            <div class="form-group">-->
          <!--              <label for="version" class="text-muted"><strong>Версия:</strong></label>-->
          <!--              <select class="form-control" id="version" v-model="data.version">-->
          <!--                <option disabled value>Выберите один из вариантов</option>-->
          <!--                <option :value="version.id" v-for="(version, key) in versions" :key="key">{{ version.name }}</option>-->
          <!--              </select>-->
          <!--            </div>-->
          <!--          </div>-->
        </div>
        <div class="row mt-4">
          <div class="col-md-2">
            <div class="form-group">
              <label for="version_22" class="text-muted"><strong>Версия ф.22:</strong></label>
              <select class="form-control" id="version_22" v-model="data.version_f22">
                <option disabled value>Выберите один из вариантов</option>
                <option :value="version.id" v-for="(version, key) in versions" :key="key">{{ version.name }}</option>
              </select>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label for="version_budget" class="text-muted"><strong>Версия бюджета:</strong></label>
              <select class="form-control" id="version_budget" v-model="data.version_budget">
                <option disabled value>Выберите один из вариантов</option>
                <option :value="version.id" v-for="(version, key) in versions" :key="key">{{ version.name }}</option>
              </select>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label for="version_involvement" class="text-muted"><strong>Версия вовлечения:</strong></label>
              <select class="form-control" id="version_involvement" v-model="data.version_involvement">
                <option disabled value>Выберите один из вариантов</option>
                <option :value="version.id" v-for="(version, key) in versions" :key="key">{{ version.name }}</option>
              </select>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label for="version_ship" class="text-muted"><strong>Версия плана поставки:</strong></label>
              <select class="form-control" id="version_ship" v-model="data.version_shipment">
                <option disabled value>Выберите один из вариантов</option>
                <option :value="version.id" v-for="(version, key) in versions" :key="key">{{ version.name }}</option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="card mt-3">
      <preloader v-if="isLoading"></preloader>
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Денежная заявка</h4>
        <template v-if="dataForProps.periods.length === 1">
          <button class="btn btn-secondary" v-if="!mode.edit" @click="mode.edit = true">редактировать</button>
          <button class="btn btn-danger" v-if="mode.edit" @click="mode.edit = false">отменить</button>
        </template>
      </div>
      <div class="card-body">
        <application-table :mode="mode" :data="dataForProps"></application-table>
      </div>
    </div>
    <application-upload @close="modals.upload = false"
                        v-if="modals.upload"
                        :initial-data="data"
                        :periods="periods"
                        @setResult="setResult"></application-upload>
  </main>
</template>
<script>
  import ApplicationTable from './ApplicationTable';
  import ApplicationUpload from './ApplicationUpload';

  export default {
    components: {
      ApplicationTable,
      ApplicationUpload
    },
    data() {
      return {
        data: {
          periods: [2],
          article: 1,
          version_budget: 2,
          version_involvement: 1,
          version_f22: 2,
          version_shipment: 2
        },
        modals: {
          upload: false
        },
        isLoading: true,
        messages: [
          { 'login.required': 'Поле <strong>Логин</strong> обязательно для заполнения' },
          { 'password.required': 'Поле <strong>Пароль</strong> обязательно для заполнения' },
          { 'role.required': 'Поле <strong>Роль</strong> обязательно для заполнения' }
        ],
        errors: [],
        result: '',
        mode: {
          edit: false
        },
        dataForProps: {
          periods: [1],
          article: 1,
          version_budget: 2,
          version_involvement: 2,
          version_f22: 2,
          version_shipment: 1
        }
      }
    },
    props: {
      initialData: {
        type: Object,
        required: true
      }
    },
    methods: {
      consolidate() {
        this.isLoading = true;
        this.mode.edit = false;
        this.dataForProps = _.clone(this.data);

        this.$store.dispatch('application/consolidateApplication', this.data).then(res => {
          this.errors = res.errors;
          if (!res.hasOwnProperty('errors')) {

            this.setResult('<strong>' + this.periods.filter(item => item.id === this.data.periods[0])[0].name + '</strong> успешно сконсолидирован');
          }
          this.isLoading = false;
        });
      },
      download() {
        this.isLoading = true;
        this.mode.edit = false;
        let { periods } = this.data;

        this.$store.dispatch('application/exportApplications', { period: periods[0] }).then(res => {
          this.errors = res.errors;

          if (!res.errors) {
            const a = document.createElement('a');
            a.href = res;
            a.click();
          }

          this.isLoading = false;
        });
      },
      confirm() {
        this.isLoading = true;
        this.mode.edit = false;
        this.dataForProps = _.clone(this.data);

        this.$store.dispatch('application/updateApplications', this.data).then(res => {
          this.errors = res.errors;
          this.isLoading = false;
        });
      },
      setResult(str) {
        this.result = str;
        setTimeout(() => this.result = '', 3000);
      }
    },
    computed: {
      consolidateToggle() {
        return this.dataForProps.periods.length === 1 &&
          ( this.dataForProps.periods[0] === 2 || this.dataForProps.periods[0] === 6 || this.dataForProps.periods[0] === 10 || this.dataForProps.periods[0] === 14 )
      },
      periods() {
        return this.initialData.periods;
      },
      versions() {
        return this.initialData.versions;
      },
      articles() {
        return this.initialData.articles;
      }
    },
    mounted() {
      this.confirm();
    }
  }
</script>
