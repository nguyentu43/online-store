<template>
	<el-container>
	  <el-aside width="250px">
	  	<aside-menu></aside-menu>
	  </el-aside>
	  <el-container id="main">
	    <el-header>
	    	<nav-bar></nav-bar>
	    </el-header>
	    <el-main>
	    	<router-view></router-view>
	    </el-main>
	    <el-footer style="line-height: 60px">
	    	<strong>EShop</strong>
	    </el-footer>
	  </el-container>
	</el-container>
</template>

<script>
	import AsideMenu from '../../components/admin/AsideMenu.vue'
	import NavBar from '../../components/admin/NavBar.vue'

	export default{
		created(){
			
		},
		components: { AsideMenu, NavBar }
	}
</script>

<style scoped>
	body{
		margin: 0;
	}

	.el-aside{
		height: 100vh;
		position: sticky;
		top: 0;
	}

	.el-main{
		padding: 10px;
		background-color: #E0E0E0;
	}

	.el-table__expand-icon .el-icon.el-icon-arrow-right{
		transform: rotate(180deg)
	}

	.dialog-lg .el-dialog{
        width: 70%
    }

</style>