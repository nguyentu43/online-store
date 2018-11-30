<template>
	<div class="panel">
		<el-row>
			<div class="panel-heading">
				<el-steps :active="step" direction="horizontal">
				  <el-step title="1" description="Điền thông tin"></el-step>
				  <el-step title="2" description="Kiểm tra đơn hàng"></el-step>
				  <el-step title="3" description="Hoàn tất"></el-step>
				</el-steps>
			</div>
			<template v-if="step==1">
				<div class="panel-body border-top">
					<el-form ref="formInfo" :model="formInfo" label-width="120px" :rules="rules">
						<div class="panel-heading">
							<h5><i class="el-icon-edit"></i> Điền thông tin người nhận</h5>
						</div>
						<el-row>
							<el-col :sm="18" :xs="24">
								<div class="panel-body">
									<el-form-item label="Họ và tên" prop="name">
										<el-input v-model="formInfo.name" size="small"></el-input>
									</el-form-item>
									<el-form-item label="Địa chỉ" prop="address">
										<el-input v-model="formInfo.address" size="small"></el-input>
									</el-form-item>
									<el-form-item label="Tỉnh/thành phố" prop="city">
										<el-select 
										v-model="formInfo.city"
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
									<el-form-item label="Số điện thoại" prop="phone">
										<el-input v-model="formInfo.phone" size="small"></el-input>
									</el-form-item>

									<el-form-item label="Lưu địa chỉ">
										<el-checkbox v-model="saveAddress" size="small"></el-checkbox>
									</el-form-item>
								</div>
							</el-col>
						</el-row>

						<div class="panel-heading">
							<h5><font-awesome-icon icon="money-bill" /> Phương thức thanh toán</h5>
						</div>

						<el-form-item prop="payment_method_id">
							<el-radio 
							v-model="formInfo.payment_method_id" 
							v-for="item in payment_methods"
							:label="item.id"
							:key="item.id"
							>
								{{ item.name }}
							</el-radio>
						</el-form-item>
					</el-form>
				
					<el-form 
					v-if="formInfo.payment_method_id && formInfo.payment_method_id == 2"
					ref="formCard" 
					:model="formCard"
					:rules="formCardRules"
					label-width="100px"
					>
						<el-row>
							<el-col :sm="18" :xs="24">
								<div class="panel-body">
									<el-form-item label="Mã thẻ" prop="card">
									<el-input size="small" v-model="formCard.card"></el-input>
									</el-form-item>
									<el-form-item label="Ngày hạn" prop="exp_date">
										<el-date-picker size="small" format="yyyy-MM" type="month" v-model="formCard.exp_date"></el-date-picker>
									</el-form-item>
									<el-form-item label="Mã CVC" prop="cvc">
										<el-input size="small" v-model="formCard.cvc"></el-input>
									</el-form-item>
								</div>
							</el-col>
						</el-row>
					</el-form>
					
				</div>

				<div class="panel-footer">
					<el-button-group>
						<el-button @click="dialogAddress=true" size="small"><font-awesome-icon icon="address-book"/> Chọn địa chỉ từ sổ</el-button>
						<el-button icon="el-icon-arrow-right" type="primary" size="small" @click="nextStep(2)">Tiếp theo</el-button>
					</el-button-group>
				</div>
						
			</template >
				<template v-if="step > 1">

					<template v-if="step == 3">

						<div class="panel-heading">
							<h5><font-awesome-icon icon="address-card"/> Thông tin người nhận</h5>
						</div>

						<div class="panel-body">
							<ul>
								<li>
									Họ và tên: {{ formInfo.name }}
								</li>
								<li>
									Địa chỉ: {{ formInfo.address }}
								</li>
								<li>
									Tỉnh/thành: {{ formInfo.city }}
								</li>
								<li>
									Số điện thoại: {{ formInfo.phone }}
								</li>
								<li>
									Thanh toán: {{ get_payment_name(formInfo.payment_method_id) }}
								</li>
								<li class="mb-1" v-if="formInfo.description">
									Ghi chú: {{ formInfo.description }}
								</li>
							</ul>
						</div>
					</template>

					<div class="panel-heading">
							<h5><i class="el-icon-tickets"></i> Thông tin đơn hàng</h5>
						</div>

					<div class="panel-body">
						<el-table :data="order.items">
							<el-table-column
						      label="#" width="50px"
						      >
						      <template slot-scope="scope">
						        <span>{{ scope.$index + 1 }}</span>
						      </template>
						    </el-table-column>

							<el-table-column
							label="Sản phẩm"
							>
								<template slot-scope="scope">
						        <span class="text-click" @click="$router.push({

						        	name: 'product', params: { slug: scope.row.product.slug }

						        })">
						        	{{ scope.row.product.name}} ({{ scope.row.sku.name }})
						        </span>
						      </template>
							</el-table-column>

							<el-table-column
							label="Số lượng"
							>
								<template slot-scope="scope">
						        <span>{{ scope.row.cart.quantity }} </span>
						      </template>
							</el-table-column>

							<el-table-column
							label="Giá tiền"
							>
								<template slot-scope="scope">
						        <span>{{ scope.row.price * (1 - scope.row.discount) * scope.row.cart.quantity | currency('đ', 0, { symbolOnLeft: false, spaceBetweenAmountAndSymbol: true })}} </span>
						      </template>
							</el-table-column>

						</el-table>

						<div class="text-right mt-1">
							<el-input style="width: 25%" size="small" v-model="discount.code" placeholder="Nhập mã giảm giá">
								<el-button @click="check_discount_code" slot="append">Áp dụng</el-button>
							</el-input>
						</div>
						<div class="text-right mt-1">
							<strong v-if="discount.value">Giảm giá: {{ discount.value | currency('đ', 0, { symbolOnLeft: false, spaceBetweenAmountAndSymbol: true}) }}</strong>
							<strong class="text-danger" v-if="discount.invalid">Mã giảm giá không hợp lệ hoặc đơn hàng không đủ điều kiện</strong>
							<div v-if="discount.value > 0"><del>{{ order.amount | currency('đ', 0, { symbolOnLeft: false, spaceBetweenAmountAndSymbol: true }) }}</del></div>
						</div>

						<h5 class="text-right"> Thành tiền: {{ (!discount.invalid ? order.amount - discount.value : order.amount)  | currency('đ', 0, { symbolOnLeft: false, spaceBetweenAmountAndSymbol: true }) }} </h5>

						<el-input
							type="textarea"
							:rows="2"
							placeholder="Nhập thêm yêu cầu đơn hàng"
							v-model="formInfo.description"
							class="mb-1"
							v-if="step==2"
							>
						</el-input>
					</div>
					
					<template v-if="step==2">
						<div class="panel-footer">
							<el-button-group>
								<el-button size="small" icon="el-icon-arrow-left" @click="prevStep(1)">Trước</el-button>
								<el-button size="small" icon="el-icon-arrow-right" type="primary" @click="nextStep(3)">Tiếp theo</el-button>
							</el-button-group>
						</div>
					</template>
					<template v-else>
						<div class="panel-footer">
							<el-button-group>
								<el-button size="small" icon="el-icon-arrow-left" @click="prevStep(2)">Trước</el-button>
								<el-button size="small" icon="el-icon-check" type="success" :loading="loadButton" @click="checkout">Đặt hàng</el-button>
							</el-button-group>
						</div>
					</template>
				</template>
		</el-row>

		<el-dialog :visible.sync="dialogAddress" title="Sổ địa chỉ">
			<el-table :data="user.address_books" v-if="user">
				<el-table-column
			      label="#" width="50px"
			      >
			      <template slot-scope="scope">
			        <span>{{ scope.$index + 1 }}</span>
			      </template>
			    </el-table-column>
				<el-table-column
			      label="Họ và tên"
			      >
			      <template slot-scope="scope">
			        <span>{{ scope.row.name }}</span>
			      </template>
			    </el-table-column>
			    <el-table-column
			      label="Địa chỉ"
			      >
			      <template slot-scope="scope">
			        <span>{{ scope.row.address + ' ' + scope.row.city }}</span>
			      </template>
			    </el-table-column>
			    <el-table-column
			      label="Số ĐT"
			      >
			      <template slot-scope="scope">
			        <span>{{ scope.row.phone }}</span>
			      </template>
			    </el-table-column>
			    <el-table-column
			      label="Chọn"
			      >
			      <template slot-scope="scope">
				     <el-button type="success" icon="el-icon-check" size="small" @click="selectAddress(scope.$index)">Chọn</el-button>
			      </template>
			    </el-table-column>
			</el-table>
		</el-dialog>

	</div>
