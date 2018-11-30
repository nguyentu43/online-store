<template>
	<el-row class="panel">
		<div class="panel-heading">
			<h5>Khôi phục mật khẩu</h5>
		</div>
		<el-col :sm="{ span: 10, offset: 7}">
			<div class="form">
				<el-form :model="form" ref="form" label-width="120px" :rules="{ password: [ {required: true, trigger: 'blur'} ] }">
					<el-form-item label="Mật khẩu mới" prop="password">
						<el-input size="small" type="password" v-model="form.password"></el-input>
					</el-form-item>
					<el-form-item>
						<el-button native-type="submit" :disabled="showTag" @click.prevent="sendRequest" type="primary" size="small">Đổi mật khẩu</el-button>
						<el-tag v-if="showTag" type="success">Tài khoản của bạn đã được thay đổi mật khẩu</el-tag>
					</el-form-item>
				</el-form>
			</div>
		</el-col>
	</el-row>
</template>

<script>

	export default{
		data(){
			return {
				form: {},
				showTag: false
			}
		},
		beforeRouteEnter(to, from, next){
			if(localStorage.getItem('user'))
			{
				next({name: 'home'});
			}
			else
			{
				next();
			}
		},
		methods: {
			sendRequest()
			{
				this.$refs.form.validate(valid => {

					this.form.token = this.$route.params.token;

					if(valid)
					{
						this.axios.post(this.api.reset_password.get(), this.form)
						.then(() => {

							this.showTag = true;
							this.$notify({
								type: success,
								message: 'Tài khoản của bạn đã được thay đổi mật khẩu',
								title: 'Thông báo'
							})

						})
						.catch(() => this.$refs.form.clearValidate());
					}

				})
			}
		},
		created(){
		}
	}
</script>

<style>
	.form{
		padding: 10px;
	}

</style>