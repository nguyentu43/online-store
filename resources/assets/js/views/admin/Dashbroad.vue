<template>
	<div>
		<div class="panel panel-body">
			<el-row>
				<el-col :sm="6" v-for="item in overview" :key="item.title">
					<div class="panel panel-border statistical" :style="{ 'background': item.color, 'color': 'white' }">
						<div class="panel-heading">
							<strong>{{ item.title }}</strong>
						</div>
						<div class="panel-body">
							<h4 v-if="item.format && item.format == 'price'">
								{{ item.value | currency('đ', 0, { symbolOnLeft: false, spaceBetweenAmountAndSymbol: true }) }}
							</h4>
							<h4 v-else>
								{{ item.value }}
							</h4>
						</div>
					</div>
				</el-col>
			</el-row>
		</div>

		<div class="panel panel-border">
			<div class="panel-heading">
				<strong>Thống kê</strong>
			</div>
			<div class="panel-body">
				<div class="panel panel-border">
					<order-chart by="year" :show-table="false" />
				</div>
				<div class="panel panel-border">
					<order-chart by="month" :show-table="false" />
				</div>
			</div>
		</div>

		<div class="panel panel-border">
			<div class="panel-heading">
				<strong>Đơn hàng gần đây</strong>
			</div>
			<div class="panel-body">
				<el-table
				:data="orderData"
				border
				@row-click="orderRowClick"
				height="400"
				>
					<el-table-column
					label="#"
					width="80px"
					>
						<template slot-scope="scope">
							<span class="col">{{ scope.$index + 1 }}</span>
						</template>
					</el-table-column>

					<el-table-column
					label="Mã đơn hàng"
					>
						<template slot-scope="scope">
							<span class="col">{{ scope.row.order_code }}</span>
						</template>
					</el-table-column>

					<el-table-column
					label="Ngày đặt hàng"
					>
						<template slot-scope="scope">
							<span class="col">{{ scope.row.created_at }}</span>
						</template>
					</el-table-column>

					<el-table-column
					label="Tổng giá trị"
					>
						<template slot-scope="scope">
							<span class="col">{{ scope.row.amount | currency('đ', 0, { symbolOnLeft: false, spaceBetweenAmountAndSymbol: true }) }}</span>
						</template>
					</el-table-column>

					<el-table-column
					label="Tình trạng"
					>
						<template slot-scope="scope">
							<span class="col">{{ _.last(scope.row.status).name }}</span>
						</template>
					</el-table-column>
				</el-table>
			</div>
		</div>

		<div class="panel panel-border">
			<div class="panel-heading">
				<strong>Sản phẩm sắp hết</strong>
			</div>
			<div class="panel-body">
				<el-table
				:data="productsSoldOut"
				border
				height="400"
				@row-click="soldOutClick"
				>
					<el-table-column
					label="#"
					width="80px"
					>
						<template slot-scope="scope">
							<span class="col">{{ scope.$index + 1 }}</span>
						</template>
					</el-table-column>

					<el-table-column
					label="Tên sản phẩm"
					>
						<template slot-scope="scope">
							<span class="col">{{ scope.row.product.name }} - Phân loại: {{ scope.row.name }}</span>
						</template>
					</el-table-column>

					<el-table-column
					label="Phân loại"
					width="150px"
					>
						<template slot-scope="scope">
							<span class="col">{{ scope.row.name }}</span>
						</template>
					</el-table-column>

					<el-table-column
					label="Còn lại"
					width="120px"
					>
						<template slot-scope="scope">
							<span class="col">{{ scope.row.quantity }}</span>
						</template>
					</el-table-column>
				</el-table>
			</div>
		</div>

	</div>
</template>

<script>

	import moment from 'moment';
	import OrderChart from '../../components/admin/OrderChart.vue';

	export default{
		data(){
			return {
				orderData: [],
				overview: [],
				productsSoldOut: []
			}
		},
		methods: {
			init(){
				this.axios.get(this.api.products.get(), { params: { skip_prop: 'enable' } })
				.then((res) => {
					this.overview.push({ title: 'Tổng sản phẩm', value: res.data.meta.total, color: '#409eff' });
				})
				.then(() => {
					this.axios.get(this.api.users.get(), { params: { type: 'customer' } })
					.then((res) => {
						this.overview.push({ title: 'Tổng khách hàng', value: res.data.length, color: '#cf9236' })
					})
				})
				.then(() => {
					this.axios.get(this.api.orders.get())
					.then((res) => {
						this.orderData = res.data.orders;
						this.overview.push({ title: 'Tổng đơn hàng', value: res.data.meta.total, color: '#dd6161'})
						this.overview.push({ title: 'Tổng doanh thu', value: res.data.meta.income, format: 'price', color: '#5daf34' })
					})
				});

				this.axios.get(this.api.statistic.getProductSoldOut()).then(res => this.productsSoldOut = res.data);
			},
			orderRowClick(row)
			{
				this.$router.push({ name: 'order-admin', query: { order_code: row.order_code } });
			},
			soldOutClick(row)
			{
				this.$router.push({ name: 'product-edit-admin', params: { slug: row.product.slug } });
			}
		},
		created(){
			this.init();
		},
		components: { OrderChart }
	}
</script>

<style scoped>
	.panel.statistical{
		margin: 0 10px;
	}
</style>