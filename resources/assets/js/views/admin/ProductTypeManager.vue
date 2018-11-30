<template>

	<div class="panel panel-body"> 
		<el-tabs v-model="activeTab" :stretch="true">
			<el-tab-pane name="ptype" label="Quản lí loại sản phẩm">
				<div class="panel panel-body">
					<div class="mb-1">
						<el-button @click="openDialogPType()" size="small" icon="el-icon-circle-plus-outline" type="primary">Thêm mới</el-button>
					</div>

					<el-table
					v-loading="loadPType"
					:data="pTypeData"
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
						label="Tên loại sản phẩm"
						>
							<template slot-scope="scope">
								<span class="col">{{ scope.row.name }}</span>
							</template>
						</el-table-column>
						<el-table-column
						label="Các thuộc tính"
						width="120px"
						type="expand"
						>
							<template slot-scope="scope">
								<list-attr-tag :ptype="scope.row"></list-attr-tag>
							</template>
						</el-table-column>
						<el-table-column
						label="Tổng số sản phẩm"
						>
							<template slot-scope="scope">
								<span class="col">{{ scope.row.product_count }}</span>
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
									@click="openDialogPType(scope.row);"
									>Sửa</el-button>
									<el-button 
									type="danger" 
									icon="el-icon-delete"
									size="small"
									@click="deletePType(scope.row.id)"
									>Xoá</el-button>
								</span>
							</template>
						</el-table-column>
					</el-table>

					<el-dialog :visible.sync="dialogPType" title="Loại sản phẩm">
						
						<el-form ref="formPType" :model="formPType" label-width="120px" :rules="pTypeRules">
							<el-form-item prop="name" label="Loại sản phẩm">
								<el-input size="small" v-model="formPType.name" />
							</el-form-item>
							<el-form-item>
								<el-button type="success" size="small" icon="el-icon-check" native-type="submit" @click.prevent="savePType">Lưu</el-button>
							</el-form-item>
						</el-form>

					</el-dialog>
				</div>

			</el-tab-pane>
			<el-tab-pane name="pattr" label="Quản lí thuộc tính">

				<div class="panel panel-body">
					<div class="mb-1">
						<el-button @click="openDialogPAttr" size="small" icon="el-icon-circle-plus-outline" type="primary">Thêm mới</el-button>
					</div>

					<el-table
					:data="pAttrData"
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
						label="Tên thuộc tính"
						>
							<template slot-scope="scope">
								<span class="col">{{ scope.row.name }}</span>
							</template>
						</el-table-column>

						<el-table-column
						label="Có thể lọc"
						width="120px"
						>
							<template slot-scope="scope">
								<span class="col">
									<el-checkbox v-model="scope.row.filterable" @change="setFilterable(scope.row)"></el-checkbox>
								</span>
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
									@click="openDialogPAttr(scope.row);"
									>Sửa</el-button>
									<el-button 
									type="danger" 
									icon="el-icon-delete"
									size="small"
									@click="deletePAttr(scope.row.id)"
									>Xoá</el-button>
								</span>
							</template>
						</el-table-column>

					</el-table>

					<el-dialog :visible.sync="dialogPAttr" title="Thuộc tính">
						<el-form ref="formPAttr" :model="formPAttr" label-width="120px" :rules="pAttrRules">
							<el-form-item prop="name" label="Tên thuộc tính">
								<el-input size="small" v-model="formPAttr.name" />
							</el-form-item>
							<el-form-item>
								<el-button type="success" size="small" icon="el-icon-check" native-type="submit" @click.prevent="savePAttr">Lưu</el-button>
							</el-form-item>
							
						</el-form>
					</el-dialog>
				</div>

			</el-tab-pane>
		</el-tabs>
	</div>
	
</template>

