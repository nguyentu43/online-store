<template>
	<div v-loading="loading">
		<GChart
		:type="type"
		:data="data"
		:options="options"
		/>

		<div v-if="showTable">
			<div class="mb-1">
	    		<export-excel
		    	:data="dataTable"
		    	:fields="labelTable.fields"
		    	:title="labelTable.title"
		    	>
		    		<el-button icon="el-icon-download" size="small">Xuất Excel</el-button>
		    	</export-excel>
	    	</div>
	   		
	    	<el-table
	    	:data="dataTable"
	    	v-if="dataTable.length > 0"
	    	border
	    	style="width: 50%"
	    	>
	    		<el-table-column
	    		:label="labelTable.label[0]"
	    		width="80px"
	    		>
	    			<template slot-scope="scope">
	    				<div class="col">{{ scope.row.label }}</div>
	    			</template>
	    		</el-table-column>
	    		<el-table-column
	    		:label="labelTable.label[1]"
	    		prop="sum"
	    		class-name="text-right col"
	    		label-class-name="text-right"
	    		>
	    			<template slot-scope="scope">
	    				{{ scope.row.sum | currency('đ', 0, { symbolOnLeft: false, spaceBetweenAmountAndSymbol: true }) }}
	    			</template>
	    		</el-table-column>
	    	</el-table>
		</div>
	</div>
</template>

<script>
	import { GChart } from 'vue-google-charts';
	import moment from 'moment';
	import ExportExcel from 'vue-json-excel';

	export default{
		props: {
			by: {
				type: String,
				required: true
			},
			year: {
				type: Number,
				default: moment().year()
			},
			month: {
				type: Number,
				default: moment().month() + 1
			},
			showTable: {
				type: Boolean,
				default: true
			}
		},
		data(){
			return {
				data: [],
				dataTable: [],
				labelTable: {
					label: []
				},
				options: {
					height: 350
				},
				loading: false,
				type: 'ColumnChart'
			}
		},
		mounted(){

			let url = this.api.statistic.getOrder();

			if(this.by == 'year')
			{
				this.options.title = 'Doanh thu năm ' + this.year;
				url += '?year=' + this.year;

				this.loading = true;
				this.axios.get(url)
				.then((res) => {

					let data = res.data;
					let sum = 0;

					this.labelTable.fields = { 'Tháng': 'label', 'Doanh thu': 'sum' };
					this.labelTable.label = [ 'Tháng', 'Doanh thu' ];
					this.labelTable.title = 'Doanh thu năm ' + this.year;
					this.data = [[ 'Tháng', 'Doanh thu tháng' ]];

					for(let i = 1; i<=12; ++i)
					{
						if(data[i])
						{
							this.data.push([ "Tháng " + i, parseInt(data[i].sum)]);
							sum += parseInt(data[i].sum);
							this.dataTable.push({ label: i, sum: parseInt(data[i].sum) });
						}
						else
						{
							this.data.push([ "Tháng " + i, 0 ]);
							this.dataTable.push({ label: i, sum: 0 });
						}
					}
					this.dataTable.push({ label: 'Tổng', sum });
					
					this.loading = false;

				});
			}
			else if(this.by == 'month')
			{
				url += `?by=month&year=${ this.year }&month=${ this.month }`;
				this.options.title = 'Doanh thu tháng ' + this.month + '/' + this.year;

				this.loading = true;
				this.axios.get(url)
				.then((res) => {
					let data = res.data;
					let daysInMonth = moment().daysInMonth();
					let sum = 0;

					this.labelTable.fields = { 'Ngày': 'label', 'Doanh thu': 'sum' };
					this.labelTable.label = [ 'Ngày', 'Doanh thu' ];
					this.labelTable.title = 'Doanh thu tháng ' + this.month + '/' + this.year;
					this.data = [[ 'Ngày', 'Doanh thu ngày' ]];

					for(let i = 1; i<=daysInMonth; ++i)
					{
						if(data[i])
						{
							sum += parseInt(data[i].sum);
							this.data.push([ "Ngày " + i, parseInt(data[i].sum)]);
							this.dataTable.push( { label: i, sum: parseInt(data[i].sum) } );
						}
						else
						{
							this.data.push([ "Ngày " + i, 0 ]);
							this.dataTable.push( { label: i, sum: 0 } )
						}
					}
					this.dataTable.push({ label: 'Tổng', sum });

					this.loading = false;

				});
			}
		},
		components: { GChart, ExportExcel }
	}
</script>