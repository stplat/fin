<template>
  <main>
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
          <div class="col-md-2">
            <div class="form-group">
              <label for="article" class="text-muted"><strong>Статья ПБ:</strong></label>
              <select class="form-control" id="article" v-model="data.article">
                <option disabled value>Выберите один из вариантов</option>
                <option :value="article.id" v-for="(article, key) in articles" :key="key">{{ article.name }}</option>
              </select>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label for="version" class="text-muted"><strong>Версия:</strong></label>
              <select class="form-control" id="version" v-model="data.version">
                <option disabled value>Выберите один из вариантов</option>
                <option :value="version.id" v-for="(version, key) in versions" :key="key">{{ version.name }}</option>
              </select>
            </div>
            <button class="btn btn-primary mr-3" @click="confirm">Применить</button>
            <button class="btn btn-secondary float-right">Загрузить</button>
            <button class="btn btn-secondary float-right mr-1">Выгрузить</button>
          </div>
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
      <h4 class="card-header">Бюджетные параметры</h4>
      <div class="card-body">
        <application-table></application-table>
      </div>
    </div>
  </main>
</template>
<script>
  import ApplicationTable from "./ApplicationTable";

  export default {
    components: {
      ApplicationTable
    },
    data() {
      return {
        data: {
          periods: [ 1 ],
          article: 1,
          version: 1,
          version_budget: 2,
          version_involvement: 2,
          version_f22: 2,
          version_shipment: 2,
        },
        isLoading: true,
        messages: [
          { 'login.required': 'Поле <strong>Логин</strong> обязательно для заполнения' },
          { 'password.required': 'Поле <strong>Пароль</strong> обязательно для заполнения' },
          { 'role.required': 'Поле <strong>Роль</strong> обязательно для заполнения' },
        ],
        errors: [],
      }
    },
    props: {
      initialData: {
        type: Object,
        required: true
      }
    },
    methods: {
      confirm() {
        this.isLoading = true;
        this.$store.dispatch('application/updateApplications', this.data).then(res => {
          this.errors = res.errors;
          this.isLoading = false;
        });
      }
    },
    computed: {
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