<script>
	
	import ListAttrTag from '../../components/admin/ListAttrTag.vue';

	export default{
		data(){
			return {
				activeTab: 'ptype',
				pTypeData: [],
				pAttrData: [],
				loadPType: false,
				dialogPType: false,
				dialogPAttr: false,
				formPType: {},
				formPAttr: {},
				pTypeRules: {
					name: [{ required: true, message: 'Tên loại là bắt buộc', trigger:'blur' }]
				},
				pAttrRules: {
					name: [{ required: true, message: 'Tên thuộc tính là bắt buộc', trigger:'blur' }]
				}
			}
		},
		methods:{
			getData(){
				this.loadPType = true;
				this.axios.get(this.api.producttypes.get())
				.then(res=>{
					this.pTypeData = res.data;
				})
				.then(()=>{
					this.axios.get(this.api.attributes.get())
					.then(res=>{
						res.data.forEach(i => i.filterable = i.filterable ? true : false);
						this.pAttrData = res.data;
						this.loadPType = false;
					})
				})
			},
			openDialogPType(data={}){
				if(!this.$_casl_open_dialog('producttype', data)) return;

				this.dialogPType = true;
				this.formPType = Object.assign({}, data);
			},
			openDialogPAttr(data={}){
				if(!this.$_casl_open_dialog('productattr', data)) return;

				this.dialogPAttr = true;
				this.formPAttr = Object.assign({}, data);
			},
			savePType(){

				this.$refs.formPType.validate((valid)=>{

					if(valid)
					{
						let p;
						if(this.formPType.id)
							p = this.updatePType();
						else
							p = this.addPType();

						p.then(()=>{
							this.dialogPType = false;
							this.getData();
						})
					}

				})
			},
			savePAttr(){

				this.$refs.formPAttr.validate((valid)=>{

					if(valid)
					{
						let p;
						if(this.formPAttr.id)
							p = this.updatePAttr();
						else
							p = this.addPAttr();

						p.then(()=>{
							this.dialogPAttr = false;
							this.getData();
						})
					}

				})
			},
			updatePType(){

				return this.axios.put(this.api.producttypes.get() + '/' + this.formPType.id, this.formPType)
				.then(()=>{
					this.$notify({
						title: 'Thông báo',
						message: 'Đã cập nhật thành công ',
						type: 'success'
					})
				})
			},
			updatePAttr(){

				return this.axios.put(this.api.attributes.get() + '/' + this.formPAttr.id, this.formPAttr)
				.then(()=>{
					this.$notify({
						title: 'Thông báo',
						message: 'Đã cập nhật thành công ',
						type: 'success'
					})
				})
			},
			addPType(){
				return this.axios.post(this.api.producttypes.get(), this.formPType)
				.then(()=>{

					this.$notify({
						title: 'Thông báo',
						message: 'Đã tạo thành công loại sản phẩm',
						type: 'success'
					})
				})
			},
			addPAttr(){
				return this.axios.post(this.api.attributes.get(), this.formPAttr)
				.then(()=>{

					this.$notify({
						title: 'Thông báo',
						message: 'Đã tạo thành công thuộc tính sản phẩm',
						type: 'success'
					})
				})
			},
			deletePType(id){

				if(!this.$_casl_check('producttype', 'delete')) return;

				this.$confirm('Bạn muốn xoá loại sản phẩm này?', 'Xoá loại sản phẩm',{
					confirmButtonText: 'Có',
					cancelButtonText: 'Không',
					type: 'warning'
				}).then(()=>{

					this.axios.delete(this.api.producttypes.get() + '/' + id)
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
			deletePAttr(id){

				if(!this.$_casl_check('productattr', 'delete')) return;

				this.$confirm('Bạn muốn xoá thể loại sản phẩm này?', 'Xoá loại sản phẩm',{
					confirmButtonText: 'Có',
					cancelButtonText: 'Không',
					type: 'warning'
				}).then(()=>{

					this.axios.delete(this.api.producttypes.get() + '/' + id)
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
			setFilterable(row)
			{
				if(!this.$_casl_check('productattr', 'update')) return;

				this.axios.patch(this.api.attributes.get() + '/' + row.id, { filterable: row.filterable })
				.then(()=>{
					this.$notify({
						title: 'Thông báo',
						message: 'Đã cập nhật thành công',
						type: 'success'
					})
				})
			}
		},
		created(){
			this.getData();
		},
		components: {
			ListAttrTag
		}
	}

</script>

<style>
	.el-tag + .el-tag {
	    margin-left: 10px;
	    margin-bottom: 5px;
	}
</style>