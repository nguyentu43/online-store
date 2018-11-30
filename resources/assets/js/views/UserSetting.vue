<template>

	<el-row>
		<el-col :sm="{span: 5, offset: 2}" :xs="24">
			<el-menu :router="true">
				<el-menu-item index="/user"><font-awesome-icon icon="address-card"/> Thông tin cá nhân</el-menu-item>
				<el-menu-item index="/user/addressbooks"><font-awesome-icon icon="address-book"/> Sổ địa chỉ</el-menu-item>
				<el-menu-item index="/user/orders"><font-awesome-icon icon="boxes"/> Quản lí đơn hàng</el-menu-item>
			</el-menu>
		</el-col>
		<el-col :sm="{span: 16}" :xs="24">
			<router-view></router-view>
		</el-col>
	</el-row>
</template>

<script>
</script>

<style>

</style>