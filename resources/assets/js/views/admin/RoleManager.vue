<template>
	<div class="panel">
		<div class="panel-heading">
			<h5>Phân quyền</h5>
		</div>
		<div class="panel-body">
			<el-button class="mb-1" @click="openDialog()" size="small" icon="el-icon-circle-plus" type="primary">Tạo mới</el-button>

			<el-table
			:data="roleData"
			border
			>
				<el-table-column
				label="#"
				width="80px"
				>
					<template slot-scope="scope"><span class="col">{{ scope.$index + 1 }}</span></template>
				</el-table-column>

				<el-table-column
				label="Vai trò"
				>
					<template slot-scope="scope"><span class="col">{{ scope.row.name }}</span></template>
				</el-table-column>

				<el-table-column
				label="Quyền"
				type="expand"
				width="100px"
				>
					<template slot-scope="scope">
						<el-tag v-for="item in scope.row.permission" :key="item" style="margin-bottom: 5px">
							{{ _.find(permission, { 'value': item }).name }}
						</el-tag>
					</template>
				</el-table-column>

				<el-table-column
				label="Thao tác"
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
						@click="deleteRole(scope.row.id)"
						>Xoá</el-button>
					</template>
				</el-table-column>
			</el-table>

			<el-dialog
			:visible.sync="showDialog"
			@close="$refs.form.resetFields()"
			>
				<el-form label-width="100px" ref="form" :model="form" :rules="{ name: [ {required: true} ], permission: [ {required: true} ] }">
					<el-form-item label="Vai trò" prop="name">
						<el-input  size="small" v-model="form.name" />
					</el-form-item>
					<el-form-item label="Quyền" prop="permission">
						<el-select
						size="small"
						v-model="form.permission"
						multiple
						>
							<el-option
							v-for="item in permission"
							:key="item.value"
							:value="item.value"
							:label="item.name"
							></el-option>
						</el-select>
					</el-form-item>
					<el-form-item>
						<el-button type="success" icon="el-icon-check" size="small" @click="saveRole">Lưu</el-button>
					</el-form-item>
				</el-form>
			</el-dialog>
		</div>
	</div>
</template>

<script>

	export default{
		data(){
			return {
				permission: [],
				roleData: [],
				showDialog: false,
				form: {}
			}
		},
		methods: {
			getData(){
				this.axios.get(this.api.roles.get()).then(res => {
					this.roleData = res.data.roles;
					this.permission = res.data.permission;
				});
			},
			openDialog(data = { permission: [] })
			{
				if(!this.$_casl_open_dialog('role', data)) return;

				this.form = Object.assign({}, data);
				this.showDialog = true;
			},
			addRole()
			{
				return this.axios.post(this.api.roles.get(), this.form).then(() => {

					this.$notify({
						type:'success',
						message: 'Đã thêm thành công'
					});
				});
			},
			updateRole()
			{
				return this.axios.put(this.api.roles.get() + '/' + this.form.id , this.form).then(() => {
					this.$notify({
						type:'success',
						message: 'Đã cập nhật thành công'
					});
				});
			},
			saveRole()
			{
				this.$refs.form.validate(valid => {
					if(valid)
					{
						let result;
						if(this.form.id)
							result = this.updateRole();
						else
							result = this.addRole();
						result.then(() => {this.showDialog = false; this.getData();} );
					}
				});
			},
			deleteRole()
			{
				if(!this.$_casl_check('role', 'delete')) return;

				this.axios.delete(this.api.roles.get() + '/' + this.form.id).then(() => {
					this.$notify({
						type:'success',
						message: 'Đã xoá thành công'
					});
					this.getData();
				});
			}
		},
		created(){
			this.getData();
		}
	}
</script>

<style scoped>
	.el-tag{
		margin-right: 5px;
	}
</style>