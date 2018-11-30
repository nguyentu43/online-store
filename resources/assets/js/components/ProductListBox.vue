<template>
	
	<div>
		<template v-if="data.length > 0">
			<div v-if="mode == 'carousel'">
				<carousel :per-page-custom="[[768, 2], [992, 4], [1220, 6], [1920, 6]]">
					<template v-for="p in data">
						<slide v-if="!($route.name == 'product' && $route.params.slug == p.slug)">
			            	<product-item :data="p"></product-item>
			        	</slide>
			        </template>
		    	</carousel>
		    </div>
		    <div v-else>
		    	<el-row>
		    		<template v-for="p in data">
			    		<el-col :sm="8" :xs="12" :md="4" v-if="!($route.name == 'product' && $route.params.slug == p.slug)" >
				            <product-item :data="p"></product-item>
				        </el-col>
				    </template>
		    	</el-row>
		    </div>
		</template>
		<div v-else class="text-center">
			<div>
				<img class="img-empty" :src="$_storage_getImageFromApp('ORDER_EMPTY')"/>
			</div>
			<el-tag>Không còn sản phẩm nào</el-tag>
		</div>
	</div>

</template>

<script>
	import ProductItem from './ProductItem.vue';
	import { Carousel, Slide } from 'vue-carousel';

	export default{
		props: {
			data: {
				type: Array,
				required: true
			},
			mode: {
				type: String,
				default: false
			}
		},
		components: { ProductItem, Carousel, Slide }
	}
</script>

<style scoped>

	@media screen and (max-width: 768px){

		.VueCarousel-slide{
			width: 50%;
		}

	}

	.VueCarousel-slide{
		width: 16.6%;
	}

</style>