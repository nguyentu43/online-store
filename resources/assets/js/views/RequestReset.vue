<template>
	<el-row class="panel">
		<div class="panel-heading">
			<h5>Yêu cầu khôi phục mật khẩu</h5>
		</div>
		<el-col :sm="{ span: 10, offset: 7}">
			<div class="form panel-body">
				<el-form :model="form" ref="form" label-width="120px" :rules="{ email: [ {required: true, trigger: 'blur'}, { type: 'email', trigger: 'change' } ] }">
					<el-form-item label="Địa chỉ email" prop="email">
						<el-input size="small" type="email" v-model="form.email"></el-input>
					</el-form-item>
					<el-form-item>
						<el-button native-type="submit" :disabled="showTag" @click.prevent="sendRequest" type="primary" size="small">Gửi yêu cầu</el-button>
						<el-tag v-if="showTag" type="success">Đã gửi email. Hãy kiểm tra lại email của bạn</el-tag>
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

					if(valid)
					{
						this.axios.post(this.api.request_reset.get(), this.form)
						.then(() => {

							this.showTag = true;
							this.$notify({
								type: success,
								message: 'Đã gửi email. Hãy kiểm tra lại email của bạn',
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