<template>
  <div class="table">
    <div class="table__unit">млн.руб. без НДС</div>
    <table>
      <tr class="not-hover">
        <th rowspan="2">ДКРЭ/ВД</th>
        <th colspan="4">Вовлечение</th>
        <th colspan="3">Опережающее финансирование</th>
      </tr>
      <tr class="not-hover">
        <th>ВСЕГО:</th>
        <th>за счет аванса прошлого года</th>
        <th>за счет сверхнорматива</th>
        <th>за счет аванса текущего года</th>
        <th>ВСЕГО:</th>
        <th>аванс текущего года</th>
        <th>аванс со следующего года</th>
      </tr>
      <template v-for="involvement in involvements">
        <tr class="strong bg">
          <td>{{ involvement.dkre }}</td>
          <td>{{ addWithEmptyHelper([ involvement.total['involve_last'], involvement.total['involve_turnover'], involvement.total['involve_current'],
            involvement.total['prepayment'] ]) | roundHelper }}</td>
          <td>{{ involvement.total['involve_last'] | roundHelper }}</td>
          <td>{{ involvement.total['involve_turnover'] | roundHelper }}</td>
          <td>{{ involvement.total['involve_current'] | roundHelper }}</td>
          <td>{{ involvement.total['prepayment'] | roundHelper }}</td>
        </tr>
        <tr v-for="(activity, key) in involvement.activity" :key="involvement.dkre + key">
          <td>{{ activity.name }}</td>
          <td>{{ addWithEmptyHelper([ activity['involve_last'], activity['involve_turnover'], activity['involve_current'],
            activity['prepayment'] ]) | roundHelper }}</td>
          <td>{{ activity['involve_last'] | roundHelper }}</td>
          <td>{{ activity['involve_turnover'] | roundHelper }}</td>
          <td>{{ activity['involve_current'] | roundHelper }}</td>
          <td>{{ activity['prepayment'] | roundHelper }}</td>
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
      involvements() {
        return this.$store.getters['involvement/getInvolvement'];
      }
    },
    methods: {},
    mounted() {
    }
  }
</script>
