<template>
	<div class="panel">
		<div class="panel-heading">
			<h5>Quản lí campaign</h5>
		</div>

		<div class="panel-body">
			<div class="mb-1">
				<el-button @click="openDialog()" size="small" icon="el-icon-circle-plus-outline" type="primary">Thêm mới</el-button>
			</div>

			<el-table
			:data="campaignData"
			v-loading="loading"
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
				label="Hiển/ẩn"
				width="80px"
				>
					<template slot-scope="scope">
						<el-checkbox v-model="scope.row.enable" @change="formCampaign = Object.assign({}, scope.row);updateCampaign(scope.row)"></el-checkbox>
					</template>
				</el-table-column>

				<el-table-column
				label="Tiêu đề"
				>
					<template slot-scope="scope">
						<span class="col">{{ scope.row.name }}</span>
					</template>
				</el-table-column>
				<el-table-column
				label="Thời gian"
				>
					<template slot-scope="scope">
						<div class="col">
							<countdown 
							:start="scope.row.start_datetime"
							:end="scope.row.end_datetime"
							size="xs"
							/>
						</div>
					</template>
				</el-table-column>
				<el-table-column
				label="Chi tiết"
				width="80px"
				type="expand"
				>
					<template slot-scope="scope">
						<p v-html="scope.row.description">
						</p>
						<p>
							Thời gian: {{ scope.row.start_datetime }} - {{ scope.row.end_datetime }}
						</p>
						<p>
							<img v-viewer :src="scope.row.url"/>
						</p>

						<el-table
						:data="scope.row.products"
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
							label="Sản phẩm"
							>
								<template slot-scope="scope">
									<span class="col">{{ scope.row.name }}</span>
								</template>
							</el-table-column>

							<el-table-column
							label="Hình ảnh"
							>
								<template slot-scope="scope">
									<span class="col">
										<img style="width:50px" :src="scope.row.skus[0].urls.length > 0 ? scope.row.skus[0].urls[0] : $_storage_getImageFromApp('NO_IMAGE')" />
									</span>
								</template>
							</el-table-column>
						</el-table>

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
							@click="deleteCampaign(scope.row.slug)"
							>Xoá</el-button>
						</span>
					</template>
				</el-table-column>
			</el-table>

			<el-dialog 
			class="dialog-lg" 
			:visible.sync="showDialog" 
			title="Campaign"
			:fullscreen="true"
			@before-close="$refs.formCampaign.resetFields()"
			>
					
				<el-form v-if="showDialog" ref="formCampaign" :model="formCampaign" label-width="140px" :rules="rules">
					<el-form-item prop="name" label="Tiêu đề">
						<el-input size="small" v-model="formCampaign.name" />
					</el-form-item>
					<el-form-item label="Mô tả">
						<html-editor v-if="showDialog" v-model="formCampaign.description"></html-editor>
					</el-form-item>
					<el-form-item label="Thời gian" prop="range">
						<el-date-picker
					      v-model="range"
					      type="datetimerange"
					      start-placeholder="Bắt đầu"
					      end-placeholder="Kết thúc"
					      format="dd/MM/yyyy HH:mm:ss"
					      value-format="yyyy-MM-dd HH:mm:ss"
					      >
					    </el-date-picker>
					</el-form-item>

					<el-form-item label="Tìm sản phẩm">
						<el-autocomplete
						style="width: 100%"
						v-model="queryString"
						:fetch-suggestions="querySearchAsync"
						placeholder="Nhập tên sản phẩm"
						value-key="name"
						@select="addProduct"
						></el-autocomplete>
					</el-form-item>

					<el-form-item label="Sản phẩm" prop="products">
						<el-table
						:data="formCampaign.products"
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
									<span class="col">{{ scope.row.name}}</span>
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

					<el-form-item label="Hình ảnh">
						<file-upload 
						:file-list="formCampaign.fileList"
						@success="successUpload"
						@remove="removeUpload()"
						:limit="1"
						/>
					</el-form-item>

					<el-form-item label="Tag với danh mục">
						<el-select
						v-model="formCampaign.categories"
						multiple
						size="small"
						>
							<el-option v-for="item in categoryOptions" :key="item.id" :value="item.id" :label="item.name"></el-option>
						</el-select>
					</el-form-item>

					<el-form-item>
						<el-button size="small" type="success" icon="el-icon-check" native-type="submit" @click.prevent="saveCampaign">Lưu</el-button>
					</el-form-item>
				</el-form>
			</el-dialog>
		</div>
	</div>
