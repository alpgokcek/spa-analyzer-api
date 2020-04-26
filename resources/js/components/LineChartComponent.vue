
<script>
import { Line } from 'vue-chartjs';

export default {
   extends: Line,
   mounted() {
         let uri = 'http://127.0.0.1:8000/api/sgmgco?student=41401002&course=22';
         let Codes = new Array();
         let Labels = new Array();
         let Grades = new Array();
         this.axios.get(uri, {headers: {Authorization: "Bearer yGcA6L1vrvXkqBDafTNE3OJogMAr17g9ejLM8GVXYpTlxlrFvVtXLsSiWzmgW10C"}}).then((response) => {
            let data = response.data;
            if(data) {
                
               data.data.forEach(element => {
               Codes.push(element.code);
               Labels.push(element.explanation);
               Grades.push(element.grade);
               });
               this.renderChart({
               labels: Codes,
               datasets: [{
                  label: 'Grade',
                  backgroundColor: '#FC2525',
                  data: Grades
            }]
         }, {responsive: true, maintainAspectRatio: false})
       }
       else {
          console.log('No data');
       }
      });  
      console.log('Component mounted.')          
   }
}
</script>

<style>
</style>
