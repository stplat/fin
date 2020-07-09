<template>
  <main>
    <div class="card mb-1">
      <h4 class="card__header">Выбор бюджетных параметров</h4>
      <div class="card__body">
        <div class="card__panel">
          <div class="card-panel">
            <div class="card-panel__item">
              <label for="zavod_dkre" class="label mb-1">По участкам:</label>
              <div>
                <label title="Участки/ДКРЭ" class="toggle">
                  <input type="checkbox" id="zavod_dkre" hidden="hidden">
                  <span class="toggle__box"></span>
                </label>
              </div>
            </div>
            <div class="card-panel__item">
              <label for="dkre" class="label mb-1">ДКРЭ:</label>
              <select class="select select--multiple" multiple name="" id="dkre">
                <option value="asd">ДКРЭ_ОКТ</option>
              </select>
            </div>
            <div class="card-panel__item">
              <label for="zavod" class="label mb-1">Участок:</label>
              <select class="select select--multiple" multiple name="" id="zavod">
                <option value="asd">ДКРЭ_ОКТ_ОКТ</option>
              </select>
            </div>
            <div class="card-panel__item">
              <label for="period" class="label mb-1">Период:</label>
              <select class="select select--multiple" multiple name="" id="period">
                <option value="asd">1 квартал 2020</option>
                <option value="asd">2 квартал 2020</option>
                <option value="asd">3 квартал 2020</option>
                <option value="asd">4 квартал 2020</option>
                <option value="asd">1 квартал 2020</option>
              </select>
            </div>
            <div class="card-panel__item">
              <label for="version" class="label mb-1">Версия бюджета:</label>
              <select class="select" name="" id="version">
                <option disabled="disabled" value="">Выберите версию</option>
                <option value="asd">15.04.2020</option>
              </select>
            </div>
            <div class="card-panel__item">
              <button>Выгрузить</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="card">
      <div class="card__body">
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
      return {}
    },
    props: {
      initialData: {
        type: Array
      }
    },
    mounted() {
    }
  }
</script>
