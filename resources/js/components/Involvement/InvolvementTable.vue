<template>
  <div class="table">
    <preloader v-if="isLoading"></preloader>
    <div class="table__unit">млн.руб. без НДС</div>
    <table>
      <tr class="not-hover">
        <th rowspan="2">ДКРЭ/ВД</th>
        <th colspan="4" class="strong bg">ВОВЛЕЧЕНИЕ</th>
        <th colspan="3" class="strong bg">ОПЕРЕЖАЮЩЕЕ ФИНАНСИРОВАНИЕ</th>
      </tr>
      <tr class="not-hover">
        <th class="strong bg">ВСЕГО:</th>
        <th>за счет прошлого года</th>
        <th>за счет сверхнорматива</th>
        <th>за счет текущего года</th>
        <th class="strong bg">ВСЕГО:</th>
        <th>за счет текущего года</th>
        <th>за счет следующего года</th>
      </tr>
      <template v-for="involvement in involvements">
        <tr class="strong bg">
          <td>{{ involvement.dkre }}</td>
          <td>{{ addWithEmptyHelper([ involvement.total['involve_last'], involvement.total['involve_turnover'],
            involvement.total['involve_current'],
            involvement.total['prepayment'] ]) | roundHelper }}
          </td>
          <td>{{ involvement.total['involve_last'] | roundHelper }}</td>
          <td>{{ involvement.total['involve_turnover'] | roundHelper }}</td>
          <td>{{ involvement.total['involve_current'] | roundHelper }}</td>
          <td>{{ addWithEmptyHelper([ involvement.total['prepayment_current'], involvement.total['prepayment_next'] ]) |
            roundHelper }}
          </td>
          <td>{{ involvement.total['prepayment_current'] | roundHelper }}</td>
          <td>{{ involvement.total['prepayment_next'] | roundHelper }}</td>
        </tr>
        <tr v-for="(activity, key) in involvement.activity" :key="involvement.dkre + key">
          <td>{{ activity.name }}</td>
          <td class="strong bg">{{ addWithEmptyHelper([ activity['involve_last'], activity['involve_turnover'],
            activity['involve_current'] ]) | roundHelper }}
          </td>
          <td><input :value="activity['involve_last'] | roundHelper"
                     v-if="involvement['dkre_id']"
                     :data-region="involvement['dkre_id']"
                     :data-article="involvement['article_id']"
                     :data-activity="activity['activity_id']"
                     :disabled="!mode.edit"
                     name="involve_by_prepayment_last_year"
                     v-prevent-number=""
                     @change="changeData">
            <span v-else>{{ activity['involve_last'] | roundHelper }}</span>
          </td>
          <td><input :value="activity['involve_turnover'] | roundHelper"
                     v-if="involvement['dkre_id']"
                     :data-region="involvement['dkre_id']"
                     :data-article="involvement['article_id']"
                     :data-activity="activity['activity_id']"
                     :disabled="!mode.edit"
                     name="involve_by_turnover"
                     v-prevent-number=""
                     @change="changeData">
            <span v-else>{{ activity['involve_turnover'] | roundHelper }}</span>
          </td>
          <td><input :value="activity['involve_current'] | roundHelper"
                     v-if="involvement['dkre_id']"
                     :data-region="involvement['dkre_id']"
                     :data-article="involvement['article_id']"
                     :data-activity="activity['activity_id']"
                     :disabled="!mode.edit"
                     name="involve_by_prepayment_current_year"
                     v-prevent-number=""
                     @change="changeData">
            <span v-else>{{ activity['involve_current'] | roundHelper }}</span>
          </td>
          <td class="strong bg">{{ addWithEmptyHelper([ activity['prepayment_current'], activity['prepayment_next'] ]) |
            roundHelper }}
          </td>
          <td><input :value="activity['prepayment_current'] | roundHelper"
                     v-if="involvement['dkre_id']"
                     :data-region="involvement['dkre_id']"
                     :data-article="involvement['article_id']"
                     :data-activity="activity['activity_id']"
                     :disabled="!mode.edit"
                     name="prepayment_current_year"
                     v-prevent-number=""
                     @change="changeData">
            <span v-else>{{ activity['prepayment_current'] | roundHelper }}</span>
          </td>
          <td><input :value="activity['prepayment_next'] | roundHelper"
                     v-if="involvement['dkre_id']"
                     :data-region="involvement['dkre_id']"
                     :data-article="involvement['article_id']"
                     :data-activity="activity['activity_id']"
                     :disabled="!mode.edit"
                     name="prepayment_next_year"
                     v-prevent-number=""
                     @change="changeData">
            <span v-else>{{ activity['prepayment_next'] | roundHelper }}</span>
          </td>
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
      involvements() {
        return this.$store.getters['involvement/getInvolvement'];
      }
    },
    methods: {
      changeData(e) {
        this.isLoading = true;
        const payload = {
          period: this.data.currentPeriods[0],
          periods: this.data.currentPeriods,
          regions: this.data.currentRegions,
          version: this.data.currentVersion,
          region: e.target.dataset.region,
          activity: e.target.dataset.activity,
          article: e.target.dataset.article,
          param: e.target.name,
          value: e.target.value
        };

        this.$store.dispatch('involvement/editInvolvement', payload).then(res => {
          this.errors = res.errors;
          this.isLoading = false;
        });
      }
    },
    mounted() {
    }
  }
</script>
