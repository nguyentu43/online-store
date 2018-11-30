<template>
	<div class="panel">
		<div class="panel-heading">
			<h5>Quản lí đơn hàng</h5>
		</div>

		<div class="panel-body">
			<el-button
			type="primary"
			icon="el-icon-circle-plus-outline"
			size="small"
			class="mb-1"
			@click="openDialog()"
			>
				Thêm đơn hàng
			</el-button>

			<el-row :gutter="2" class="mb-1">
				<el-col :sm="6">
					<el-input
					  v-model.trim="searchOrderCode"
					  placeholder="Mã đơn hàng?"
					  :clearable="true"
					  @clear="changeQuery"
					  size="small"
					>
						<el-button slot="append" @click="changeQuery">
							<i class="el-icon-search"></i>
						</el-button>
					</el-input>
				</el-col>

				<el-col :sm="18">
					<el-select
					v-model="status_code_filter"
					@change="changeQuery()"
					placeholder="Lọc tình trạng"
					:clearable="true"
					@clear="changeQuery"
					size="small"
					>
						<el-option
						v-for="item in order_status"
						:key="item.id"
						:value="item.id"
						:label="item.name"
						>
						</el-option>
					</el-select>

					<el-date-picker
				      v-model="dateRange"
				      type="daterange"
				      range-separator="To"
				      start-placeholder="Ngày bắt đầu"
				      @change="changeQuery()"
				      value-format="yyyy-MM-dd"
				      size="small"
				      end-placeholder="Kết thúc">
				    </el-date-picker>
				</el-col>
			</el-row>

			<el-table
			:data="orderData"
			v-loading="loading"
			border
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
				label="Chi tiết"
				type="expand"
				width="80px"
				>
					<template slot-scope="scope">
						<el-steps :active="_.last(scope.row.status).id > 5 ? 5 : _.last(scope.row.status).id" align-center>
							<el-step 
							v-for="status in scope.row.status" 
							:title="status.name" 
							:description="status.pivot.updated_at"
							:key="status.id">
							</el-step>
							<template v-if="_.last(scope.row.status).id < 4">
								<el-step
								v-for="status in _.range(_.last(scope.row.status).id + 1, 5)"
								:title="_.find(order_status, {id: status}).name"
								:key="status">
								</el-step>
							</template>
						</el-steps>

						<div class="box">
							<h5>Mã đơn hàng: {{scope.row.order_code}}</h5>
							<div><strong>Thông tin người mua hàng: </strong></div>
							<ul>
								<li>Người mua: {{scope.row.user.name}}</li>
								<li>Địa chỉ email: {{scope.row.user.email}}</li>
							</ul>
							
							<div><strong>Thông tin giao hàng: </strong></div>
							<ul>
								<li>Người nhận: {{scope.row.name}}</li>
								<li>Địa chỉ: {{scope.row.address + ' ' + scope.row.city}}</li>
								<li>Số điện thoại: {{scope.row.phone}}</li>
								<li>Thanh toán: {{scope.row.payment_method.name}}
									<el-tag v-if="scope.row.payment_method.name == 'VISA'"> {{ scope.row.charge.status == 'succeeded' ? 'Đã thanh toán': 'Chưa thanh toán' }}</el-tag>
								</li>
								<li>Ghi chú: {{scope.row.description || 'Không'}}</li>
							</ul>
							
							<el-table
							:data="scope.row.items"
							border
							>
								<el-table-column
									label="#"
									width="80px"
									>
									<template slot-scope="scope2">
										<span class="col">{{ scope2.$index + 1 }}</span>
									</template>
								</el-table-column>

								<el-table-column
									label="Tên sản phẩm"
									width="300px"
									>
									<template slot-scope="scope2">
										<span class="col">{{ scope2.row.product.name }}</span>
									</template>
								</el-table-column>

								<el-table-column
									label="Phân loại"
									>
									<template slot-scope="scope2">
										<span class="col">{{ scope2.row.name }}</span>
									</template>
								</el-table-column>

								<el-table-column
									label="Số lượng"
									>
									<template slot-scope="scope2">
										<span class="col">{{ scope2.row.pivot.quantity }}</span>
									</template>
								</el-table-column>

								<el-table-column
									label="Giảm giá"
									>
									<template slot-scope="scope2">
										<span class="col">{{ scope2.row.pivot.quantity * scope2.row.pivot.price *scope2.row.pivot.discount | currency('đ', 0, { symbolOnLeft: false, spaceBetweenAmountAndSymbol: true }) }}</span>
										<el-tag type="danger">{{scope2.row.pivot.discount*100}}%</el-tag>
									</template>
								</el-table-column>

								<el-table-column
									label="Tổng"
									>
									<template slot-scope="scope2">
										<span class="col">{{ scope2.row.pivot.quantity * scope2.row.pivot.price * ( 1 - scope2.row.pivot.discount) | currency('đ', 0, { symbolOnLeft: false, spaceBetweenAmountAndSymbol: true }) }}</span>
									</template>
								</el-table-column>
							</el-table>

							<div class="text-right">
								<strong v-if="scope.row.discount.code"> Đã giảm: {{ scope.row.discount.value | currency('đ', 0, { symbolOnLeft: false, spaceBetweenAmountAndSymbol: true }) }} (Mã giảm: {{scope.row.discount.code }})</strong><br/>
								<strong>Tổng giá trị: {{scope.row.amount | currency('đ', 0, { symbolOnLeft: false, spaceBetweenAmountAndSymbol: true })}}</strong>
							</div>

						</div>
					</template>
				</el-table-column>

				<el-table-column
				label="Tình trạng"
				>
					<template slot-scope="scope">
						<el-select
						v-model="scope.row.lastStatus"
						@change="updateOrderStatus(scope.row)"
						size="small"
						>
							<el-option
							v-for="item in order_status"
							:key="item.id"
							:value="item.id"
							:label="item.name"
							>
							</el-option>
						</el-select>
					</template>
				</el-table-column>

				<el-table-column
				label="Thao tác"
				>
					<template slot-scope="scope">
						<span class="col">
							<el-button
							type="success" 
							icon="el-icon-edit"
							size="small"
							@click="openDialog(scope.row)"
							>Sửa</el-button>
							<el-button 
							type="danger" 
							icon="el-icon-delete"
							size="small"
							@click="deleteOrder(scope.row.id)"
							>Xoá</el-button>
						</span>
					</template>
				</el-table-column>
			</el-table>

			<div class="text-right">
				<el-pagination
				  v-if="pagination.total > 0"
				  background
				  layout="prev, pager, next"
				  :total="pagination.total"
				  @current-change="changeQuery"
				  @prev-click="changeQuery"
				  @next-click="changeQuery"
				  :current-page="pagination.page"
				  :page-size="pagination.pageSize"
				  >
				</el-pagination>
			</div>

			<el-dialog
			:visible.sync="showDialog"
			@close="$refs.formOrder.resetFields()"
			>
				<el-form
				ref="formOrder"
				:model="formOrder"
				:rules="formOrderRules"
				label-width="150px"
				>
					<el-form-item prop="user_id" label="Người mua hàng">
						<el-select
						v-model="formOrder.user_id"
						filterable
						placeholder="Chọn người mua hàng" 
						size="small"
						>
							<el-option
							:key="item.id"
							:value="item.id"
							:label="item.email + ' - ' + item.name"
							v-for="item in customerData"
							></el-option>
						</el-select>
					</el-form-item>

					<el-form-item prop="name" label="Họ tên người nhận">
						<el-input v-model="formOrder.name" size="small">
						</el-input>
					</el-form-item>

					<el-form-item prop="address" label="Địa chỉ người nhận">
						<el-input v-model="formOrder.address" size="small">
						</el-input>
					</el-form-item>

					<el-form-item prop="city" label="Tỉnh/thành phố">
						<el-select 
						v-model="formOrder.city"
						filterable
						size="small"
						>
							<el-option
							v-for="item in provide"
							:label="item.name_with_type"
							:value="item.name_with_type"
							:key="item.code"
							>
							</el-option>
						</el-select>
					</el-form-item>

					<el-form-item prop="phone" label="Số điện thoại">
						<el-input v-model="formOrder.phone" size="small">
						</el-input>
					</el-form-item>

					<el-form-item label="Thanh toán" prop="payment_method_id">
						<el-radio 
						v-model="formOrder.payment_method_id" 
						v-for="item in payment_methods"
						:label="item.id"
						:key="item.id"
						>
							{{ item.name }}
						</el-radio>
					</el-form-item>

					<el-form-item prop="description" label="Ghi chú">
						<el-input type="textarea" v-model="formOrder.description" autosize>
						</el-input>
					</el-form-item>

					<el-form-item prop="description" label="Mã giảm giá">
						<el-input size="small" v-model="formOrder.discount.code">
						</el-input>
					</el-form-item>

					<el-form-item label="Tìm sản phẩm">
						<el-autocomplete
						v-model="queryString"
						:fetch-suggestions="querySearchAsync"
						placeholder="Nhập tên sản phẩm"
						@select="addProduct"
						style="width: 50%"
						size="small"
						></el-autocomplete>
					</el-form-item>

					<el-table
					:data="formOrder.items"
					border
					>
						<el-table-column
						label="#"
						width="80px"
						>
							<template slot-scope="scope">
								<span class="col">{{ scope.$index + 1}}</span>
							</template>
						</el-table-column>

						<el-table-column
						label="Sản phẩm"
						width="350px"
						>
							<template slot-scope="scope">
								<div class="col">
									<strong>{{ scope.row.product.name }}</strong><br/>
									<strong>Phân loại: </strong>{{ scope.row.name }}<br/>
									<strong>SKU: </strong>{{ scope.row.sku }}
								</div>
							</template>
						</el-table-column>

						<el-table-column
						label="Số lượng"
						>
							<template slot-scope="scope">
								<el-input-number size="small" v-model="scope.row.pivot.quantity"></el-input-number>
							</template>
						</el-table-column>

						<el-table-column
						label="Xoá"
						>
							<template slot-scope="scope">
								<el-button type="danger" size="small" icon="el-icon-delete" @click="deleteProduct(scope.$index)"></el-button>
							</template>
						</el-table-column>
					</el-table>

					<el-button class="mt-1" type="success" size="small" @click="saveOrder">Lưu</el-button>

				</el-form>
			</el-dialog>
		</div>
	</div>
