<template>
  <canvas ref="canvas"></canvas>
</template>


<script>
  import { HorizontalBar } from 'vue-chartjs';

  /**
   *  График горизонтальной гистрограммы
   *
   * */
  export default {
    extends: HorizontalBar,
    props: {
      initialData: {
        type: Object,
        default: null
      }
    },
    mounted() {
      let {
        chartdata, options = {
          scales: { xAxes: [{ ticks: { beginAtZero: true }, scaleLabel: { display: true, labelString: 'Количество, шт.' } }] },
          legend: { display: false }
        }
      } = this.initialData;

      this.renderChart(chartdata, options);
    }
  };
</script>
