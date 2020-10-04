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
              <label for="month" class="text-muted"><strong>Период:</strong></label>
              <select class="form-control" id="month" multiple v-model="data.periods">
                <option :value="month.id" v-for="(month, key) in months" :key="key">{{ month.name }}</option>
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
      </div>
    </div>
    <div class="card mt-3">
      <preloader v-if="isLoading"></preloader>
      <div class="card-header">
        <h4 class="mb-0">Вовлечение</h4>
      </div>
      <div class="card-body">
        <shipment-table :data="dataForProps"></shipment-table>
      </div>
    </div>
    <shipment-upload @close="modals.upload = false"
                   v-if="modals.upload"
                   :initial-data="data"
                   :versions="versions"
                   @setResult="setResult"></shipment-upload>
  </main>
</template>

<script>
  import ShipmentTable from "./ShipmentTable";
  import ShipmentUpload from "./ShipmentUpload";

  export default {
    components: {
      ShipmentTable,
      ShipmentUpload
    },
    data() {
      return {
        data: {
          regions: [],
          periods: [ 3 ],
          version: 11,
        },
        modals: {
          upload: false
        },
        isLoading: true,
        messages: [
          { 'login.required': 'Поле <strong>Логин</strong> обязательно для заполнения' },
          { 'password.required': 'Поле <strong>Пароль</strong> обязательно для заполнения' },
          { 'role.required': 'Поле <strong>Роль</strong> обязательно для заполнения' },
        ],
        errors: [],
        dataForProps: {
          periods: [],
          regions: [],
          version: null,
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
      changeData() {

      },
      confirm() {
        this.isLoading = true;
        this.dataForProps = _.clone(this.data);

        this.$store.dispatch('shipment/updateShipments', this.data).then(res => {
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
      months() {
        return this.initialData.months;
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

<style lang="scss" scoped>
  .card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
</style>
