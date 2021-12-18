<template>
	
	<div class="panel">

		<div class="panel-heading">
			<h5>Quản lí danh mục</h5>
		</div>

		<div class="panel-body">
			<div class="mb-1">
				<el-button @click="addCategory" size="small" type="primary" icon="el-icon-circle-plus-outline">Thêm mới</el-button>
			</div>

			<el-tree
			v-loading="loadingTree"
			:data="data" 
			:props="defaultProps" 
			@node-drop="handleDrop"
			default-expand-all
			draggable
			:expand-on-click-node="false"
			>
				<span class="custom-tree-node" slot-scope="{ node, data }">
			        <span>
			        	<el-checkbox v-model="data.enable" @change="updateCategory(data)"></el-checkbox>
			        	<img v-if="data.image" class="img-category" :src="data.url" />
			        	<span :class="{ 'text-muted': !data.enable }">{{ data.name }}</span>
			        </span>
			        <span class="button">
			          <el-button
			            type="success"
			            size="mini"
			            @click="() => openDialog(data)"
			            icon="el-icon-edit">
			            Sửa
			          </el-button>
			          <el-button
			            type="danger"
			            size="mini"
			            @click="() => deleteCategory(data.slug)"
			            icon="el-icon-delete">
			            Xoá
			          </el-button>
			        </span>
		      </span>
			</el-tree>
		</div>

		<el-dialog title="Chỉnh sửa danh mục" :visible.sync="showDialog">
			
			<el-form 
			ref="form"
			:model="form" 
			:rules="{ name: [{ required: true, message: 'Tên danh mục là bắt buộc', trigger: 'blur'}] }"
			label-width="120px"
			>
				<el-form-item label="Hiển/ẩn" prop="enable">
					<el-checkbox v-model="form.enable"></el-checkbox>
				</el-form-item>

				<el-form-item label="Tên danh mục" prop="name">
					<el-input v-model="form.name"></el-input>
				</el-form-item>

				<el-form-item label="Hình ảnh">
					<file-upload
					:limit="1"
					:file-list="fileList"
					@success="successUpload"
					@remove="removeUpload()"
					/>
				</el-form-item>

				<el-form-item>
					<el-button type="success" icon="el-icon-check" size="small" native-type="submit" @click.prevent="saveCategory">Lưu</el-button>
				</el-form-item>
				
			</el-form>

		</el-dialog>
	</div>

</template>

<script>

	import FileUpload from '../../components/FileUpload.vue';

	export default {
		data(){
			return {
				loadingTree: false,
				showDialog: false,
				data: [],
				fileList: [],
				fileListBanner: [],
				defaultProps: {
					children: 'children',
					label: 'name'
				},
				form: {
					name: ''
				}
			}
		},
		methods:{
			addCategory(){

				this.axios.post(this.api.categories.get(), { name: 'Danh mục mới' })
				.then(res => {

					this.data.push(res.data);

					this.$notify({
						type: 'success',
						title: 'Thông báo',
						message: 'Đã thêm mới danh mục'
					});
				});
				
			},
			updateCategory(data){

				this.axios.put(this.api.categories.get() + '/' + data.slug, data)
				.then((res) => {

					this.$notify({
						type: 'success',
						title: 'Thông báo',
						message: 'Đã cập nhật danh mục '
					})

					this.getData()
					this.showDialog = false;

				})
			},
			saveCategory(){
				this.$refs.form.validate((valid)=>{

					if(valid)
					{
						this.updateCategory(this.form);
					}

				})
			},
			openDialog(data){

				if(!this.$_casl_open_dialog('category', data)) return;

				this.showDialog = true; 
				this.form = Object.assign({}, data);

				if(data.image)
					this.fileList = [{name: data.image, url: data.url }];
				else
					this.fileList = [];

				if(data.banner)
					this.fileListBanner = [{name: data.banner, url: data.url }];
				else
					this.fileListBanner = [];
			},
			deleteCategory(slug){

				if(!this.$_casl_check('category', 'delete')) return;

				this.$confirm('Bạn muốn xoá danh mục này?', 'Xoá danh mục',{
					confirmButtonText: 'Có',
					cancelButtonText: 'Không',
					type: 'warning'
				}).then(()=>{

					this.axios.delete(this.api.categories.get() + '/' + slug)
					.then(res => {

						this.$notify({
							type: 'success',
							title: 'Thông báo',
							message: 'Đã xoá danh mục'
						})

						this.getData();
					})

				})
			},
			handleDrop(draggingNode, dropNode, dropType, ev){
				
				if(dropType == 'inner')
				{
					let order = dropNode.data.order ? dropNode.data.order : '';
					draggingNode.data.parent_id = dropNode.data.id;
					draggingNode.data.order = order == '' ? dropNode.data.id : order + ',' + dropNode.data.id ;
				}
				else
				{
					draggingNode.data.parent_id = dropNode.data.parent_id;
					draggingNode.data.order = dropNode.data.order;
				}

				this.updateCategory(draggingNode.data);

			},
			getData(){
				this.loadingTree = true;
				this.axios.get(this.api.categories.get(), { params: { skip_prop: 'enable' } } )
				.then(res => {

					this.data = res.data;
					this.loadingTree = false;
				})
			},
			successUpload(res, file, fileList)
			{
				this.form.image = res.id;
			},
			removeUpload(file, fileList)
			{
				this.form.image = null;
				if(this.form.id){
					this.saveCategory();
				}
			}
		},
		created(){

			this.getData();
		},
		components: { FileUpload }
	}

</script>

<style>

	.custom-tree-node .button{
		opacity: 0;
	}

	.custom-tree-node:hover .button{
		opacity: 1;
	}

	.custom-tree-node{
		font-size: 16px;
		color: black;
	}

	.el-tree-node__content{
		padding: 10px;
	}

	.img-category{
		height: auto;
		width: 40px;
		vertical-align: middle;
	}

</style>