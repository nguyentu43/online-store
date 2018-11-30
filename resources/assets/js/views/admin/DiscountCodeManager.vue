<template>
	
	<div class="panel">
		<div class="panel-heading">
			<h5>Quản lí mã giảm giá</h5>
		</div>

		<div class="panel-body">
				<div class="mb-1">
					<el-button @click="openDialog()" size="small" icon="el-icon-circle-plus-outline" type="primary">Thêm mới</el-button>
				</div>
				<el-table
				:data="discountCodeData"
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
					label="Mã giảm giá"
					>
						<template slot-scope="scope">
							<span class="col">
								{{ scope.row.code }}
							</span>
						</template>
					</el-table-column>

					<el-table-column
					label="Số lượng"
					>
						<template slot-scope="scope">
							<span class="col">
								{{ scope.row.count }}
							</span>
						</template>
					</el-table-column>

					<el-table-column
					label="Thời hạn"
					>
						<template slot-scope="scope">
							<div class="col">
								<span v-if="scope.row.remain"> {{ scope.row.remain.content }}</span>
								<span v-else> Không có </span>
							</div>
						</template>
					</el-table-column>

					<el-table-column
					label="Mô tả"
					type="expand"
					width="80px"
					>
						<template slot-scope="scope">
							<ul>
								<li>Loại: {{ scope.row.value.type == 0 ? 'Phần trăm' : 'Giá tiền' }}</li>
								<li>Giá trị giảm: {{ scope.row.value.discount }}</li>
								<li v-if="scope.row.remain">
									Thời hạn: {{ scope.row.begin_date }} - {{ scope.row.end_date }}
								</li>
							</ul>
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
								@click="deleteDiscount(scope.row.id)"
								>Xoá</el-button>
							</span>
						</template>
					</el-table-column>
				</el-table>

				<el-dialog 
				class="dialog-lg" 
				:visible.sync="showDialog" 
				title="Mã giảm giá"
				:fullscreen="true"
				@before-close="$refs.formDiscountCode.resetFields()"
				>
						
				<el-form v-if="showDialog" ref="formDiscountCode" :model="formDiscountCode" label-width="180px" :rules="rules">
					<el-form-item prop="code" label="Mã giảm giá">
						<el-input size="small" v-model="formDiscountCode.code" />
					</el-form-item>
					<el-form-item label="Số lượng" prop="count">
						<el-input-number :min="1" size="small" v-model="formDiscountCode.count"></el-input-number>
					</el-form-item>
					<el-form-item label="Loại" prop="value.type" >
						<el-select 
						v-model="formDiscountCode.value.type" 
						@change="changeType"
						size="small">
							<el-option :value="0" label="Phần trăm"></el-option>
							<el-option :value="1" label="Giá tiền"></el-option>
						</el-select>
					</el-form-item>
					<el-form-item label="Giá trị giảm" prop="value.discount">
						<el-input-number :min="0.1" size="small" v-model="formDiscountCode.value.discount"></el-input-number>
					</el-form-item>
					<el-form-item label="Giá trị giảm tối đa" v-if="!formDiscountCode.value.type">
						<el-input-number :min="100" size="small" v-model="formDiscountCode.value.max"></el-input-number>
					</el-form-item>
					<el-form-item label="Thời hạn" prop="rangeDate">
						<el-date-picker
					      v-model="formDiscountCode.rangeDate"
					      type="datetimerange"
					      start-placeholder="Bắt đầu"
					      end-placeholder="Kết thúc"
					      >
					    </el-date-picker>
					</el-form-item>
					<el-form-item label="Khách hàng">
						<el-select
						v-model="formDiscountCode.value.users"
						filterable
						placeholder="Chọn khách hàng"
						multiple 
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
					<el-form-item label="Danh mục">
						<el-select
						v-model="formDiscountCode.value.categories"
						filterable
						placeholder="Chọn danh mục"
						multiple 
						size="small"
						>
							<el-option v-for="item in categoryData" :key="item.id" :value="item.id" :label="item.name"></el-option>
						</el-select>
					</el-form-item>
					<el-form-item label="Nhãn hiệu">
						<el-select
						v-model="formDiscountCode.value.brand"
						filterable
						placeholder="Chọn nhãn hiệu"
						multiple 
						size="small"
						>
							<el-option v-for="item in brandData" :key="item.id" :value="item.id" :label="item.name"></el-option>
						</el-select>
					</el-form-item>
					<el-form-item label="Loại sản phẩm">
						<el-select
						v-model="formDiscountCode.value.ptype"
						filterable
						placeholder="Chọn loại sản phẩm"
						multiple 
						size="small"
						>
							<el-option v-for="item in pTypeData" :key="item.id" :value="item.id" :label="item.name"></el-option>
						</el-select>
					</el-form-item>
					<el-form-item label="Chứa các từ khoá">
						<el-input size="small" v-model="formDiscountCode.value.tags"/>
					</el-form-item>
					<el-form-item label="Tìm sản phẩm">
						<el-autocomplete
						v-model="queryString"
						:fetch-suggestions="querySearchAsync"
						placeholder="Nhập tên sản phẩm"
						@select="addProduct"
						style="width: 50%"
						size="small"
						value-key="name"
						></el-autocomplete>
					</el-form-item>
					<el-form-item label="Sản phẩm" prop="products">
						<el-table
						:data="formDiscountCode.value.products"
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
							label="Tên sản phẩm"
							>
								<template slot-scope="scope">
									<span class="col">{{ scope.row.name }}</span>
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
					</el-form-item>
					<el-form-item label="Giá trị đơn hàng tối thiểu">
						<el-input-number :min="0" size="small" v-model="formDiscountCode.value.minOrder"></el-input-number>
					</el-form-item>
					<el-form-item>
						<el-button size="small" type="success" icon="el-icon-check" native-type="submit" @click.prevent="saveDiscount">Lưu</el-button>
					</el-form-item>
				</el-form>

			</el-dialog>
		</div>

	</div>

