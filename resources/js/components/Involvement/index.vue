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
              <label for="version" class="text-muted"><strong>Версия бюджета:</strong></label>
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
      </div>
    </div>
    <div class="card mt-3">
      <preloader v-if="isLoading"></preloader>
      <h4 class="card-header">Вовлечение</h4>
      <div class="card-body">
        <involvement-table></involvement-table>
      </div>
    </div>
  </main>
</template>

<script>
  import InvolvementTable from "./InvolvementTable";

  export default {
    components: {
      InvolvementTable
    },
    data() {
      return {
        data: {
          regions: [],
          periods: [ 3 ],
          version: 1,
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
        let { regions, periods, version } = this.data;
        this.$store.dispatch('involvement/updateInvolvement', { regions, periods, version }).then(res => {
          this.errors = res.errors;
          this.isLoading = false;
        });
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
