<template>
    <div class="relative w-50 h-32">
        <Doughnut :data="chartData" :options="chartOptions" :plugins="gaugePlugins" />
    </div>
</template>


<script setup>
import { computed } from 'vue';

import { Doughnut } from 'vue-chartjs';
import { Chart as ChartJS, ArcElement, Tooltip } from 'chart.js';

ChartJS.register(ArcElement,Tooltip);

const props = defineProps({
    avgRating:{
        type:Number,
        required:true,
        default:0
    },
    maxRating:{
        type:Number,
        default:5
    }
});

const gaugePlugins = [
  {
    id: 'gaugeText',
    beforeDraw: (chart) => {
        const { width, height, ctx } = chart;
        ctx.restore();  
        const text = props.avgRating.toFixed(1);    
        ctx.font = 'bold 30px sans-serif';
        ctx.fillStyle = '#1f2937'; 
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';    
        const textX = width / 2;
        const textY = height - 50;  
        ctx.fillText(text, textX, textY);   
        
        const subText = 'Átlag értékelés';
        ctx.font = '14px sans-serif';
        ctx.fillStyle = '#6b7280'

        const subTextX = width / 2;
        const subTextY = height - 15;

        ctx.fillText(subText, subTextX, subTextY);
      ctx.save();
    }
  }
];

const chartData = computed(() => {
  return {
    datasets: [
      {

        data: [props.avgRating, props.maxRating - props.avgRating],
        backgroundColor: [
          '#36082A', 
          '#e5e7eb' 
        ],

        borderWidth: 0,
      },
    ],
  };
});

const chartOptions = computed(() => ({
  responsive: true,
  maintainAspectRatio: false,
  rotation: -110, 
  circumference: 220, 
  cutout: '70%', 
  plugins: {
    tooltip: {
      enabled: false,
    },
  },
}));

</script>
