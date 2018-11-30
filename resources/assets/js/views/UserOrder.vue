<template>
	<div v-loading="loading">
		<div class="panel panel-body" v-if="orders.length > 0">
			<el-collapse v-model="activeOrder">
			  <el-collapse-item 
			  v-for="item in orders" 
			  :name="item.order_code" 
			  :key="item.id">
			  	<template slot="title">
			  		<div class="col">
			  			Đơn hàng #{{ item.order_code }} - Tình trạng: {{ _.last(item.status).name }} - Giá trị: {{ item.amount | currency('đ', 0, { symbolOnLeft: false, spaceBetweenAmountAndSymbol: true }) }}
			  		</div>
			  	</template>
			  	<div class="panel panel-body col">

			  		<el-steps :active="_.last(item.status).id > 4 ? 5 : _.last(item.status).id" align-center class="mb-1">
					  <el-step 
					  v-for="status in item.status" 
					  :title="status.name" 
					  :description="status.pivot.updated_at"
					  :key="status.id">
					  </el-step>
					  <template v-if="_.last(item.status).id < 4">
						  <el-step
						  v-for="status in _.range(_.last(item.status).id + 1, 5)"
						  :title="_.find(orderStatus, {id: status}).name"
						  :key="status" 
						  description="">
						  </el-step>
					  </template>
					</el-steps>

					<ul>
		  				<li>Họ và tên: {{ item.name }}</li>
		  				<li>
						Địa chỉ: {{ item.address }}
						</li>
						<li>
							Tỉnh/thành: {{ item.city }}
						</li>
						<li>
							Số điện thoại: {{ item.phone }}
						</li>
						<li v-if="item.description">
						Ghi chú: {{ item.description }}
						</li>
						<li>
							Ngày đặt hàng: {{ item.created_at | moment('DD/MM/YYYY HH:mm:ss') }}
						</li>
						<li>
							Thanh toán: {{ item.payment_method.name }}
							<el-tag v-if="item.payment_method.id == 2"> {{ item.charge.status == 'succeeded' ? 'Đã thanh toán': 'Chưa thanh toán' }}</el-tag>
							<el-button 
							size="small" 
							type="primary" 
							v-if="item.payment_method.id == 2 && !(item.charge && item.charge.status == 'succeeded')"
							 @click="showDialogPayment = true; formPayment.orderId = item.id">
								Thanh toán lại
							</el-button>
						</li>
		  			</ul>

		  			<el-table :data="item.items" border>
						<el-table-column
					      label="#" width="50px"
					      >
					      <template slot-scope="scope">
					        <span class="col">{{ scope.$index + 1 }}</span>
					      </template>
					    </el-table-column>

						<el-table-column
						label="Sản phẩm" width="300px"
						>
							<template slot-scope="scope">
						        <span class="text-click col" @click="$router.push({

						        	name: 'product', params: { slug: scope.row.product.slug }

						        })">
						        	{{ scope.row.product.name}} ({{ scope.row.name }})
						        </span>
						      </template>
						</el-table-column>

						<el-table-column
						label="Số lượng"
						>
							<template slot-scope="scope">
					        <span  class="col">{{ scope.row.pivot.quantity }} </span>
					      </template>
						</el-table-column>

						<el-table-column
						label="Giá tiền"
						>
							<template slot-scope="scope">
					        <span class="col">{{ scope.row.pivot.price * (1 - scope.row.pivot.discount) * scope.row.pivot.quantity | currency('đ', 0, { symbolOnLeft: false, spaceBetweenAmountAndSymbol: true })}} </span>
					      </template>
						</el-table-column>

						<el-table-column
						label="Đánh giá"
						type="expand"
						width="80px"
						v-if="_.find(item.status, { name: 'Đã giao' })"
						>
							<template slot-scope="scope">
								<rate-item 
								v-if="_.find(item.rate_list, { product_sku_id: scope.row.id } )"
								:rate="_.find(item.rate_list, { product_sku_id: scope.row.id } )"/>
						        <rate-edit 
						        v-else 
						        :data="{product_sku_id: scope.row.id, order_id: item.id}" 
						        />
						      </template>
						</el-table-column>
					</el-table>
					
					<div class="text-right col">
						<strong v-if="item.discount">Đã giảm: {{ item.discount.value | currency('đ', 0, { symbolOnLeft: false, spaceBetweenAmountAndSymbol: true }) }} (Mã giảm: {{ item.discount.code }})</strong>
						<br/>
						<strong>Tổng giá trị: {{ item.amount | currency('đ', 0, { symbolOnLeft: false, spaceBetweenAmountAndSymbol: true }) }}</strong>
					</div>

					<template v-if="_.last(item.status).id < 4">
						<el-button type="danger" size="small" @click="showDialogCancelOrder=true; formCancel.orderId = item.id">Huỷ đơn hàng</el-button>
					</template>
					

			  	</div>
			  </el-collapse-item>
			</el-collapse>
		</div>

		<div class="panel panel-body text-center" v-else>
			<h5>Bạn không có đơn hàng nào</h5>
			<img style="width: 100%; height: auto" :src="$_storage_getImageFromApp('ORDER_EMPTY')"/>
		</div>

		<el-dialog :visible.sync="showDialogPayment" title="Payment">
			<el-form ref="formPayment" :model="formPayment" :rules="rulesPayment" label-width="140px">
				<el-form-item label="Mã thẻ" prop="card" >
					<el-input size="small" v-model="formPayment.card"></el-input>
				</el-form-item>
				<el-form-item label="Ngày hạn" prop="exp_date">
					<el-date-picker size="small" v-model="formPayment.exp_date" type="date"></el-date-picker>
				</el-form-item>
				<el-form-item label="Mã CVC" prop="cvc">
					<el-input size="small" v-model="formPayment.cvc"></el-input>
				</el-form-item>
				<el-form-item>
					<el-button type="primary" @click="payment" size="small">Thanh toán</el-button>
				</el-form-item>
			</el-form>
		</el-dialog>

		<el-dialog :visible.sync="showDialogCancelOrder" title="Huỷ đơn hàng">
			<el-input placeholder="Lí do huỷ đơn hàng" v-model="formCancel.feedback" type="textarea"></el-input>
			<el-button class="mt-1" type="danger" size="small" @click="cancelOrder">Huỷ đơn hàng</el-button>
		</el-dialog>

	</div>

