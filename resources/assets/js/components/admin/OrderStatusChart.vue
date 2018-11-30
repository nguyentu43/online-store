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
	    		label="Tháng"
	    		width="80px"
	    		class-name="col"
	    		>
	    			<template slot-scope="scope">
	    				{{ scope.row.label }}
	    			</template>
	    		</el-table-column>
	    		<el-table-column
	    		label="Đã giao"
	    		class-name="text-right col"
	    		label-class-name="text-right"
	    		>
	    			<template slot-scope="scope">
	    				{{ scope.row.deliveried  }}
	    			</template>
	    		</el-table-column>

	    		<el-table-column
	    		label="Hoàn trả"
	    		class-name="text-right col"
	    		label-class-name="text-right"
	    		>
	    			<template slot-scope="scope">
	    				{{ scope.row.refund  }}
	    			</template>
	    		</el-table-column>

	    		<el-table-column
	    		label="Huỷ đơn hàng"
	    		class-name="text-right col"
	    		label-class-name="text-right"
	    		>
	    			<template slot-scope="scope">
	    				{{ scope.row.cancel }}
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
			year: {
				type: Number,
				default: moment().year()
			},
			month: {
				type: Number,
				default: moment().month()
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
			
			this.type='LineChart';
			this.options.title = 'Tình trạng đơn hàng ' + this.year;

			this.axios.get(this.api.statistic.getOrder(), { params: { by: 'order_status', year: this.year, month: this.month } })
			.then(res => {

				let data = res.data;
				let sum = 0;
				this.labelTable.fields = { 'Tháng': 'month', 'Đã giao': 'deliveried', 'Hoàn trả': 'refund', 'Huỷ đơn hàng': 'cancel' };
				this.labelTable.title = 'Doanh thu tháng ' + this.month + '/' + this.year;
				this.data = [[ 'Tháng', 'Đã giao', 'Hoàn trả', 'Huỷ đơn hàng' ]];

				let rowSum = { 'label': 'Tổng', 'deliveried': 0, 'refund': 0, 'cancel': 0 }

				for(let i = 1; i<=12; ++i)
				{
					let rowChart = [ "Tháng " + i, 0, 0, 0 ];
					let rowTable = { 'label': i, 'deliveried': 0, 'refund': 0, 'cancel': 0 };

					if(data[i])
					{
						this._.each(data[i], item => {

							if(item['name'] == 'Đã giao')
							{
								rowChart[1] = item['sum'];
								rowTable.deliveried = item['sum'];
								rowSum.deliveried += parseInt(item['sum']);
							}
							else if(item['name'] == 'Hoàn trả')
							{
								rowChart[2] = item['sum'];
								rowTable.refund = item['sum'];
								rowSum.refund += parseInt(item['sum']);
							}
							else
							{
								rowChart[3] = item['sum'];
								rowTable.cancel = item['sum'];
								rowSum.cancel += parseInt(item['sum']);
							}

						});
					}

					this.data.push(rowChart);
					this.dataTable.push(rowTable);
				}

				this.dataTable.push(rowSum);
			});
		},
		components: { GChart, ExportExcel }
	}
</script>