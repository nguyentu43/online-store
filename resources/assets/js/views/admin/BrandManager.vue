<template>
	
	<div class="panel">
		<div class="panel-heading">
			<h5>Quản lí thương hiệu</h5>
		</div>

		<div class="panel-body">
				<div class="mb-1">
					<el-button @click="openDialog()" size="small" icon="el-icon-circle-plus-outline" type="primary">Thêm mới</el-button>
				</div>
				<el-table
				:data="brandData"
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
					label="Tên thương hiệu"
					>
						<template slot-scope="scope">
							<span class="col">
								{{ scope.row.name }}
							</span>
							<img class="img-brand" :src="scope.row.url || $_storage_getImageFromApp('NO_IMAGE')"/>
						</template>
					</el-table-column>

					<el-table-column
					label="Mô tả"
					type="expand"
					width="80px"
					>
						<template slot-scope="scope">
							<div v-html="scope.row.description ? scope.row.description: 'Không có'"></div>
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
								@click="deleteBrand(scope.row.id)"
								>Xoá</el-button>
							</span>
						</template>
					</el-table-column>
				</el-table>

				<el-dialog 
				class="dialog-lg" 
				:visible.sync="showDialog" 
				title="Thương hiệu"
				:fullscreen="true"
				@before-close="$refs.formBrand.resetFields()"
				>
						
				<el-form ref="formBrand" :model="formBrand" label-width="140px" :rules="rules">
					<el-form-item prop="name" label="Tên thương hiệu">
						<el-input size="small" v-model="formBrand.name" />
					</el-form-item>
					<el-form-item label="Mô tả" v-if="showDialog">
						<html-editor v-model="formBrand.description"></html-editor>
					</el-form-item>
					<el-form-item label="Hình ảnh">
						<file-upload :limit="1" :file-list="fileList" @success="successUpload" @remove="removeUpload()"/>
					</el-form-item>
					<el-form-item>
						<el-button size="small" type="success" icon="el-icon-check" native-type="submit" @click.prevent="saveBrand">Lưu</el-button>
					</el-form-item>
				</el-form>

			</el-dialog>
		</div>

	</div>

</template>

<script>
	import HtmlEditor from '../../components/HtmlEditor.vue'
	import FileUpload from '../../components/FileUpload.vue'

	export default{
		data(){
			return {
				brandData: [],
				loading: false,
				fileList: [],
				rules:{
					name: [{ required: true, message: 'Tên thương hiệu là bắt buộc', trigger: 'blur' }]
				},
				formBrand: {},
				showDialog: false
			}
		},
		methods:{
			openDialog(data = { })
			{
				if(!this.$_casl_open_dialog('brand', data)) return;

				this.showDialog = true;
				this.formBrand = Object.assign({}, data);
				if(data.image)
					this.fileList = [{name: data.image, url: data.url}];
				else
					this.fileList = [];
			},
			getData(){

				this.loading = true;
				this.axios.get(this.api.brands.get()).then((res)=>{
					this.brandData = res.data;
					this.loading = false;
				})

			},
			addBrand(){

				return this.axios.post(this.api.brands.get(), this.formBrand)
				.then((res) => {

					this.$notify({
						title: 'Thông báo',
						message: 'Đã thêm thành công ',
						type: 'success'
					})

					this.getData();
				})
			},
			updateBrand(){

				return this.axios.put(this.api.brands.get() + '/' + this.formBrand.id, this.formBrand)
				.then((res) => {

					this.$notify({
						title: 'Thông báo',
						message: 'Đã cập nhật thành công ',
						type: 'success'
					})

					this.getData();

				})

			},
			saveBrand(){

				this.$refs.formBrand.validate((valid)=>{

					if(valid)
					{
						let p;
						if(this.formBrand.id)
							p = this.updateBrand();
						else
							p = this.addBrand();

						p.then(() => this.showDialog = false);
					}

				})

			},
			deleteBrand(id){

				if(!this.$_casl_check('brand', 'delete')) return;

				this.$confirm('Bạn muốn xoá?', 'Xoá',{
					confirmButtonText: 'Có',
					cancelButtonText: 'Không',
					type: 'warning'
				}).then(()=>{

					this.axios.delete(this.api.brands.get() + '/' + id)
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
			successUpload(res)
			{
				this.formBrand.image = res.id;
			},
			removeUpload()
			{
				this.formBrand.image = null;
				if(this.formBrand.id){
					this.saveBrand();
				}
			}
		},
		created(){
			this.getData();
		},
		components: {HtmlEditor, FileUpload}
	}
</script>

<style scoped>
	.img-brand{
		height: auto; 
		width: 50px;
		vertical-align: middle;
	}
</style>