</template>

<script>
	import moment from 'moment';
	import RateEdit from '../components/RateEdit.vue';
	import RateItem from '../components/RateItem.vue';
	
	export default {
		data(){
			return {
				orders: [],
				activeOrder: [],
				formPayment: {},
				rulesPayment: {
					card: [ { required: true } ],
					exp_date: [ { required: true } ],
					cvc: [ { required: true } ]
				},
				showDialogPayment: false,
				showDialogCancelOrder: false,
				formCancel: {
				},
				loading: false,
				orderStatus: []
			}
		},
		created(){

			this.getData();
			
		},
		computed: {
			user()
			{
				return this.$store.state.user;
			}
		},
		methods: {
			getData(){

				this.loading = true;
				this.axios.get(this.api.user.get() + '/orders').then((res)=>{
					this.orders = res.data.orders;
					this.orderStatus = res.data.meta.order_status;
					this.loading = false;
				});
			},
			payment()
			{
				this.$refs.formPayment.validate(valid => {

					if(valid)
					{
						let data = Object.assign({}, this.formPayment);
						data.exp_date = moment(data.exp_date).format('YYYY-MM-DD');
						this.axios.post(this.api.orders.get() + '/' + this.formPayment.orderId + '/payment', data)
						.then((res) => {

							this.$notify({
								type:'success',
								message: 'Đã thanh toán thành công',
								title: 'Thông báo'
							});

							this.$refs.formPayment.resetFields();
							this.showDialogPayment = false;
						})
					}

				})
			},
			cancelOrder()
			{
				let { orderId, feedback } = this.formCancel;

				this.axios.patch(this.api.orders.get() + '/' + orderId + '/update-status', { status :this._.last(this.orderStatus).id, feedback })
				{
					this.showDialogCancelOrder = false;
					this.formCancel = {};
					this.getData();
				}
			}
		},

		components: { RateItem, RateEdit }
	}
</script>

<style scoped>
</style>