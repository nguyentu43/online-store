<template>
	<div v-loading="loading">
		<div v-if="products.length > 0" class="mb-1">
			<el-button
			v-for="item in orderOptions"
			:class="{ 'select-button': sort == item.sort }"
			:style="{ 'color': item.color }"
			:key="item.sort"
			size="mini"
			@click="sort = item.sort; page = 1; products = []; getMoreProduct();"
			>
				{{ item.text }}
			</el-button>
		</div>
		<product-list-box
		:data="products"
		:mode="mode"
		></product-list-box>
		<template v-if="mode == 'carousel'">
			<div class="text-center mt-1" v-if="!hide">
	    		<el-button type="primary" size="small" round @click="moveToCategory(data)">Xem tất cả</el-button>
	    	</div>
	    </template>
	    <template v-else>
	    	<div class="text-center mt-1" v-if="!hide">
	    		<el-button type="primary" size="small" round @click="getMoreProduct">Xem thêm</el-button>
	    	</div>
	    </template>
	</div>
</template>

<script>
	
	import ProductListBox from './ProductListBox.vue'

	export default{
		props: {
			data: {
				type: Object,
				required: true
			},
			mode: {
				type: String,
				required: true
			}
		},
		data(){
			return {
			hide: false,
				loading: false,
				page: 1,
				per_page: 16,
				total: 0,
				products: [],
				orderOptions: [
					{ text: 'Giảm giá', color: '#f56c6c', sort: '-discount' },
					{ text: 'Mới nhất', color: '#409eff', sort: 'newest' },
					{ text: 'Bán chạy', color: '#67c23a', sort: 'bestsell' }
				],
				sort: '-discount'
			}
		},
		components: { ProductListBox },
		methods:{
			getMoreProduct(){
				this.loading = true;
				this.axios.get(this.api.products.get(), { 
					params: {
						category: this.data.id,
						page: this.page,
						per_page: this.per_page,
						sort: this.sort
					}
				}).then(res => {

					this.loading = false;

					this.total = res.data.meta.total;

					if(res.data.products.length > 0)
					{
						this.products = this.products.concat(res.data.products);
						this.page++;
					}
					else
					{
						this.hide = true;
					}
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
		mounted(){
			this.getMoreProduct();
		}
	}

</script>

<style scoped>

	.select-button{
		border: 1px solid rgb(2, 136, 209);
		border-radius: 5px;
	}
</style>