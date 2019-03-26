<template>

	<div>
		<campaign :category="category.id" />
		<el-row class="panel panel-body">
			<el-col :sm="4" :xs="12" v-for="c in category.children" :key="c.id">
				<div class="box text-click" @click="moveToCategory(c)">
					<img :src="$_storage_getImagePath(c.image)" />
					<div>{{ c.name }}</div>
				</div>
			</el-col>
			<el-col :sm="4" :xs="12">
				<div class="box text-click" @click="$router.push({
					name: 'search', query: { category: category.id, page: 1 }
				})">
					<img :src="$_storage_getImageFromApp('ALL_PRODUCT')" />
					<div>Xem tất cả</div>
				</div>
			</el-col>
		</el-row>

		<div class="panel" v-for="c in category.children" :key="c.id">
			<div class="panel-heading highlight text-click">
				<h5 @click="moveToCategory(c)">{{ c.name }}</h5>
			</div>
			<div class="panel-body">
				<product-list-by-category
				:data="c"
				mode="'col'"
				></product-list-by-category>
			</div>
		</div>

		<div class="panel">
			<div class="panel-heading highlight text-click highlight-red">
				<h5 @click="moveToCategory(category)">Tất cả</h5>
			</div>
			<div class="panel-body">
				<product-list-by-category
				:data="category"
				mode="'col'"
				></product-list-by-category>
			</div>
		</div>
	</div>

</template>

<script>

	import ProductListByCategory from '../components/ProductListByCategory.vue'
	import Campaign from '../components/Campaign.vue'

	export default {
		data(){
			return {
				category: {},
			}
		},
		beforeRouteUpdate(to, from, next){
			this.init(to).then(() => next());
		},
		created(){
			this.init(this.$route);
		},
		methods:{
			init(route){
				return this.axios.get(this.api.categories.get() + '/' + route.params.slug)
				.then((res)=>{
					this.category = res.data;
				})
			},
			moveToCategory(c)
			{
				if(c.children.length > 0)
					this.$router.push({
						name: 'categories', params: { slug: c.slug }
					});
				else
					this.$router.push({
						name: 'search', query: { category: c.id }
					});
			}
		},
		components: {ProductListByCategory, Campaign}
	}

</script>

<style scoped>
	
	.box{
		padding: 20px;
		margin: 10px;
		text-align: center;
		font-weight: bold;
	}

	.box img{
		height: 40px;
		width: auto;
		vertical-align: middle;
	}

</style>