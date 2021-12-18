<template>
	<div class="panel panel-body">
		<el-button
		type="primary"
		@click="openDialog()"
		size="small"
		icon="el-icon-circle-plus-outline"
		class="mb-1"
		>
			Thêm mới
		</el-button>
		<br/>
		<el-table
		:data="skuList"
		max-height="500"
		style="width: 100%"
		v-loading="loading"
		border
		>
			<el-table-column
			label="#"
			width="80px"
			>
				<template slot-scope="scope"><span class="col">{{ scope.$index + 1 }}</span></template>
			</el-table-column>

			<el-table-column
			label="Mã SKU"
			width="200px"
			>
				<template slot-scope="scope"><span class="col">{{ scope.row.sku }}</span></template>
			</el-table-column>


			<el-table-column
			label="Tên phân loại"
			width="120px"
			>
				<template slot-scope="scope"><span class="col">{{ scope.row.name }}</span></template>
			</el-table-column>

			<el-table-column
			label="Số lượng"
			width="80"
			>
				<template slot-scope="scope"><span class="col">{{ scope.row.quantity }}</span></template>
			</el-table-column>

			<el-table-column
			label="Hình ảnh"
			type="expand"
			width="100px"
			>
				<template slot-scope="scope">
					<div v-if="scope.row.urls.length > 0" class="box-img" v-viewer>
						<img v-for="url in scope.row.urls" :src="url" />
					</div>
					<span class="col" v-else>Không có</span>
				</template>
			</el-table-column>

			<el-table-column
			label="Giá tiền"
			width="150px"
			sortable
			>
				<template slot-scope="scope">
					<span class="col">{{ scope.row.price | currency('đ', 0, { symbolOnLeft: false, spaceBetweenAmountAndSymbol: true }) }}
					</span>
				</template>
			</el-table-column>

			<el-table-column
			label="Giảm giá"
			width="150px"
			>
				<template slot-scope="scope">
					<template v-if="scope.row.discount">
						<div class="col">
							<el-tag type="danger">
								{{ scope.row.discount.value * 100 }}%
							</el-tag>
							<div class="mt-1">
								<countdown 
								:start="scope.row.discount.start_datetime"
								:end="scope.row.discount.end_datetime"
								size="xs"
								/>
							</div>
						</div>
					</template>
					<span v-else class="col">Không có</span>
				</template>
			</el-table-column>

			<el-table-column
			label="Thao tác"
			fixed="right"
			width="180px"
			>
				<template slot-scope="scope">
					<el-button
					type="success"
					size="small"
					icon="el-icon-edit"
					@click="openDialog(scope.row)"
					>Sửa</el-button>
					<el-button
					type="danger"
					size="small"
					icon="el-icon-delete"
					@click="deleteSku(scope.row.id)"
					>Xoá</el-button>
				</template>
			</el-table-column>
		</el-table>

		<el-dialog
		:visible.sync="showDialog"
		title="Phân loại, giá"
		@close="$refs.formSku.resetFields()"
		>
			<el-form
			ref="formSku"
			:model="formSku"
			:rules="skuRules"
			label-width="150px"
			>
				<el-form-item
				label="Tên phân loại"
				prop="name"
				>
					<el-input size="small" v-model="formSku.name"></el-input>
				</el-form-item>

				<el-form-item
				label="Giá tiền"
				prop="price"
				>
					<el-input-number size="small" :min="0" :step="100" v-model="formSku.price"></el-input-number>
				</el-form-item>

				<el-form-item
				label="Số lượng"
				prop="quantity"
				>
					<el-input-number size="small" :min="0" v-model="formSku.quantity"></el-input-number>
				</el-form-item>

				<el-form-item prop="discount">
					<el-checkbox @change="setDiscount" v-model="formSku.isDiscount">Giảm giá</el-checkbox>
				</el-form-item>

				<div v-if="formSku.isDiscount">
					<el-form-item prop="">
						<el-input-number size="small" :min="0.1" :step="0.1" v-model="formSku.discount.value"></el-input-number>
					</el-form-item>

					<el-form-item>
						<el-date-picker
					      v-model="range"
					      type="datetimerange"
					      start-placeholder="Bắt đầu"
					      end-placeholder="Kết thúc"
					      value-format="yyyy-MM-dd HH:mm:ss"
					      >
					    </el-date-picker>
					</el-form-item>
				</div>

				<el-form-item label="Hình ảnh" v-if="formSku.id">
					<file-upload 
						:file-list="formSku.fileList"
						@success="successUpload"
						@remove="removeUpload"
						:limit="10"
						/>
				</el-form-item>

				<el-form-item
				label="Khuyến mại"
				v-if="showDialog"
				>
					<html-editor v-model="formSku.promotion" />
				</el-form-item>

				<el-form-item>
					<el-button icon="el-icon-check" @click="saveSku" type="success" size="small">Lưu</el-button>
				</el-form-item>
			</el-form>
		</el-dialog>
	</div>
