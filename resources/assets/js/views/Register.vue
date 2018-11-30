<template>
	<el-row class="panel">
        <div class="panel-heading">
            <h5>Đăng ký</h5>
        </div>
		<el-col :sm="{ span: 8, offset: 8 }">
			<div class="form panel">
				<el-form ref="formRegister" :model="formRegister" :rules="rulesRegister" label-width="100px">
			        <el-form-item label="Họ và tên" prop="name">
			            <el-input size="small" type="text" v-model="formRegister.name"></el-input>
			        </el-form-item>
			        <el-form-item label="Email" prop="email">
			            <el-input size="small" type="email" v-model="formRegister.email"></el-input>
			        </el-form-item>
			        <el-form-item label="Ngày sinh" prop="date_of_birth">
			            <el-date-picker
			              v-model="formRegister.date_of_birth"
			              type="date"
			              placeholder="Chọn ngày sinh"
			              format="dd/MM/yyyy"
                          size="small"
			              >
			            </el-date-picker>
			        </el-form-item>
			        <el-form-item label="Giới tính" prop="gender">
			            <el-radio-group v-model="formRegister.gender">
			                <el-radio label="1">Nam</el-radio>
			                <el-radio label="2">Nữ</el-radio>
			            </el-radio-group>
			        </el-form-item>
			        <el-form-item label="Mật khẩu" prop="password">
			            <el-input size="small" type="password" v-model="formRegister.password"></el-input>
			        </el-form-item>
			        <el-form-item label="Nhập lại mật khẩu" prop="rpassword">
			            <el-input size="small" type="password" v-model="formRegister.rpassword"></el-input>
			        </el-form-item>
			        <el-form-item>
			            <el-button native-type="submit" :loading="loadRegister" @click.prevent="register" type="success" icon="el-icon-check">Đăng ký</el-button>
			        </el-form-item>
			    </el-form>
			</div>
		</el-col>
	</el-row>
</template>

<script>

    import moment from 'moment';

	export default{
		data(){
			return {
				formRegister: {
                },
                loadRegister: false,
                rulesRegister: {

                    name: [
                        { required:true, message: 'Họ và tên là bắt buộc', trigger: 'blur'},
                        { min: 6, message: 'Họ và tên phải dài hơn 6 kí tự', trigger: 'change' }
                    ],
                    email: [
                        { required:true, message: 'Địa chỉ email là bắt buộc', trigger: 'blur'},
                        { type: 'email', message: 'Địa chỉ email không hợp lệ', trigger: 'blur'},
                        { validator: (rule, value, callback) => {

                            this.axios.get(this.api.user.get() + '/email/', {params: {value}}).then((res) => {

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

                            if(this.formRegister.password != value)
                                callback(new Error('Mật khẩu nhập lại không trùng khớp'))
                            else
                                callback()

                        }, trigger: 'change'}
                    ]

                }
			}
		},
		methods: {
			register(){

                this.$refs.formRegister.validate((valid) => {

                    if(valid)
                    {
                        this.loadRegister = true;
                        this.formRegister.date_of_birth = moment(this.formRegister.date_of_birth).format('YYYY-MM-DD')
                        this.axios.post(this.api.register.get(), this.formRegister).then((res) => {
                            
                            this.$notify({
                                title: 'Thông báo!',
                                message: 'Đăng ký thành công',
                                type: 'success',
                                position: 'top-right'
                            })

                            locatStorage.setItem('token', JSON.stringify(res.data.token))

                            this.loadRegister = false;

                            location.href="/";

                        }).catch(() => this.loadRegister = false)
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
        },
        created()
        {
        }
	}
</script>

<style scoped>
	.form{
		padding: 10px;
	}
</style>