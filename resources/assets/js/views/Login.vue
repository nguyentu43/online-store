<template>
	<el-row class="panel">
		<div class="panel-heading">
			<h5>Đăng nhập</h5>
		</div>
		<el-col :sm="{ span: 8, offset: 8 }">
			<div class="form panel-body">
				<el-form ref="formLogin" :model="formLogin" :rules="rulesLogin" label-width="100px">
			        <el-form-item label="Email" prop="email">
			            <el-input size="small" type="email" v-model="formLogin.email"></el-input>
			        </el-form-item>
			        <el-form-item label="Mật khẩu" prop="password">
			            <el-input size="small" type="password" v-model="formLogin.password"></el-input>
			        </el-form-item>

			        <div>
			            <el-button :loading="loadLogin" native-type="submit" @click.prevent="login"><font-awesome-icon icon="sign-in-alt"/> Đăng nhập</el-button>
			            <el-button type="text" @click="$router.push({name:'request-reset'})">Quên mật khẩu</el-button>
			        </div>

			        <div class="mt-1">
			            <el-button type="danger" @click="login_with_provider('google')"><font-awesome-icon :icon="{ prefix: 'fab', iconName: 'google' }"/> Google</el-button>
			            <el-button type="primary" @click="login_with_provider('facebook')"><font-awesome-icon :icon="{ prefix: 'fab', iconName: 'facebook' }"/> Facebook</el-button>
			        </div>
			    </el-form>
			</div>
		</el-col>
	</el-row>
</template>

<script>

	export default{
		data(){
			return {
				formLogin: {
					email: 'eshop@eshop.test',
					password: '123456'
				},
				loadLogin: false,
                rulesLogin: {
                    email: [
                        { required:true, message: 'Địa chỉ email là bắt buộc', trigger: 'blur'},
                        { type: 'email', message: 'Địa chỉ email không hợp lệ', trigger: 'blur'}
                    ],
                    password: [
                        { required:true, message: 'Mật khẩu là bắt buộc', trigger: 'blur' }
                    ]   
                }
			}
		},
		methods:{
			login(){

                this.$refs.formLogin.validate((valid) => {

                    if(valid)
                    {
                        this.loadLogin = true;
                        this.axios.post(this.api.login.get(), this.formLogin).then((res) => {

                        	this.loadLogin = false;

                        	if(!res.data.token)
                        	{
                        		this.$notify({
	                                title: 'Lỗi!',
	                                message: 'Đăng nhập không thành công',
	                                type: 'warning'
	                            })
                        		return;
                        	}

                            this.$notify({
                                title: 'Thông báo!',
                                message: 'Đăng nhập thành công',
                                type: 'success'
                            })

                            localStorage.setItem('token', res.data.token);
                            localStorage.setItem('user', JSON.stringify(res.data.user));
                            this.$store.dispatch('setUser', res.data.user);
                            this.$_casl_update();

                            this.$router.push({name: 'home'});

                        }).catch( () => this.loadLogin = false);
                    }

                })
        	},
        	login_with_provider(provider)
        	{
        		this.axios.get(this.api.login_provider.get() + '/' + provider)
        		.then(res => {
        			let windowLogin = window.open(res.data, "", "width=400px;height=300px");
        			windowLogin.onunload = function(){
        				location.href = this.api.root;
        			}
        		})
        	}
		},
		beforeRouteEnter(to, from, next)
		{
			if(localStorage.getItem('token'))
				next("/");
			else
			{
				next();
			}
		}
	}
</script>

<style scoped>
	.form{
		padding: 10px;
	}
</style>