</template>

<script>
	import HtmlEditor from '../../components/HtmlEditor.vue';
	import moment from 'moment';
	import FileUpload from '../../components/FileUpload.vue';
	import Countdown from '../../components/Countdown.vue';

	export default{
		data(){
			return {
				campaignData: [],
				formCampaign: {	},
				showDialog: false,
				loading: false,
				rules: {
					name: [{required: true, message: 'Tiêu đề là bắt buộc', trigger: 'blur'}],
					range: [ { validator:(rule, value, callback) => { 
							if(this.range.length == 0) 
								callback(new Error('Thời hạn là bắt buộc')); 
							else callback(); } 
					} ],
					products: [ { validator(rule, value, callback){
							if(value.length == 0)
								callback(new Error('Chưa thêm sản phẩm'));
							else
								callback();
						} 
					} ]
				},
				range: [],
				categoryOptions: [],
				queryString: ''
			}
		},
		methods: {
			getData(){
				this.loading = true;
				this.axios.get(this.api.campaigns.get(), { params: { skip_prop: 'enable' } }).then(res => {

					res.data.forEach(item => {

						if(item.banner)
							item.fileList = [ {name: item.banner, url: item.url } ];
						else
							item.fileList = [];
					});

					this.campaignData = res.data
					this.loading = false;
				});

				this.axios.get(this.api.categories.get()).then(res => {

					this.categoryOptions = [];
					
					let addCategoryOptions = (category) => {
						this.categoryOptions.push({ id: category.id, name: category.name });
						if(category.children.length > 0)
							category.children.forEach(item => addCategoryOptions(item));
					}

					if(res.data.length > 0)
						addCategoryOptions(res.data[0]);

				});
			},
			openDialog(data={ products: [] })
			{
				if(!this.$_casl_open_dialog('campaign', data)) return;

				this.formCampaign = Object.assign({}, data);

				if(this.formCampaign.id)
					this.range = [ this.formCampaign.start_datetime, this.formCampaign.end_datetime ];
				else
					this.range = [];
				this.showDialog = true;
			},
			saveCampaign()
			{
				this.$refs.formCampaign.validate(valid => {

					this.queryString = '';

					if(!valid) return;

					if(this.range.length > 0)
					{
						this.formCampaign.start_datetime = this.range[0];
						this.formCampaign.end_datetime = this.range[1];
					}

					if(this.formCampaign.id)
					{
						this.updateCampaign();
					}
					else
					{
						this.addCampaign();
					}

				})
				
			},
			updateCampaign(){
				this.axios.put(this.api.campaigns.get() + '/' + this.formCampaign.slug, this.formCampaign).then(() => {
					this.getData();
					this.showDialog = false;
					this.$notify({
						type: 'success',
						message: 'Đã cập nhật',
						title: 'Thông báo'
					})
				});
			},
			addCampaign(){
				this.axios.post(this.api.campaigns.get(), this.formCampaign).then(() => {
					this.getData();
					this.showDialog = false;
					this.$notify({
						type: 'success',
						message: 'Đã tạo',
						title: 'Thông báo'
					})
				});
			},
			deleteCampaign(slug)
			{
				if(!this.$_casl_check('campaign', 'delete')) return;
					
				this.$confirm('Bạn có muốn xoá?', 'Xoá', {
					confirmButtonText: 'Có',
					cancelButtonText: 'Không',
					type: 'warning'
				}).then(()=>{

					this.axios.delete(this.api.campaigns.get() + '/' + slug).then(() => {
						this.getData();

						this.$notify({
							type: 'success',
							message: 'Đã xoá',
							title: 'Thông báo'
						})
					});

				})
				
			},
			successUpload(res)
			{
				this.formCampaign.banner = res.id;
			},
			removeUpload()
			{
				this.formCampaign.banner = null;
				if(this.formCampaign.id){
					this.saveCampaign();
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

						items.push({
							name: `${p.name}`,
							id: p.id
						})
					})

					cb(items);
				})
			},
			addProduct(data)
			{
				if(!this.formCampaign.products)
					this.formCampaign.products = [];
				this.formCampaign.products.push(data);
			},
			deleteProduct(index)
			{
				this.formCampaign.products.splice(index, 1);
			}
		},
		created(){
			this.getData();
		},
		components: {HtmlEditor, FileUpload, Countdown}
	}
</script>

<style scoped>
	img{
		width: 300px;
		height: auto;
	}
</style>