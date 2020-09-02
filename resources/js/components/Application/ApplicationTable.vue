<template>
  <div class="table">
    <div class="table__unit">млн.руб. без НДС</div>
    <table>
      <tr class="not-hover">
        <th rowspan="2">ДКРЭ</th>
        <th colspan="4">Денежная заявка</th>
        <th colspan="4">Форма №22</th>
        <th rowspan="2">Финансирование</th>
        <th colspan="3">План поставки</th>
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
        <th>ИТОГО:</th>
        <th>ЦЗ/РЗ</th>
        <th>СЗ</th>
      </tr>
      <template v-for="app in applications">
        <tr class="strong bg">
          <td>{{ app.dkre }}</td>
          <td>{{ addWithEmptyHelper([ app.total['1'].finance, app.total['2'].finance, app.total['3'].finance ]) | roundHelper}}</td>
          <td>{{ app.total['1'].finance | roundHelper}}</td>
          <td>{{ app.total['2'].finance | roundHelper}}</td>
          <td>{{ app.total['3'].finance | roundHelper}}</td>
          <td>{{ addWithEmptyHelper([ app.total['1'].f22, app.total['2'].f22, app.total['3'].f22 ]) | roundHelper}}</td>
          <td>{{ app.total['1'].f22 | roundHelper}}</td>
          <td>{{ app.total['2'].f22 | roundHelper}}</td>
          <td>{{ app.total['3'].f22 | roundHelper}}</td>
          <td>{{ app.total['1'].budget | roundHelper}}</td>
          <td>{{ addWithEmptyHelper([ app.total['1'].shipment, app.total['2'].shipment ]) | roundHelper}}</td>
          <td>{{ app.total['1'].shipment | roundHelper}}</td>
          <td>{{ app.total['2'].shipment | roundHelper}}</td>
        </tr>
        <tr v-for="(activity, key) in app.activity" :key="app.dkre + key">
          <td>{{ activity.name }}</td>
          <td>{{ addWithEmptyHelper([ activity.source['1'].finance, activity.source['2'].finance, activity.source['3'].finance ]) | roundHelper}}</td>
          <td>{{ activity.source['1'].finance | roundHelper}}</td>
          <td>{{ activity.source['2'].finance | roundHelper}}</td>
          <td>{{ activity.source['3'].finance | roundHelper}}</td>
          <td>{{ addWithEmptyHelper([ activity.source['1'].f22, activity.source['2'].f22, activity.source['3'].f22 ]) | roundHelper}}</td>
          <td>{{ activity.source['1'].f22 | roundHelper}}</td>
          <td>{{ activity.source['2'].f22 | roundHelper}}</td>
          <td>{{ activity.source['3'].f22 | roundHelper}}</td>
          <td>{{ activity.source['1'].budget | roundHelper}}</td>
          <td>{{ addWithEmptyHelper([ activity.source['1'].shipment, activity.source['2'].shipment ]) | roundHelper}}</td>
          <td>{{ activity.source['1'].shipment | roundHelper}}</td>
          <td>{{ activity.source['2'].shipment | roundHelper}}</td>
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
      applications() {
        return this.$store.getters['application/getApplications'];
      }
    },
    methods: {},
    mounted() {
    }
  }
</script>
