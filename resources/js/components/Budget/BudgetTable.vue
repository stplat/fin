<template>
  <div class="table">
    <alert v-for="(error, key) in errors" :key="key" v-html="error"></alert>
    <preloader v-if="isLoading"></preloader>
    <div class="table__unit">млн.руб. без НДС</div>
    <table>
      <tr class="not-hover">
        <th rowspan="2">ДКРЭ/ВД</th>
        <th rowspan="2">Бюджет на новые закуп-ые</th>
        <th colspan="4" class="strong bg">Вовлечение</th>
        <th colspan="3" class="strong bg">Опережающее финансирование</th>
        <th rowspan="2">Лимит на закупку матер-ов</th>
        <th colspan="5">Лимит на закупку топлива</th>
        <th rowspan="2">ВСЕГО:</th>
      </tr>
      <tr class="not-hover">
        <th class="strong bg">ИТОГО:</th>
        <th>за счет прошлого года</th>
        <th>за счет сверх-норматива</th>
        <th>за счет текущего года</th>
        <th class="strong bg">ИТОГО:</th>
        <th>за счет текущего года</th>
        <th>за счет следущ-го года</th>
        <th>ИТОГО:</th>
        <th>Дизельное топливо</th>
        <th>Мазут</th>
        <th>Уголь</th>
        <th>Другие виды топлива<br/>(бензин и газ)</th>
      </tr>
      <template v-for="budget in budgets">
        <tr class="strong bg">
          <td>{{ budget.dkre }}</td>
          <td>{{ budget.total.article['63400'] | roundHelper }}</td>
          <td>{{ addWithEmptyHelper([ budget.total['involve_last'], budget.total['involve_turnover'],
            budget.total['involve_current'] ]) |
            roundHelper }}
          </td>
          <td>{{ budget.total['involve_last'] | roundHelper }}</td>
          <td>{{ budget.total['involve_turnover'] | roundHelper }}</td>
          <td>{{ budget.total['involve_current'] | roundHelper }}</td>
          <td>{{ addWithEmptyHelper([ budget.total['prepayment_current'], budget.total['prepayment_next'] ]) |
            roundHelper }}
          </td>
          <td>{{ budget.total['prepayment_current'] | roundHelper }}</td>
          <td>{{ budget.total['prepayment_next'] | roundHelper }}</td>
          <td>{{ budget.total['finance_material'] | roundHelper }}</td>
          <td>{{ addWithEmptyHelper([ budget.total.article['63310'], budget.total.article['63320'],
            budget.total.article['63330'],
            budget.total.article['63340'] ]) |
            roundHelper }}
          </td>
          <td>{{ budget.total.article['63310'] | roundHelper }}</td>
          <td>{{ budget.total.article['63320'] | roundHelper }}</td>
          <td>{{ budget.total.article['63330'] | roundHelper }}</td>
          <td>{{ budget.total.article['63340'] | roundHelper }}</td>
          <td>{{ budget.total['finance'] | roundHelper }}</td>
        </tr>
        <tr v-for="(activity, key) in budget.activity" :key="budget.dkre + key">
          <td>{{ activity.name }}</td>
          <td>{{ activity.article['63400'] | roundHelper }}</td>
          <td>{{ addWithEmptyHelper([ activity['involve_last'], activity['involve_turnover'],
            activity['involve_current'] ]) |
            roundHelper }}
          </td>
          <td><input :value="activity['involve_last'] | roundHelper"
                     v-if="budget['dkre_id']"
                     :data-region="budget['dkre_id']"
                     :data-activity="activity['activity_id']"
                     :data-article="63400"
                     :disabled="!mode.edit"
                     name="involve_by_prepayment_last_year"
                     v-prevent-number=""
                     @change="changeData">
            {{ !budget['dkre_id'] ? activity['involve_last'] : '' | roundHelper }}
          </td>
          <td><input :value="activity['involve_turnover'] | roundHelper"
                     v-if="budget['dkre_id']"
                     :data-region="budget['dkre_id']"
                     :data-activity="activity['activity_id']"
                     :data-article="63400"
                     :disabled="!mode.edit"
                     name="involve_by_turnover"
                     v-prevent-number=""
                     @change="changeData">
            {{ !budget['dkre_id'] ? activity['involve_turnover'] : '' | roundHelper }}
          </td>
          <td><input :value="activity['involve_current'] | roundHelper"
                     v-if="budget['dkre_id']"
                     :data-region="budget['dkre_id']"
                     :data-activity="activity['activity_id']"
                     :data-article="63400"
                     :disabled="!mode.edit"
                     name="involve_by_prepayment_current_year"
                     v-prevent-number=""
                     @change="changeData">
            {{ !budget['dkre_id'] ? activity['involve_current'] : '' | roundHelper }}
          </td>
          <td>{{ addWithEmptyHelper([ activity['prepayment_current'], activity['prepayment_next'] ]) |
            roundHelper }}
          </td>
          <td><input :value="activity['prepayment_current'] | roundHelper"
                     v-if="budget['dkre_id']"
                     :data-region="budget['dkre_id']"
                     :data-activity="activity['activity_id']"
                     :data-article="63400"
                     :disabled="!mode.edit"
                     name="prepayment_current_year"
                     v-prevent-number=""
                     @change="changeData">
            {{ !budget['dkre_id'] ? activity['prepayment_current'] : '' | roundHelper }}
          </td>
          <td><input :value="activity['prepayment_next'] | roundHelper"
                     v-if="budget['dkre_id']"
                     :data-region="budget['dkre_id']"
                     :data-activity="activity['activity_id']"
                     :data-article="63400"
                     :disabled="!mode.edit"
                     name="prepayment_next_year"
                     v-prevent-number=""
                     @change="changeData">
            {{ !budget['dkre_id'] ? activity['prepayment_next'] : '' | roundHelper }}
          </td>
          <td>{{ activity['finance_material'] | roundHelper }}</td>
          <td>{{ addWithEmptyHelper([ activity.article['63310'], activity.article['63320'], activity.article['63330'],
            activity.article['63340'] ]) |
            roundHelper }}
          </td>
          <td>{{ activity.article['63310'] | roundHelper }}</td>
          <td>{{ activity.article['63320'] | roundHelper }}</td>
          <td>{{ activity.article['63330'] | roundHelper }}</td>
          <td>{{ activity.article['63340'] | roundHelper }}</td>
          <td>{{ activity['finance'] | roundHelper }}</td>
        </tr>
      </template>
    </table>
  </div>
</template>

<script>
  export default {
    data() {
      return {
        isLoading: false,
        errors: []
      }
    },
    props: {
      mode: {
        type: Object,
        required: true
      },
      data: {
        type: Object,
        required: true
      }
    },
    computed: {
      budgets() {
        return this.$store.getters['budget/getBudget'];
      }
    },
    methods: {
      changeData(e) {
        if (this.validation(e.target.value)) {
          this.isLoading = true;
          const payload = Object.assign({
            period: this.data.periods[0],
            region: e.target.dataset.region,
            activity: e.target.dataset.activity,
            article: e.target.dataset.article,
            param: e.target.name,
            value: e.target.value ? e.target.value.replace(',', '.') : 0
          }, this.data);

          this.$store.dispatch('budget/editBudget', payload).then(res => {
            this.errors = res.errors;
            this.isLoading = false;
          });
        }

      },
      validation(str) {
        const regexp = /^-?\d*[,.]?\d{0,3}$/i;
        let result = true;
        this.errors = [];

        if (!regexp.test(str) && str !== '') {
          this.errors.push('Максимальное число знаком после запятой:<strong>3</strong>');
          result = false;
        }

        return result;
      }
    },
    mounted() {
    }
  }
</script>
