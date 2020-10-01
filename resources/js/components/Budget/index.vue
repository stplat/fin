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
              <label for="region" class="text-muted"><strong>Участок:</strong></label>
              <select class="form-control" id="region" multiple v-model="data.regions">
                <option :value="region.id" v-for="(region, key) in regions" :key="key">{{ region.region }}</option>
              </select>
            </div>
          </div>
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
              <label for="version" class="text-muted"><strong>Версия:</strong></label>
              <select class="form-control" id="version" v-model="data.version">
                <option disabled value>Выберите один из вариантов</option>
                <option :value="version.id" v-for="(version, key) in versions" :key="key">{{ version.name }}</option>
              </select>
            </div>
            <button class="btn btn-primary mr-3" @click="confirm">Применить</button>
            <button class="btn btn-secondary float-right" @click="modals.upload = true">Импорт</button>
            <button class="btn btn-secondary float-right mr-1">Экспорт</button>
          </div>
        </div>
        <div class="row mt-4">
          <div class="col-md-2">
            <div class="form-group">
              <label for="version_22" class="text-muted"><strong>Версия вовлечения:</strong></label>
              <select class="form-control" id="version_22" v-model="data.version_involvement">
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
        <h4 class="mb-0">Бюджетные параметры</h4>
        <template v-if="dataForProps.periods.length === 1">
          <button class="btn btn-secondary" v-if="!mode.edit" @click="mode.edit = true">редактировать</button>
          <button class="btn btn-danger" v-if="mode.edit" @click="mode.edit = false">отменить</button>
        </template>
      </div>
      <div class="card-body">
        <budget-table :mode="mode" :data="dataForProps"></budget-table>
      </div>
    </div>
    <budget-upload @close="modals.upload = false"
                   v-if="modals.upload"
                   :initial-data="data"
                   :versions="versions"
                   @setResult="setResult"></budget-upload>
  </main>
</template>
<script>
  import BudgetTable from "./BudgetTable";
  import BudgetUpload from "./BudgetUpload";

  export default {
    components: {
      BudgetTable,
      BudgetUpload
    },
    data() {
      return {
        data: {
          regions: [],
          periods: [ 14 ],
          version: 11,
          version_involvement: 1
        },
        modals: {
          upload: false
        },
        isLoading: true,
        errors: [],
        mode: {
          edit: false
        },
        dataForProps: {
          periods: [],
          regions: null,
          version: null,
          version_involvement: null
        },
        result: ''
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
        this.mode.edit = false;
        this.dataForProps = _.clone(this.data);

        this.$store.dispatch('budget/updateBudget', this.data).then(res => {
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
      dkres() {
        return this.initialData.dkres;
      },
      regions() {
        this.data.regions = this.initialData.regions.map(item => item.id);
        return this.initialData.regions;
      },
      periods() {
        return this.initialData.periods;
      },
      versions() {
        return this.initialData.versions;
      }
    },
    mounted() {
      this.confirm();
    }
  }
</script>
