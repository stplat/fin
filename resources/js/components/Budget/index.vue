<template>
  <main>
    <div class="card mb-1">
      <h4 class="card-header">Выбор бюджетных параметров</h4>
      <div class="card-body">
        <div class="row">
<!--          <div class="col-md-1">-->
<!--            <div class="card-panel__item">-->
<!--              <label for="region_or_dkre" class="label mb-2">По участкам:</label>-->
<!--              <div>-->
<!--                <label title="Участки/ДКРЭ" class="toggle">-->
<!--                  <input type="checkbox" id="region_or_dkre" hidden="hidden" v-model="region">-->
<!--                  <span class="toggle__box"></span>-->
<!--                </label>-->
<!--              </div>-->
<!--            </div>-->
<!--          </div>-->
<!--          <div class="col-md-3" v-if="!region">-->
<!--            <div class="form-group">-->
<!--              <label for="dkre" class="text-muted"><strong>ДКРЭ:</strong></label>-->
<!--              <select class="form-control" id="dkre" multiple v-model="data.dkres">-->
<!--                <option :value="dkre.id" v-for="(dkre, key) in dkres" :key="key">{{ dkre.name }}</option>-->
<!--              </select>-->
<!--            </div>-->
<!--          </div>-->
          <div class="col-md-4" v-if="region">
            <div class="form-group">
              <label for="region" class="text-muted"><strong>Участок:</strong></label>
              <select class="form-control" id="region" multiple v-model="data.regions">
                <option :value="region.id" v-for="(region, key) in regions" :key="key">{{ region.region }}</option>
              </select>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label for="month" class="text-muted"><strong>Период:</strong></label>
              <select class="form-control" id="month" multiple v-model="data.periods">
                <option :value="month.id" v-for="(month, key) in months" :key="key">{{ month.name }}</option>
              </select>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label for="version" class="text-muted"><strong>Версия бюджета:</strong></label>
              <select class="form-control" id="version" v-model="data.version">
                <option disabled value>Выберите один из вариантов</option>
                <option :value="version.id" v-for="(version, key) in versions" :key="key">{{ version.name }}</option>
              </select>
            </div>
          </div>
          <div class="col-md-1">
            <div class="btn btn-primary">Применить</div>
          </div>
          <div class="col-md-2 text-right">
            <div class="btn btn-secondary">Загрузить</div>
            <div class="btn btn-secondary">Выгрузить</div>
          </div>
        </div>
      </div>
    </div>
    <div class="card mt-3">
      <h4 class="card-header">Бюджетные параметры</h4>
      <div class="card-body">
        <budget-table></budget-table>
      </div>
    </div>
  </main>
</template>
<script>
  import BudgetTable from "./BudgetTable";

  export default {
    components: {
      BudgetTable
    },
    data() {
      return {
        data: {
          dkres: [],
          regions: [],
          periods: [],
          version: '',
        },
        region: false
      }
    },
    props: {
      initialData: {
        type: Object,
        required: true
      }
    },
    computed: {
      dkres() {
        return this.initialData.dkres;
      },
      regions() {
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
      this.$store.commit('budget/setBudget', this.initialData.budget);
    }
  }
</script>
