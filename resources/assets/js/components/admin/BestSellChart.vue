<template>
	<GChart
	type="PieChart"
	:data="data"
	:options="options"
	/>
</template>

<script>
	import { GChart } from 'vue-google-charts';
	import moment from 'moment';

	export default{
		props: {
			year: {
				type: Number,
				default: moment().year()
			},
			month: {
				type: Number
			}
		},
		data(){
			return {
				data: [],
				options: {}
			}
		},
		mounted(){
			this.axios.get(this.api.statistic.getOrder(), { params: { by: 'bestsell', year: this.year } }).then(res => {

				let data = res.data;
				let sum = 0;

				this.data = this._.map(data.result, item => {
					sum += parseInt(item.count);
					return [ item.product_name + ' - ' + item.sku_name, item.count ];
				});

				this.data.unshift(['Sản phẩm', 'Số lượng bán ra']);

				if(sum != data.total)
				{
					this.data.push([ 'Khác', parseInt(data.total) - sum ]);
				}

			});
		},
		components: { GChart }
	}
</script>