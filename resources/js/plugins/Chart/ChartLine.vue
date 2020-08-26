<template>
  <canvas ref="canvas"></canvas>
</template>


<script>
  import { Line } from 'vue-chartjs';

  /**
   *  График линии
   *
   * */
  export default {
    extends: Line,
    props: {
      initialData: {
        type: Object,
        default: null
      }
    },
    mounted() {
      let {
        chartdata, options = {
          scales: { xAxes: [{ type: 'time', time: { unit: 'month' } }] },
          legend: { display: false }
        }
      } = this.initialData;

      this.renderChart(chartdata, options);
    }
  };
</script>
