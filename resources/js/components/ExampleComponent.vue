<template>
  <div class="container">
    <line-chart
      v-if="loaded"
      :chartdata="chartdata"
      :options="options"/>
      <p>yarrak</p>
  </div>
</template>

<script>
import LineChart from './Chart.vue'
import axios from 'axios'

export default {
  name: 'LineChartContainer',
  components: { LineChart },
  data: () => ({
    loaded: false,
    chartdata: null
  }),
  async mounted () {
    this.loaded = false
    try {
      let uri = 'http://localhost:8000/api/sgmgco';
      let Years = new Array();
      let Labels = new Array();
      let Prices = new Array();
      await this.axios.get(uri, {"student":"41401002", "course": "22"}, {headers: {Authorization: "Bearer yGcA6L1vrvXkqBDafTNE3OJogMAr17g9ejLM8GVXYpTlxlrFvVtXLsSiWzmgW10C"}})
      .then((response) => {
            let data = response.data;
            if(data) {
               data.forEach(element => {
                Codes.push(element.code);
                Labels.push(element.explanation);
                Grades.push(element.grade);
               });

              this.chartdata = {
                      labels: Codes,
                      datasets: [{
                          label: 'Grade',
                          backgroundColor: '#FC2525',
                          data: Grades
                    }]}
              this.options = {responsive: true, maintainAspectRatio: false}
              this.loaded = true
              }
      })
    } catch (e) {
      console.error(e)
    }
  }
}
</script>