</template>

<script>

    import CartMixin from '../mixins/cart.js'
    import provide from '../data/tinh_tp.json'

	export default {
		mixins: [CartMixin],
		data(){
			return {
				step: 1,
				formInfo: {
					name: 'example',
					address: '123',
					city: 'hcm',
					phone: 123456
				},
				discount: {
					code: '',
					value: null,
					invalid: null
				},
				formCard: {
					card: '4242424242424242',
					exp_date: '2020-10',
					cvc: '314'
				},
				payment_methods: [],
				order: null,
				rules: {
					name: [
						{ required: true, message: 'Họ và tên là bắt buộc', trigger: 'blur' },
						{ min: 6, message: 'Họ và tên phải dài hơn 6 kí tự', trigger: 'change' }
					],
					address: [
						{ required: true, message: 'Địa chỉ là bắt buộc', trigger: 'blur' }
					],
					city: [
						{ required: true, message: 'Tỉnh/thành phố là bắt buộc', trigger: 'blur' }
					],
					phone: [
						{ required: true, message: 'Số điện thoại là bắt buộc', trigger: 'blur' },
						{ validator: (rule, value, callback) => {
								if(/^[0-9]+$/g.test(value))
								{
									callback();
								}
								else
								{
									callback(new Error('Phải là số'));
								}
							}
						}
					],
					payment_method_id: [{ required: true, message: 'Phương thức thanh toán là bắt buộc', trigger: 'blur'}]
				},
				formCardRules: {
					card: [ { required: true, message: 'Mã thẻ là bắt buộc' } ],
					exp_date: [ { required: true, message: 'Ngày hạn thẻ là bắt buộc' } ],
					cvc: [ { required: true, message: 'Mã CVC là bắt buộc' } ],
				},
				saveAddress: false,
				dialogAddress: false,
				loadButton: false
			}
		},
		computed: {
			user(){
				return this.$store.state.user;
			},
			provide(){
				return provide;
			}
		},
		methods: {
			nextStep(step){

				if(step == 2)
				{
					this.$refs.formInfo.validate((valid) => {

						if(this.formInfo.payment_method_id == 2)
						{
							this.$refs.formCard.validate((valid2) => {
								if(valid2)
								{
									this.step = step;
								}
							});
						}
						else
						{
							if(valid)
							{
								this.step = step;
							}
						}
					})
				}
				else if(step == 3)
				{
					this.step = step;
				}

			},
			prevStep(step){
				this.step = step;
			},
			selectAddress(index){
				let {name, address, city, phone} = this.user.address_books[index];
				this.formInfo = {
					name,
					address,
					city,
					phone
				}
				this.dialogAddress = false;
			},
			checkout(){

				let data = { ...this.formInfo }
				data.card = this.formCard;

				this.loadButton = true;

				this.axios.post(this.api.user.get() + '/orders/', data).then(res => {

					if(this.saveAddress)
					{
						return this.axios.post(this.api.user.get() + '/addressbooks/', this.formInfo)
						.then((res) => {

							this.$notify({
								title: 'Thông báo',
								message: 'Đã thêm thành công địa chỉ',
								type: 'success'
							})

							this.user.address_books.push(res.data.data);
						})
					}

				}).then(()=>{

					this.$store.dispatch('setDataCart', []);

					this.$notify({
						title: 'Thông báo',
						message: 'Bạn đã đặt hàng thành công', 
						type: 'success'})

					this.loadButton = false;

					this.$router.push({path: '/'})

				})
			},
			initItems(cart){

				this.$_cart_getProducts(cart).then(data => {
					this.order = data;
				});

				this.axios.get(this.api.payment.get()).then((res) => this.payment_methods = res.data);
			},
			get_payment_name(value)
			{
				return _.find(this.payment_methods, { id: parseInt(value) }).name;
			},
			check_discount_code()
			{
				this.axios.post(this.api.discount_code.check(), { code: this.discount.code, items: this.order.items, user_id: this.user.id }).then(res => {

						if(res.data.message)
						{
							this.discount.invalid = true;
							this.discount.value = null;
							this.formInfo.discount = null;
							return;
						}

						this.formInfo.discount = {
							code: this.discount.code,
							value: res.data.discount
						};

						this.discount.invalid = false;
						this.discount.value = res.data.discount;
				});
			}
		},
		watch:{
			cart(cart){
				this.initItems(cart);
			}
		},
		created(){
			this.initItems(this.cart);
		},
		beforeRouteEnter(to, from, next){

			if(JSON.parse(localStorage.getItem('cart')).length > 0)
				next()
			else
				next('/')

		}
	}
</script>

<style scoped>

</style>