<template>
  <canvas ref="canvas"></canvas>
</template>

<script>
  import { Bar } from 'vue-chartjs';

  /**
   *  ГРАФИК ГИСТОГРАММЫ
   *
   * Принимает объект со свойствами @chartdata, @options
   * @chartdata - объект со свойствами:
   *  label array
   *  dataset array
   *
   * */
  export default {
    extends: Bar,
    props: {
      initialData: {
        type: Object,
        default: null
      }
    },
    mounted() {
      let {
        chartdata, options = {
          scales: { yAxes: [{ ticks: { beginAtZero: true } }] },
          legend: { display: false }
        }
      } = this.initialData;

      const colors = ['rgba(255, 99, 132, 0.8)', 'rgba(54, 162, 235, 0.8)', 'rgba(255, 206, 86, 0.8)', 'rgba(75, 192, 192, 0.8)', 'rgba(153, 102, 255, 0.8)', 'rgba(255, 159, 64, 0.8)'];
      chartdata.datasets[0].backgroundColor = chartdata.datasets[0].data.map((item, i) => colors[i % colors.length]);

      this.renderChart(chartdata, options);
    }
  };
</script>
