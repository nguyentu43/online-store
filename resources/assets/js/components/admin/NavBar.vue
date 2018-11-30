<template>
	
	<el-row id="navbar">
		<el-col :sm="12">
			<el-button size="small" type="success" @click="$router.push({ name: 'home' })"><font-awesome-icon icon="shopping-bag"/> EShop</el-button>
		</el-col>
		<el-col :sm="12" class="text-right">
			<el-popover
			placement="bottom"
			title="Thông tin"
			width="200"
			trigger="click"
			>
				<div v-if="user">
					<div>Họ tên: {{ user.name }}</div>
					<div>Email: {{ user.email }}</div>
					<div>Vai trò: {{ _.map(user.roles, i => { return i.name }).join(', ') }}</div>
				</div>
				<el-button slot="reference" size="small">Thông tin tài khoản</el-button>
			</el-popover>
			<el-button type="danger" size="small" @click="logout" v-if="user"><font-awesome-icon icon="sign-out-alt"/> Đăng xuất ({{ user.name }})</el-button>
		</el-col>
	</el-row>

</template>

<script>
	
	export default{
		computed: {
			user(){
				return this.$store.state.user;
			}
		},
		methods:{
			logout(){
				this.$store.dispatch('logout');
				this.$_casl_update();
				this.$router.push({ name: 'home' });
			}
		}
	}

</script>

<style scoped>

	#navbar{

		line-height: 60px;

	}

</style>