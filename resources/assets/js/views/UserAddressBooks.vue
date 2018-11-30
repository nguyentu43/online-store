<template>
	<el-row class="panel">
		<div class="panel-body">
			<el-button icon="el-icon-circle-plus" size="small" type="primary" style="margin-bottom: 20px" @click="show= true; formInfo = {}">Tạo địa chỉ mới</el-button>
			<el-table border style="width:100%" v-if="user"
			:data="user.address_books"
			>
				<el-table-column
			      label="#" width="50px"
			      >
			      <template slot-scope="scope">
			        <span class="col">{{ scope.$index + 1 }}</span>
			      </template>
			    </el-table-column>
				<el-table-column
			      label="Họ và tên"
			      >
			      <template slot-scope="scope">
			        <span class="col">{{ scope.row.name }}</span>
			      </template>
			    </el-table-column>
			    <el-table-column
			      label="Địa chỉ"
			      >
			      <template slot-scope="scope">
			        <span class="col">{{ scope.row.address + ', ' + scope.row.city }}</span>
			      </template>
			    </el-table-column>
			    <el-table-column
			      label="Số ĐT"
			      >
			      <template slot-scope="scope">
			        <span class="col">{{ scope.row.phone }}</span>
			      </template>
			    </el-table-column>
			    <el-table-column
			      label="Hành động"
			      >
			      <template slot-scope="scope">
			      	<el-button-group>
				        <el-button type="success" size="small" @click="editItem(scope.$index)"><i class="el-icon-edit"></i></el-button>
				        <el-button size="small" type="danger" @click="deleteItem(scope.$index)"><i class="el-icon-delete"></i></el-button>
				    </el-button-group>
			      </template>
			    </el-table-column>
			</el-table>
		</div>
		

		<el-dialog :visible.sync="show" title="Điền thông tin địa chỉ" @close="$refs.formInfo.resetFields()">
			<el-form ref="formInfo" :model="formInfo" label-width="120px" :rules="rules">
				<el-form-item label="Họ và tên" prop="name">
					<el-input size="small" v-model="formInfo.name"></el-input>
				</el-form-item>
				<el-form-item label="Địa chỉ" prop="address">
					<el-input size="small" v-model="formInfo.address"></el-input>
				</el-form-item>
				<el-form-item label="Tỉnh/thành phố" prop="city">
					<el-select 
					v-model="formInfo.city"
					filterable
					size="small"
					>
						<el-option
						v-for="item in provider"
						:label="item.name_with_type"
						:value="item.name_with_type"
						:key="item.code"
						>
						</el-option>
					</el-select>
				</el-form-item>
				<el-form-item label="Số điện thoại" prop="phone">
					<el-input size="small" v-model="formInfo.phone"></el-input>
				</el-form-item>
				<el-form-item>
					<el-button type="success" @click="save" size="small" icon="el-icon-check">Lưu</el-button>
				</el-form-item>
			</el-form>
		</el-dialog>
	</el-row>

</template>

<script>

	import provider from '../data/tinh_tp.json'

	export default {
		data(){
			return {
				formInfo: {},
				show: false,
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
					]
				}
			}
		},
		computed: {
			user(){
				return this.$store.state.user;
			},
			provider(){
				return provider;
			}
		},
		methods: {
			save(){

				this.$refs.formInfo.validate((valid) => {

				if(valid)
				{
					let formInfo = this.formInfo;

						if(formInfo.id)
						{
							this.axios.put(this.api.user.get() + '/addressbooks/' + formInfo.id, formInfo)
							.then(() => {
								this.$notify({
									title: 'Thông báo',
									message: 'Đã cập nhật thành công địa chỉ',
									type: 'success'
								})

								this.show = false;
							});
						}
						else
						{
							this.axios.post(this.api.user.get() + '/addressbooks', formInfo)
							.then((res) => {

								this.$notify({
									title: 'Thông báo',
									message: 'Đã thêm thành công địa chỉ',
									type: 'success'
								})

								this.user.address_books.push(res.data.data);

								formInfo = {}

								this.show = false;
							});
						}
					}

				})

				
			},
			deleteItem(index){

				this.$confirm('Bạn có muốn xoá?', 'Xoá', {
					confirmButtonText: 'Có',
					cancelButtonText: 'Không',
					type: 'warning'
				}).then(() => {

					let id = this.user.address_books[index].id;

					this.user.address_books.splice(index, 1);

					this.axios.delete(this.api.user.get() + '/addressbooks/' + id)
					.then(() => {
						this.$notify({
							title: 'Thông báo',
							message: 'Đã xoá thành công địa chỉ',
							type: 'success'
						})
					})

				});
				
			},

			editItem(index){
				this.formInfo = this.user.address_books[index];
				this.show = true;
			}
		}
	}
</script>