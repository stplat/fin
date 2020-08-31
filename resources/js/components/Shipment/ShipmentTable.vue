<template>
  <div class="table">
    <preloader v-if="isLoading"></preloader>
    <div class="table__unit">млн.руб. без НДС</div>
    <table>
      <tr class="not-hover">
        <th rowspan="2" style="width: 200px">ДКРЭ/СТАТЬЯ</th>
        <th colspan="5">ВСЕГО:</th>
        <th colspan="5">ЦЕНТРАЛИЗАЦИЯ</th>
        <th colspan="5">САМОЗАКУП</th>
      </tr>
      <tr>
        <th>ВСЕГО:</th>
        <th>ПЕР</th>
        <th>ПВД</th>
        <th>КВ</th>
        <th>ПРОЧИЕ</th>
        <th>ИТОГО:</th>
        <th>ПЕР</th>
        <th>ПВД</th>
        <th>КВ</th>
        <th>ПРОЧИЕ</th>
        <th>ИТОГО:</th>
        <th>ПЕР</th>
        <th>ПВД</th>
        <th>КВ</th>
        <th>ПРОЧИЕ</th>
      </tr>
      <template v-for="shipment in shipments">
        <tr class="strong bg">
          <td>{{ shipment.dkre }}</td>
          <td>{{ addWithEmptyHelper([ shipment.total['01']['1'], shipment.total['01']['2'], shipment.total['21']['1'],
            shipment.total['21']['2'], shipment.total['61']['1'],
            shipment.total['61']['2'], shipment.total['81']['1'], shipment.total['81']['2'] ]) | roundHelper }}
          </td>
          <td>{{ addWithEmptyHelper([ shipment.total['01']['1'], shipment.total['01']['2'] ]) | roundHelper }}</td>
          <td>{{ addWithEmptyHelper([ shipment.total['21']['1'], shipment.total['21']['2'] ]) | roundHelper }}</td>
          <td>{{ addWithEmptyHelper([ shipment.total['61']['1'], shipment.total['61']['2'] ]) | roundHelper }}</td>
          <td>{{ addWithEmptyHelper([ shipment.total['81']['1'], shipment.total['81']['2'] ]) | roundHelper }}</td>
          <td>{{ addWithEmptyHelper([ shipment.total['01']['1'], shipment.total['21']['1'],
            shipment.total['61']['1'], shipment.total['81']['1'] ]) | roundHelper }}
          </td>
          <td>{{ shipment.total['01']['1'] | roundHelper }}</td>
          <td>{{ shipment.total['21']['1'] | roundHelper }}</td>
          <td>{{ shipment.total['61']['1'] | roundHelper }}</td>
          <td>{{ shipment.total['81']['1'] | roundHelper }}</td>
          <td>{{ addWithEmptyHelper([ shipment.total['01']['2'], shipment.total['21']['2'],
            shipment.total['61']['2'], shipment.total['81']['2'] ]) | roundHelper }}
          </td>
          <td>{{ shipment.total['01']['2'] | roundHelper }}</td>
          <td>{{ shipment.total['21']['2'] | roundHelper }}</td>
          <td>{{ shipment.total['61']['2'] | roundHelper }}</td>
          <td>{{ shipment.total['81']['2'] | roundHelper }}</td>
        </tr>
        <tr v-for="(article, key) in shipment.article" :key="shipment.dkre + key">
          <td>{{ article.name }}</td>
          <td>{{ addWithEmptyHelper([ article.activity['01']['1'], article.activity['01']['2'], article.activity['21']['1'],
            article.activity['21']['2'], article.activity['61']['1'], article.activity['61']['2'],
            article.activity['61']['2'], article.activity['81']['1'] ]) | roundHelper }}
          </td>
          <td>{{ addWithEmptyHelper([ article.activity['01']['1'], article.activity['01']['2'] ]) | roundHelper }}</td>
          <td>{{ addWithEmptyHelper([ article.activity['21']['1'], article.activity['21']['2'] ]) | roundHelper }}</td>
          <td>{{ addWithEmptyHelper([ article.activity['61']['1'], article.activity['61']['2'] ]) | roundHelper }}</td>
          <td>{{ addWithEmptyHelper([ article.activity['81']['1'], article.activity['81']['2'] ]) | roundHelper }}</td>
          <td>{{ addWithEmptyHelper([ article.activity['01']['1'], article.activity['21']['1'],
            article.activity['61']['1'], article.activity['81']['1'] ]) | roundHelper }}
          </td>
          <td>{{ article.activity['01']['1'] | roundHelper }}</td>
          <td>{{ article.activity['21']['1'] | roundHelper }}</td>
          <td>{{ article.activity['61']['1'] | roundHelper }}</td>
          <td>{{ article.activity['81']['1'] | roundHelper }}</td>
          <td>{{ addWithEmptyHelper([ article.activity['01']['2'], article.activity['21']['2'],
            article.activity['61']['2'], article.activity['81']['2'] ]) | roundHelper }}
          </td>
          <td>{{ article.activity['01']['2'] | roundHelper }}</td>
          <td>{{ article.activity['21']['2'] | roundHelper }}</td>
          <td>{{ article.activity['61']['2'] | roundHelper }}</td>
          <td>{{ article.activity['81']['2'] | roundHelper }}</td>
        </tr>
      </template>
    </table>
  </div>
</template>

<script>
  export default {
    data() {
      return {
        isLoading: false
      }
    },
    props: {
      data: {
        type: Object,
        required: true
      }
    },
    computed: {
      shipments() {
        return this.$store.getters['shipment/getShipments'].map(item => {
          const emptySource = { 1: 0, 2: 0, 3: 0 };
          item.article.map(article => {
            if (!article.activity.hasOwnProperty('01')) article.activity['01'] = emptySource;
            if (!article.activity.hasOwnProperty('21')) article.activity['21'] = emptySource;
            if (!article.activity.hasOwnProperty('61')) article.activity['61'] = emptySource;
            if (!article.activity.hasOwnProperty('81')) article.activity['81'] = emptySource;

            for (let i in article.activity) {
              if (!article.activity[i].hasOwnProperty(1)) article.activity[i][1] = 0;
              if (!article.activity[i].hasOwnProperty(2)) article.activity[i][2] = 0;
              if (!article.activity[i].hasOwnProperty(3)) article.activity[i][3] = 0;
            }

            return article;
          });

          if (!item.total.hasOwnProperty('01')) item.total['01'] = emptySource;
          if (!item.total.hasOwnProperty('21')) item.total['21'] = emptySource;
          if (!item.total.hasOwnProperty('61')) item.total['61'] = emptySource;
          if (!item.total.hasOwnProperty('81')) item.total['81'] = emptySource;

          for (let i in item.total) {
            if (!item.total[i].hasOwnProperty(1)) item.total[i][1] = 0;
            if (!item.total[i].hasOwnProperty(2)) item.total[i][2] = 0;
            if (!item.total[i].hasOwnProperty(3)) item.total[i][3] = 0;
          }

          return item;
        });
      }
    },
    methods: {},
    mounted() {
    }
  }
</script>