</template>

<script>
	
	import provide from '../../data/tinh_tp.json'

	export default{
		data(){
			return {
				orderData: [],
				formOrder: { items: [], discount: {} },
				formOrderRules: {
					user_id: [{ required: true, trigger: 'blur' }],
					name: [{required: true, trigger: 'blur'}],
					address: [{required: true, trigger: 'blur'}],
					city: [{required: true, trigger: 'blur'}],
					phone: [{required: true, trigger: 'blur'}, { validator: (rule, value, callback) => {
						if(/^[0-9]+$/g.test(value))
						{
							callback();
						}
						else
						{
							callback(new Error('Phải là số'));
						}
					}  }],
					payment_method_id: [{required: true, trigger: 'blur'}]
				},
				pagination: {
					page: 1,
					total: 0,
					perPage: 16
				},
				searchOrderCode: '',
				customerData: [],
				showDialog: false,
				queryString: '',
				payment_methods: [],
				order_status: [],
				loading: false,
				status_code_filter: null,
				dateRange: null
			}
		},
		beforeRouteUpdate(to, from, next)
		{
			this.getData();
			next();
		},
		methods:{
			changeQuery(page = 1)
			{
				this.pagination.page = page;
				let query = Object.assign({}, this.$route.query);
				query.page = page;
				query.status_code_filter = this.status_code_filter;
				query.dateRange = this.dateRange;
				query.order_code = this.searchOrderCode;
				this.$router.replace({ name: 'order-admin', query})
			},
			getData(){

				this.loading = true;
				this.axios.get(this.api.orders.get(), { params: { page: this.pagination.page, per_page: this.pagination.perPage, order_code: this.searchOrderCode, status_code: this.status_code_filter, date_range: this.dateRange} }).then(res=>{

					this.orderData = res.data.orders;

					this.order_status = res.data.meta.order_status;

					this._.each(this.orderData, item=>{
						item.user_id = item.user.id;
						item.payment_method_id = item.payment_method.id;
						item.lastStatus = this._.last(item.status).id;

						if(!item.discount)
							item.discount = {};
					});

					this.pagination.total = res.data.meta.total;

					this.loading = false;
				})

				this.axios.get(this.api.users.get(), { params: { type: 'customer' } })
				.then(res=>{
					this.customerData = res.data;
				})

				this.axios.get(this.api.payment.get()).then((res) => this.payment_methods = res.data);

			},
			openDialog(data = { items: [], discount: {} })
			{
				if(!this.$_casl_open_dialog('order', data)) return;

				this.formOrder = Object.assign({}, data);
				this.showDialog = true;
			},
			addProduct(item)
			{
				this.queryString = '';
				if(this.formOrder.items)
				{
					if(this._.find(this.formOrder.items, { id: item.id }))
					{
						this.$notify({
							type: 'warning',
							message: 'Sản phẩm này đã có trong danh sách',
							title: 'Thông báo'
						})
					}
					else
						this.formOrder.items.push(item);
				}
				else
				{
					this.formOrder.items = [item];
				}
			},
			querySearchAsync(queryString, cb)
			{
				if(queryString.trim().length < 4) {
					cb([]);
					return;
				}

				this.axios.get(this.api.products.get(), { params: { keyword: queryString } })
				.then(res => {

					let items = [];

					this._.each(res.data.products, p => {

						this._.each(p.skus, sku=>{

							items.push({
								value: `${p.name} (${sku.name})`,
								product: { id: p.id, name: p.name },
								id: sku.id,
								name: sku.name,
								sku: sku.sku,
								quantity: sku.quantity,
								pivot: { quantity: 1}
							})
						})
					})

					cb(items);
				})
			},
			saveOrder()
			{
				this.$refs.formOrder.validate(valid=>{
					if(!valid) return;

					if(this.formOrder.items && this.formOrder.items.length > 0)
					{
						if(this.formOrder.id)
						{
							this.updateOrder();
						}
						else
						{
							this.addOrder();
						}
					}
					else
					{
						this.$notify({
							type:'warning',
							message: 'Bạn chưa chọn sản phẩm',
							title: 'Thông báo'
						});
					}
				})
			},
			updateOrder()
			{
				this.axios.put(this.api.orders.get() + '/' + this.formOrder.id, this.formOrder)
				.then(res=>{
					this.$notify({
						type:'success',
						message: 'Cập nhật đơn hàng #' + res.data.id + ' thành công' ,
						title: 'Thông báo'
					});

					this.showDialog = false;
					this.getData();
				})
			},
			updateOrderStatus(order)
			{
				if(!this.$_casl_check('order', 'update')) return;

				this.axios.patch(this.api.orders.get() + '/' + order.id + '/update-status', { status: order.lastStatus })
				.then(res=>{

					this.$notify({
						type:'success',
						message: 'Cập nhật đơn hàng #' + order.order_code + ' thành công' ,
						title: 'Thông báo'
					});

					this.getData();
				})
			},
			addOrder()
			{
				this.axios.post(this.api.orders.get(), this.formOrder)
				.then(res=>{
					this.$notify({
						type:'success',
						message: 'Đã tạo đơn hàng #' + res.data.order_code + ' thành công' ,
						title: 'Thông báo'
					});

					this.showDialog = false;
					this.getData();
				})
			},
			deleteOrder(id)
			{
				this.$confirm('Bạn có muốn xoá?', 'Xoá', {
					confirmButtonText: 'Có',
					cancelButtonText: 'Không',
					type: 'warning'
				}).then(()=> {
					this.axios.delete(this.api.orders.get() + '/' + id)
					.then(res => {

							this.$notify({
								type:'success',
								message: 'Đã xoá đơn hàng thành công' ,
								title: 'Thông báo'
							});

							this.getData();
					})
				})
				
			},
			deleteProduct(id)
			{
				if(!this.$_casl_check('order', 'delete')) return;

				this.$confirm('Bạn có muốn xoá?', 'Xoá', {
					confirmButtonText: 'Có',
					cancelButtonText: 'Không',
					type: 'warning'
				}).then(() => this.formOrder.items.splice(id, 1));
			}
		},
		computed: {
			provide(){
				return provide;
			}
		},
		created(){
			this.pagination.page = parseInt(this.$route.query.page) || 1;
			this.searchOrderCode = this.$route.query.order_code || '';
			this.status_code_filter = this.$route.query.status_code_filter || '';
			this.getData();
		}
	}

</script>

<style>

	.box{
		font-size: 16px;
		color: black;
		line-height: 30px;
		padding: 10px;
	}

	.el-dialog{
		width: 60%
	}

</style>