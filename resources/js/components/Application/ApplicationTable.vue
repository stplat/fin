<template>
  <div class="table">
    <alert v-for="(error, key) in errors" :key="key" v-html="error"></alert>
    <preloader v-if="isLoading"></preloader>
    <div class="table__unit">млн.руб. без НДС</div>
    <table>
      <tr class="not-hover">
        <th rowspan="2">ДКРЭ</th>
        <th colspan="4">Денежная заявка</th>
        <th colspan="4">Форма №22</th>
        <th rowspan="2">Лимит</th>
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
        <tr class="strong bg" v-if="app.total.hasOwnProperty('1')">
          <td class="text-left">{{ app.dkre }}</td>
          <td>{{ addWithEmptyHelper([ app.total['1'].finance, app.total['2'].finance, app.total['3'].finance ]) |
            roundHelper}}
          </td>
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
          <td class="text-left pl-3">{{ activity.name }}</td>
          <td class="strong bg">{{ addWithEmptyHelper([ activity.source['1'].finance, activity.source['2'].finance,
            activity.source['3'].finance ]) | roundHelper}}
          </td>
          <td><input :value="activity.source['1'].finance | roundHelper"
                     v-if="app['dkre_id']"
                     :data-region="app['dkre_id']"
                     :data-source="1"
                     :data-activity="activity['activity_id']"
                     :disabled="!mode.edit"
                     name="count"
                     v-prevent-number=""
                     @change="changeData">
            {{ !app['dkre_id'] ? activity.source['1'].finance : '' | roundHelper }}
          </td>
          <td><input :value="activity.source['2'].finance | roundHelper"
                     v-if="app['dkre_id']"
                     :data-region="app['dkre_id']"
                     :data-source="2"
                     :data-activity="activity['activity_id']"
                     :disabled="!mode.edit"
                     name="count"
                     v-prevent-number=""
                     @change="changeData">
            {{ !app['dkre_id'] ? activity.source['2'].finance : '' | roundHelper }}
          </td>
          <td><input :value="activity.source['3'].finance | roundHelper"
                     v-if="app['dkre_id']"
                     :data-region="app['dkre_id']"
                     :data-source="3"
                     :data-activity="activity['activity_id']"
                     :disabled="!mode.edit"
                     name="count"
                     v-prevent-number=""
                     @change="changeData">
            {{ !app['dkre_id'] ? activity.source['3'].finance : '' | roundHelper }}
          </td>
          <td class="strong bg">{{ addWithEmptyHelper([ activity.source['1'].f22, activity.source['2'].f22,
            activity.source['3'].f22 ]) | roundHelper}}
          </td>
          <td>{{ activity.source['1'].f22 | roundHelper}}</td>
          <td>{{ activity.source['2'].f22 | roundHelper}}</td>
          <td>{{ activity.source['3'].f22 | roundHelper}}</td>
          <td class="strong bg">{{ activity.source['1'].budget | roundHelper}}</td>
          <td class="strong bg">{{ addWithEmptyHelper([ activity.source['1'].shipment, activity.source['2'].shipment ])
            | roundHelper}}
          </td>
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
      applications() {
        return this.$store.getters['application/getApplications'];
      }
    },
    methods: {
      changeData(e) {
        if (this.validation(e.target.value)) {
          this.isLoading = true;
          const payload = Object.assign({
            period: this.data.periods[0],
            source: e.target.dataset.source,
            activity: e.target.dataset.activity,
            region: e.target.dataset.region,
            param: e.target.name,
            value: e.target.value ? e.target.value.replace(',', '.') : 0
          }, this.data);

          this.$store.dispatch('application/editApplication', payload).then(res => {
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
