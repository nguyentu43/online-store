<template>
	<div class="panel panel-body">
		<el-date-picker
	      v-model="date"
	      type="month"
	      placeholder="Chọn tháng"
	      size="small"
	      >
	    </el-date-picker>
		<el-tabs v-model="activeName" :stretch="true" v-if="reload">
		    <el-tab-pane label="Doanh thu tháng" name="report-month" :lazy="true">
		    	<order-chart by="month" :month="date.getMonth() + 1" :year="date.getFullYear()"/>
		    </el-tab-pane>
		    <el-tab-pane label="Doanh thu năm" name="report-year" :lazy="true">
		    	<order-chart by="year" :year="date.getFullYear()" />
		    </el-tab-pane>
		    <el-tab-pane label="Tình trạng đơn hàng" name="report-order-status" :lazy="true">
		    	<order-status-chart :year="date.getFullYear()" />
		    </el-tab-pane>
		    <el-tab-pane label="Sản phẩm bán chạy" name="product-bestsell" :lazy="true">
		    	<best-sell-chart :year="date.getFullYear()" />
		    </el-tab-pane>
		</el-tabs>
	</div>
</template>

<script>
	import OrderChart from '../../components/admin/OrderChart.vue';
	import BestSellChart from '../../components/admin/BestSellChart.vue';
	import OrderStatusChart from '../../components/admin/OrderStatusChart.vue';
	import moment from 'moment';
	import Vue from 'vue';

	export default{
		data(){
			return {
				date: new Date(),
				activeName: 'report-month',
				reload: true
			}
		},
		watch: {
			date(){
				this.reload = false;
				Vue.nextTick(() => this.reload = true);
			}
		},
		created(){
		},
		components: { OrderChart, BestSellChart, OrderStatusChart },
	}
</script>