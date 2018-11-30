<template>
	<div class="panel">
		<div class="panel-heading">
			<h5>Quản lí tài khoản</h5>
		</div>
		<div class="panel-body">
			<el-button class="mb-1" type="primary" icon="el-icon-circle-plus-outline" size="small" @click="openDialog()">Tạo tài khoản</el-button>

			<el-row class="mb-1">
				<el-col :sm="6" :xs="8">
					<el-input
					v-model="filter"
					:clearable="true"
					@clear="getData"
					placeholder="Địa chỉ email, họ và tên"
					>
						<el-button slot="append" @click="getData">
							<i class="el-icon-search"></i>
						</el-button>
					</el-input>
				</el-col>
			</el-row>
			

			<el-table
			:data="userData"
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
				label="Mở/chặn"
				width="80px"
				>
					<template slot-scope="scope">
						<span class="col">
							<el-checkbox v-model="scope.row.enable" @change="formUser = Object.assign({}, scope.row); updateUser(scope.row);"></el-checkbox>
						</span>
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
				label="Địa chỉ email"
				>
					<template slot-scope="scope">
						<span class="col">{{ scope.row.email }}</span>
					</template>
				</el-table-column>

				<el-table-column
				label="Vai trò"
				>
					<template slot-scope="scope">
						<span class="col">{{ getRoles(scope.row.roles) }}</span>
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
								@click="deleteUser(scope.row.email)"
								>Xoá</el-button>
							</span>
						</template>
					</el-table-column>
			</el-table>

			<el-dialog 
	        title="Đăng ký" 
	        :visible.sync="showDialog"
	        @close="$refs.formUser.resetFields()"
	        >
	            <el-form ref="formUser" :model="formUser" :rules="formUserRules" label-width="140px">
	                <el-form-item label="Họ và tên" prop="name">
	                    <el-input size="small" type="text" v-model="formUser.name"></el-input>
	                </el-form-item>
	                <el-form-item label="Vai trò" prop="role_id">
	                    <el-select v-model="formUser.role_id" multiple size="small">
	                    	<el-option
	                    	v-for="item in roleData"
	                    	:key="item.id"
	                    	:value="item.id"
	                    	:label="item.name"
	                    	></el-option>
	                    </el-select>
	                </el-form-item>
	                <el-form-item label="Email" prop="email">
	                    <el-input size="small" :disabled="formUser.id ? true : false" type="email" v-model="formUser.email"></el-input>
	                </el-form-item>
	                <el-form-item label="Ngày sinh" prop="dateofbirth">
	                    <el-date-picker
	                      v-model="formUser.date_of_birth"
	                      type="date"
	                      placeholder="Chọn ngày sinh"
	                      format="dd/MM/yyyy"
	                      size="small"
	                      >
	                    </el-date-picker>
	                </el-form-item>
	                <el-form-item label="Giới tính" prop="gender">
	                    <el-radio-group size="small" v-model="formUser.gender">
	                        <el-radio label="1">Nam</el-radio>
	                        <el-radio label="2">Nữ</el-radio>
	                    </el-radio-group>
	                </el-form-item>

	                <div v-if="!formUser.id">
		                <el-form-item label="Mật khẩu" prop="password">
		                    <el-input size="small" type="password" v-model="formUser.password"></el-input>
		                </el-form-item>
		                <el-form-item label="Mật khẩu nhập lại" prop="rpassword">
		                    <el-input size="small" type="password" v-model="formUser.rpassword"></el-input>
		                </el-form-item>
	            	</div>
	                <el-form-item>
	                    <el-button native-type="submit" @click.prevent="saveUser" type="success" size="small" icon="el-icon-check">Lưu</el-button>
	                </el-form-item>

	            </el-form>
	        </el-dialog>
	    </div>
	</div>
</template>