</template>

<script>
	import moment from 'moment';
	import HtmlEditor from '../HtmlEditor.vue';
	import Countdown from '../Countdown.vue';
	import FileUpload from '../FileUpload.vue';

	export default{
		props: ['slug'],
		data(){

			let checkDiscount = (rule, value, cb) =>
			{
				if(this.formSku && this.formSku.discount && this.formSku.discount.value)
				{
					let discount = this.formSku.discount;
					if(!(discount.value && this.range))
					{
						cb(new Error('Giảm giá, ngày bắt đầu, kết thúc là bắt buộc'));
						return;
					}
				}

				cb();
			}

			return {
				skuList: [],
				formSku: {
					discount: {
						range: ''
					},
					fileList: []
				},
				range: '',
				skuRules: {
					name: [ { required: true } ],
					price: [ { required: true, trigger: 'blur' } ],
					quantity: [ {required: true, trigger: 'blur'}],
					discount: [ { validator: checkDiscount, trigger: 'blur' } ]
				},
				showDialog: false,
				headers: {
					'Authorization': 'Bearer ' + localStorage.getItem('token')
				},
				loading: false
			}
		},
		methods: {
			getData(){

				this.loading = true;

				this.axios.get(this.api.products.getSku(this.slug)).then((res)=>{

					this.skuList = res.data;
					this._.each(this.skuList, item => {

						if(!item.discount){
							return;
						}

						item.isDiscount = true;
						item.discount.range = [ item.discount.start_datetime, item.discount.end_datetime ];
					});

					this.loading = false;
				})
			},
			openDialog(data = { discount: {} })
			{
				this.formSku = Object.assign({}, data);
				this.range = this.formSku.discount ? this.formSku.discount.range : [];

				if(data.images){
					this.formSku.fileList = data.images.map((item,index) => ({name: item, url: data.urls[index] }) );
				}

				this.showDialog = true;
			},
			saveSku(){
				this.$refs.formSku.validate(valid => {

					if(!valid) return;

					if(this.formSku.isDiscount)
					{
						this.formSku.discount.start_datetime = moment(this.range[0]).format('YYYY-MM-DD HH:mm:ss');
						this.formSku.discount.end_datetime = moment(this.range[1]).format('YYYY-MM-DD HH:mm:ss');
					}

					if(this.formSku.id)
						this.updateSku();
					else
						this.addSku();
				})
			},
			addSku(){
				this.axios.post(this.api.products.getSku(this.slug), this.formSku)
				.then(res => {

					this.$notify({
						type: 'success',
						message: 'Bạn đã thêm thành công',
						title: 'Thông báo'
					});

					this.showDialog = false;

					this.getData();
				})
			},
			updateSku(){
				this.axios.put(this.api.products.getSku(this.slug) + '/' + this.formSku.id, this.formSku)
				.then(res => {

					this.$notify({
						type: 'success',
						message: 'Bạn đã cập nhật thành công',
						title: 'Thông báo'
					});

					this.showDialog = false;

					this.getData();
				})
			},
			deleteSku(id){

				this.$confirm('Bạn có muốn xoá?', 'Xoá', {
					confirmButtonText: 'Có',
					cancelButtonText: 'Không',
					type:'warning'
				}).then(() => {

					this.axios.delete(this.api.products.getSku(this.slug) + '/' + id)
					.then(res => {

						this.$notify({
							type: 'success',
							message: 'Bạn đã xoá thành công',
							title: 'Thông báo'
						});

						this.getData();
					})

				})
			},
			setDiscount(val)
			{
				if(val)
					this.formSku.discount = { value: 0.1 };
				else
					this.formSku.discount = { };
			},
			successUpload(res)
			{
				this.formSku.images.push(res.id);
			},
			removeUpload(res)
			{
				this.formSku.images = this.formSku.images.filter(item => item != res.id);
				if(this.formSku.id){
					this.saveSku();
				}
			}
		},
		mounted(){
			this.getData();
		},
		components: { HtmlEditor, Countdown, FileUpload }
	}
</script>

<style>
	.sku{
		padding: 15px;
		border: 2px solid #eee;
	}
	.sku + .sku{
		margin-top: 15px
	}

	.box-img img{
		margin-left: 10px;
		height: 100px;
		width: auto;
	}

	.el-dialog{
		width: 60%;
	}
</style>