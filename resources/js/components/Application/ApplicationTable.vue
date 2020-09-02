<template>
  <div class="table">
    <div class="table__unit">млн.руб. без НДС</div>
    <table>
      <tr class="not-hover">
        <th rowspan="2">ДКРЭ</th>
        <th colspan="4">Денежная заявка</th>
        <th colspan="4">План поставки</th>
      </tr>
      <tr class="not-hover">
        <th>ИТОГО:</th>
        <th>ЦЗ/РЗ</th>
        <th>СЗ</th>
        <th>ЧДФ</th>
        <th>ИТОГО:</th>
        <th>ЦЗ/РЗ</th>
        <th>СЗ</th>
        <th>ЧДФ</th>
      </tr>
      <template v-for="budget in budgets">
        <tr class="strong bg">
          <td>{{ budget.dkre }}</td>
          <td>{{ budget.total['63430'] | roundHelper }}</td>
          <td>{{ addWithEmptyHelper([ budget.total['63310'], budget.total['63320'], budget.total['63330'],
            budget.total['63340'] ]) | roundHelper }}
          </td>
          <td>{{ budget.total['63310'] | roundHelper }}</td>
          <td>{{ budget.total['63320'] | roundHelper }}</td>
          <td>{{ budget.total['63330'] | roundHelper }}</td>
          <td>{{ budget.total['63340'] | roundHelper }}</td>
          <td>{{ addWithEmptyHelper([ budget.total['63310'], budget.total['63320'], budget.total['63330'],
            budget.total['63340'],
            budget.total['63430'] ]) | roundHelper }}
          </td>
        </tr>
        <tr v-for="(activity, key) in budget.activity" :key="budget.dkre + key">
          <td>{{ activity.name }}</td>
          <td>{{ activity.article['63430'] | roundHelper }}</td>
          <td>{{ addWithEmptyHelper([ activity.article['63310'], activity.article['63320'], activity.article['63330'],
            activity.article['63340'] ]) | roundHelper }}
          </td>
          <td>{{ activity.article['63310'] | roundHelper }}</td>
          <td>{{ activity.article['63320'] | roundHelper }}</td>
          <td>{{ activity.article['63330'] | roundHelper }}</td>
          <td>{{ activity.article['63340'] | roundHelper }}</td>
          <td>{{ addWithEmptyHelper([ activity.article['63310'], activity.article['63320'], activity.article['63330'],
            activity.article['63340'],
            activity.article['63430'] ]) | roundHelper }}
          </td>
        </tr>
      </template>
    </table>
  </div>
</template>

<script>
  export default {
    data() {
      return {}
    },
    computed: {
      budgets() {
        return this.$store.getters['budget/getBudget'];
      }
    },
    methods: {},
    mounted() {
    }
  }
</script>
