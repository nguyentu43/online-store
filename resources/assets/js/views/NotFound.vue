<template>
	<el-container>
		<el-main>
			<el-row class="text-center">
				<el-col :sm="24" :xs="24" class="panel panel-body">
					<h1>Đường dẫn này không tồn tại</h1>
					<div><el-button icon="el-icon-back" type="success" @click="$router.push( { name: 'home' } )">Trở về trang chính</el-button></div>
					<img :src="$_storage_getImageFromApp('PAGE_404')"/>
				</el-col>
			</el-row>
		</el-main>
		
	</el-container>
</template>

<script>
	import StorageMixins from '../mixins/storage.js'
	export default{
		mixins: [ StorageMixins ]
	}
</script>

<style scoped>

	@media screen and (max-width: 768px)
	{
		img{
			width: 100%;
		}
	}
	
</style>