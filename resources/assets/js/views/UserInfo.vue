<template>
	<el-row class="panel">
		<el-col :xs="24" :sm="12">
			<div class="panel">
				<div class="panel-heading">
					<h5>Chỉnh sửa thông tin</h5>
				</div>
				<div class="panel-body">
					<el-form ref="formUser" :model="formUser" :rules="rules" label-width="100px">
						<el-form-item label="Họ và tên" prop="name">
				            <el-input size="small" type="text" v-model="formUser.name"></el-input>
				        </el-form-item>
				        <el-form-item label="Địa chỉ email">
				            <el-input size="small" type="email" disabled v-model="formUser.email"></el-input>
				        </el-form-item>
				        <el-form-item label="Ngày sinh" prop="date_of_birth">
				            <el-date-picker
				              size="small"
				              v-model="formUser.date_of_birth"
				              type="date"
				              placeholder="Chọn ngày sinh"
				              format="dd/MM/yyyy"
				              >
				            </el-date-picker>
				        </el-form-item>
				        <el-form-item label="Giới tính" prop="gender">
				            <el-radio-group v-model="formUser.gender">
				                <el-radio label="1">Nam</el-radio>
				                <el-radio label="2">Nữ</el-radio>
				            </el-radio-group>
				        </el-form-item>

				        <div v-if="this.user && _.find(this.user.login_providers, { name: 'email' })">
				        	<div class="mb-1 text-muted text-right">Dùng cho tài khoản email</div>
					        <el-form-item label="Mật khẩu cũ">
					            <el-input size="small" type="password" v-model="formUser.oldPassword"></el-input>
					        </el-form-item>
					        <el-tooltip effect="dark" content="Bạn cần nhập lại mật khẩu cũ để đổi mật khẩu mới" placement="top-start">
						        <el-form-item label="Mật khẩu mới">
						            <el-input size="small" type="password" :disabled="changePassword" v-model="formUser.password"></el-input>
						        </el-form-item>
						    </el-tooltip>
						</div>
						<el-form-item>
				        	<el-button size="small" type="success" @click.prevent="updateInfo">
				        		<i class="el-icon-check"></i> Cập nhật
				        	</el-button>
				        </el-form-item>
					</el-form>
				</div>
			</div>
		</el-col>
		<el-col :xs="24" :sm="{ span: 10 }">
			<div class="panel">
				<div class="panel-heading">
					<h5>Liên kết tài khoản</h5>
				</div>
				<div class="panel-body">
					<el-button size="small"><font-awesome-icon icon="envelope"/></el-button>
					<el-button size="small" type="danger" @click="login('google')"><font-awesome-icon :icon="{ prefix: 'fab', iconName: 'google' }"/></el-button>
					<el-button size="small" type="primary" @click="login('google')"><font-awesome-icon :icon="{ prefix: 'fab', iconName: 'facebook' }"/></el-button>
				</div>
			</div>
		</el-col>
	</el-row>
</template>

<script>
	
	export default{
		data(){
			return {
				formUser: {},
				rules: {
					name: [
						{ required: true, message: 'Họ và tên là bắt buộc', trigger: 'blur' }
					],
					date_of_birth: [ { required: true }],
					email: [ { required: true } ]
				}
			}
		},
		computed: {
			user(){
				return this.$store.state.user;
			},
			changePassword(){
				return !this.formUser.oldPassword
			}
		},
		watch: {
			user(user){
				if(user) this.formUser = user;
			}
		},
		created(){
			if(this.user) this.formUser = this.user;
		},
		methods: {
			updateInfo(){

				this.$refs.formUser.validate((valid) => {

					if(valid)
					{
						let data = {
							name: this.formUser.name,
							password: this.formUser.password,
							oldPassword: this.formUser.oldPassword,
							date_of_birth: this.formUser.date_of_birth,
							gender: this.formUser.gender
						}

						this.axios.put(this.api.user.get(), data)
						.then(() => {
							this.$notify({message: 'Đã cập nhật thông tin', type: 'success', title: 'Thông báo'})
						});
					}

				})
			}
		}
	}

</script>

<style scoped>
	.bg-white{
		padding: 20px;
	}
</style>