</template>

<script>

	import TimeMixin from '../../mixins/time.js';
	import moment from 'moment';

	export default{
		mixins: [ TimeMixin ],
		data(){
			return {
				discountCodeData: [],
				customerData: [],
				categoryData: [],
				brandData: [],
				pTypeData: [],
				queryString: '',
				loading: false,
				rules:{
					code: [
							{ required: true, message: 'Mã giảm giá là bắt buộc', trigger: 'blur' },
							{ validator: (rule, value, callback) => {

								if(value == "")
								{
									callback();
									return;
								}

	                            this.axios.get(this.api.discount_code.get(), { params: { code: value } }).then((res) => {

	                            	if(res.data.exists)
	                                	callback(new Error('Mã này đã đăng ký rồi'));
	                                else
	                                	callback();
	                                return;

                            })}, trigger: 'change'}
					],
					count: [ { required: true } ],
					"value.type": [ { required: true } ],
					"value.discount": [ { required: true } ]
				},
				formDiscountCode: {
					value: {
					}
				},
				showDialog: false
			}
		},
		methods:{
			openDialog(data = { value: {  } })
			{
				if(!this.$_casl_open_dialog('discount-code', data)) return;

				this.showDialog = true;
				this.formDiscountCode = Object.assign({}, data);
			},
			changeType(val)
			{
				if(val)
				{
					this.formDiscountCode.value.max = null;
					this.formDiscountCode.value.discount = 10000;
				}
				else
				{
					this.formDiscountCode.value.max = 10000;
					this.formDiscountCode.value.discount = 0.1;
				}
			},
			getData(){

				this.loading = true;
				this.axios.get(this.api.discount_code.get()).then((res)=>{
					this.discountCodeData = res.data;

					this.discountCodeData.forEach(item => {

						if(item.begin_date)
						{
							item.remain = this.$_time_calcDay(item.begin_date, item.end_date);
							item.rangeDate = [ item.begin_date, item.end_date ];
						}
					});

					this.loading = false;
				})

			},
			addDiscount(data){

				return this.axios.post(this.api.discount_code.get(), data)
				.then((res) => {

					this.$notify({
						title: 'Thông báo',
						message: 'Đã thêm thành công ',
						type: 'success'
					})

					this.getData();
				})
			},
			updateDiscount(data){

				return this.axios.put(this.api.discount_code.get() + '/' + data.id, data)
				.then((res) => {

					this.$notify({
						title: 'Thông báo',
						message: 'Đã cập nhật thành công ',
						type: 'success'
					})

					this.getData();

				})

			},
			saveDiscount(){

				this.$refs.formDiscountCode.validate((valid)=>{

					if(valid)
					{
						let data = Object.assign({}, this.formDiscountCode);
						data.value = Object.assign({}, this.formDiscountCode.value);

						if(data.value.users && data.value.users.length == 0)
							delete data.value.users;

						if(data.value.categories && data.value.categories.length == 0)
							delete data.value.categories;

						if(data.value.products && data.value.products.length == 0)
							delete data.value.products;

						if(data.value.ptype && data.value.ptype.length == 0)
							delete data.value.ptype;

						if(data.value.rangeDate && moment(data.value.rangeDate).isValid())
						{
							data.value.begin_date = data.value.rangeDate[0];
							data.value.end_date = data.value.rangeDate[1];
							delete data.value.rangeDate;
						}
						else
						{
							data.begin_date = null;
							data.end_date = null;
						}

						let p;
						if(data.id)
							p = this.updateDiscount(data);
						else
							p = this.addDiscount(data);

						p.then(() => this.showDialog = false);
					}

				})

			},
			deleteDiscount(id){

				if(!this.$_casl_check('discount-code', 'delete')) return;

				this.$confirm('Bạn muốn xoá?', 'Xoá',{
					confirmButtonText: 'Có',
					cancelButtonText: 'Không',
					type: 'warning'
				}).then(()=>{

					this.axios.delete(this.api.discount_code.get() + '/' + id)
					.then(() => {

						this.$notify({
							title: 'Thông báo',
							message: 'Đã xoá thành công',
							type: 'success'
						})

						this.getData();

					})
				});

			},
			addProduct(item)
			{
				this.queryString = '';

				if(this.formDiscountCode.value.products)
				{
					if(this._.find(this.formDiscountCode.value.products, { id: item.id }))
					{
						this.$notify({
							type: 'warning',
							message: 'Sản phẩm này đã có trong danh sách',
							title: 'Thông báo'
						})
					}
					else
						this.formDiscountCode.value.products.push(item);
				}
				else
				{
					this.formDiscountCode.value.products = [item];
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
								name: `${p.name} (${sku.name})`,
								product_id: p.id,
								sku_id: sku.id
							})
						})
					})

					cb(items);
				})
			},
			deleteProduct(index)
			{
				this.formDiscountCode.value.products.splice(index, 1);
			}
		},
		created(){
			this.getData();

			this.axios.get(this.api.users.get(), { params: { type: 'customer' } })
			.then(res=>{
				this.customerData = res.data;
			});

			this.axios.get(this.api.producttypes.get())
			.then(res=>{
				this.pTypeData = res.data;
			});

			this.axios.get(this.api.brands.get())
			.then(res=>{
				this.brandData = res.data;
			});

			this.axios.get(this.api.categories.get()).then(res => {
			
				let addCategoryOptions = (category) => {
					this.categoryData.push({ id: category.id, name: category.name });
					if(category.children.length > 0)
						category.children.forEach(item => addCategoryOptions(item));
				}

				if(res.data.length > 0)
					addCategoryOptions(res.data[0]);

			});	
		}
	}
</script>

<style scoped>
</style>