<script>

	import moment from 'moment';

	export default{
		data(){
			return {
				userData: [],
				formUser: {},
				showDialog: false,
				loading: false,
				roleData: [],
				filter: '',
				formUserRules: {
					name: [
                        { required:true, message: 'Họ và tên là bắt buộc', trigger: 'blur'},
                        { min: 6, message: 'Họ và tên phải dài hơn 6 kí tự', trigger: 'change' }
                    ],
                    role_id: [
                        { required:true, message: 'Vai trò là bắt buộc', trigger: 'blur'}
                    ],
                    email: [
                        { required:true, message: 'Địa chỉ email là bắt buộc', trigger: 'blur'},
                        { type: 'email', message: 'Địa chỉ email không hợp lệ', trigger: 'blur'},
                        { validator: (rule, value, callback) => {

                        	if(this.formUser.id)
                        		return callback();

                            this.axios.get(this.api.user.get() + '/email', {params: {value}}).then((res) => {

                                if(value === '')
                                    return callback()

                                if(!res.data.match)
                                    callback()
                                else
                                    callback(new Error('Địa chỉ email này đã đăng ký rồi'))

                            })
                                    }, trigger: 'change'}
                    ],
                    date_of_birth: [
                        { required:true, message: 'Ngày sinh là bắt buộc', trigger: 'blur'},
                    ],
                    gender: [
                        { required:true, message: 'Giới tính là bắt buộc', trigger: 'change'},
                    ],
                    password: [
                        { required:true, message: 'Mật khẩu là bắt buộc', trigger: 'blur'},
                        { min:6, message: 'Mật khẩu phải dài hơn 6 kí tự', trigger: 'change'}
                    ],
                    rpassword: [
                        { required:true, message: 'Mật khẩu nhập lại là bắt buộc', trigger: 'blur'},
                        { validator: (rule, value, callback) => {

                            if(this.formUser.password != value)
                                callback(new Error('Mật khẩu nhập lại không trùng khớp'))
                            else
                                callback()

                        }, trigger: 'change'}
                    ]
				}
			}
		},
		methods:{
			getData(){
				this.loading = true;
				this.axios.get(this.api.users.get(), { params: { filter: this.filter } }).then(res=>{
					this.userData = res.data;
					this.userData.forEach(i => {
						i.role_id = i.roles.map(i => i.id)
					});
					this.loading = false;
				})
			},
			openDialog(data = { })
			{
				if(!this.$_casl_open_dialog('user', data)) return;

				this.formUser = Object.assign({}, data);
				this.showDialog = true;
			},
			getRoles(roles)
			{
				let result = roles.map(i => i.name);
				return result.join(', ');
			},
			saveUser()
			{
				this.$refs.formUser.validate(valid => {

					if(valid)
					{
						this.formUser.date_of_birth = moment(this.formUser.date_of_birth).format('YYYY-MM-DD');

						if(this.formUser.id)
							this.updateUser();
						else
							this.addUser();
					}
					
				})
			},
			updateUser()
			{
				this.axios.put(this.api.users.get() + '/' + this.formUser.email, this.formUser)
				.then(() => {

					this.$notify({
						type: 'success',
						message: 'Đã cập nhật thành công',
						title: 'Thông báo'
					});

					this.showDialog = false;
					this.getData();

				})
			},
			addUser()
			{
				this.axios.post(this.api.users.get(), this.formUser)
				.then(() => {

					this.$notify({
						type: 'success',
						message: 'Đã tạo thành công',
						title: 'Thông báo'
					});

					this.showDialog = false;
					this.getData();

				})
			},
			deleteUser(email)
			{
				if(!this.$_casl_check('user', 'delete')) return;

				this.$confirm('Bạn có muốn xoá?', 'Xoá', {
					confirmButtonText: 'Có',
					cancelButtonText: 'Không',
					type: 'warning'
				})
				.then(() => {

					this.axios.delete(this.api.users.get() + '/' + email, this.formUser)
					.then(() => {

						this.$notify({
							type: 'success',
							message: 'Đã tạo thành công',
							title: 'Thông báo'
						});

						this.showDialog = false;
						this.getData();

					})

				})
			}
		},
		created(){
			this.getData();
			this.axios.get(this.api.roles.get()).then(res=>{
				this.roleData = res.data.roles;
			})
		}
	}
</script>

<style>

</style>