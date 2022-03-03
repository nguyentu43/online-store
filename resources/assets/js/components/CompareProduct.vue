<template>
	<div>
		<transition
		enter-active-class="animated bounceIn"
		leave-active-class="animated bounceOut"
		>
			<div id="btn-compare" v-if="list.length > 0">
				<el-badge :value="list.length">
					<el-button type="success" circle @click="show=true;">
						<font-awesome-icon icon="columns"/>
					</el-button>
				</el-badge>
			</div>
		</transition>
		<el-dialog
		width="80%"
		:fullscreen="$mq == 'xs'"
		:visible.sync="show"
		title="So sánh sản phẩm"
		>
			<el-row :gutter="10" style="overflow-x: scroll;">
				<el-col 
				v-for="item in products"
				:key="item.id"
				:xs="24"
				:sm="24 / products.length"
				>
					<div class="text-center">

						<div class="text-center mb-1">
							<el-button type="danger" circle @click="removeCompareProduct(item.id)" icon="el-icon-delete"></el-button>
						</div>

						<div @click="$router.push({ name: 'product', params: { slug: item.slug } }); show = false;">
							<h5>{{ item.name }}</h5>
							<img class="img-product" :src="item.skus[0].images.length === 0 ? $_storage_getImageFromApp('NO_IMAGE') : item.skus[0].urls[0]" />
						</div>

						<div class="mt-1 mb-1">
							<template v-if="item.skus.length > 1">
								<el-button v-for="sku in item.skus" :key="sku.id">
									<strong>{{ sku.name }} - </strong>
									<template v-if="sku.discount">
										{{ sku.price * (1 - sku.discount.value) | currency('đ', 0, { symbolOnLeft: false, spaceBetweenAmountAndSymbol: true }) }}
										<span class="text-danger"> (Giảm {{ sku.discount.value*100 }}%)</span>
									</template>
									<template v-else>
										{{ sku.price | currency('đ', 0, { symbolOnLeft: false, spaceBetweenAmountAndSymbol: true }) }}
									</template>
								</el-button>
							</template>
							<template v-else>
								<el-button>
									<template v-if="item.skus[0].discount">
										{{ item.skus[0].price * (1 - item.skus[0].discount.value) | currency('đ', 0, { symbolOnLeft: false, spaceBetweenAmountAndSymbol: true }) }}
										<span class="text-danger"> (Giảm {{ item.skus[0].discount.value*100 }}%)</span>
									</template>
									<template v-else>
										{{ item.skus[0].price | currency('đ', 0, { symbolOnLeft: false, spaceBetweenAmountAndSymbol: true }) }}
									</template>
								</el-button>
							</template>
						</div>
						
					</div>

					<el-table :data="item.attributes" border :show-header="false">
						<el-table-column
						label="Thông số kĩ thuật">

							<el-table-column
						      label="Thuộc tính"
						      >
						      <template slot-scope="scope">
						        <span class="col"><strong>{{ scope.row.name }}</strong></span>
						      </template>
						    </el-table-column>
						    <el-table-column
						      label="Giá trị"
						      >
						      <template slot-scope="scope">
						        <span class="col">{{ scope.row.pivot.value }}</span>
						      </template>
						    </el-table-column>

						</el-table-column>
					</el-table>
				</el-col>
			</el-row>
		</el-dialog>
	</div>
</template>

<script>

	export default{
		data(){
			return {
				products: [],
				show: false
			}
		},
		computed: {
			list(){
				return this.$store.state.compare_product;
			}
		},
		methods:{
			getProduct()
			{
				if(this.list.length == 0){
					this.show = false;
					return;
				}

				this.axios.get(this.api.products.get(), { params: { list: this.list.join(',') } }).then(res => {
					this.products = res.data.products;
				});
			},
			removeCompareProduct(id)
			{
				this.$store.dispatch('removeCompareProduct', id);
				this.getProduct();
			}
		},
		watch: {
			show(value){
				if(value)
				{
					this.getProduct();
				}
			}
		}
	}
</script>

<style scoped>
	#btn-compare{
		position: fixed;
		right: 15px;
		bottom: 15px;
		z-index: 2200;
	}

	.img-product{
		width: auto;
		height: 200px;
	}

	@media screen and (max-width: 768px){
		.img-product{
			height: 100px;
		}
	}
</style>