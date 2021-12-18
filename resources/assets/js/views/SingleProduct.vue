<template>
	<div>
		<div v-if="product">
			<div class="panel panel-body">
				<el-breadcrumb separator-class="el-icon-arrow-right">
				  <template v-for="item in categories">
				  	<el-breadcrumb-item v-if="item.children.length > 0" :key="item.id" :to="{ name: 'categories', params: { slug: item.slug } }" >{{ item.name }}</el-breadcrumb-item>
				  	<el-breadcrumb-item v-else :key="item.id" :to="{ name: 'search', query: { category: item.id } }" >{{ item.name }}</el-breadcrumb-item>
				  </template>	
				  
				</el-breadcrumb>
			</div>

			<div class="panel">
				<el-row>
					<el-col :sm="12" :xs="24">
						<div class="text-center panel-body">
							<carousel :per-page="1">
								<template v-if="skuSelected.urls.length > 0">
							    	<slide v-for="(url, index) in skuSelected.urls" :key="index">
							      		<img @click="$viewer.view(index)" class="img-product" :src="url" />
							    	</slide>
								</template>
								<slide v-else>
							      	<img :src="$_storage_getImageFromApp('NO_IMAGE')()" />
							    </slide>
							</carousel>

							<viewer @inited="viewerInited" :images="getImages" style="display: none">
								<img v-for="img in getImages" :key="img" :src="img" />
							</viewer>
						</div>
					</el-col>
					<el-col :sm="12" :xs="24">
						<div class="product-header panel-body">
							<h5 class="text-uppercase title">{{ product.name }}</h5>
							<template v-if="skuSelected.discount && $_product_checkExpDiscount(skuSelected.discount)">
								<h5 class="price">
									<span class="text-warning">{{ skuSelected.price * (1 - skuSelected.discount.value) | currency('đ', 0, { symbolOnLeft: false, spaceBetweenAmountAndSymbol: true }) }}</span>
									<br/>

									<del class="text-muted">({{ skuSelected.price | currency('đ', 0, { symbolOnLeft: false, spaceBetweenAmountAndSymbol: true }) }})</del> <el-tag type="danger"> {{ skuSelected.discount.value*100 }}%</el-tag>
								</h5>
							</template>
							<h5 v-else class="price">{{ skuSelected.price | currency('đ', 0, { symbolOnLeft: false, spaceBetweenAmountAndSymbol: true }) }}</h5>

							<div v-if="skuSelected.discount && $_product_checkExpDiscount(skuSelected.discount)">
								<countdown 
								:start="skuSelected.discount.start_datetime"
								:end="skuSelected.discount.end_datetime"
								:size="$mq != 'xs' ? 'md' : 'sm' "
								/>
							</div>

							<h5>Thông tin chung</h5>

							<ul class="summary">
								<li>Thương hiệu: <dialog-info v-if="product.brand.description" :title="product.brand.name" :content="product.brand.description || ''"/><span v-else>{{ product.brand.name }}</span>
								</li>
								<li>Trọng lượng: {{ product.weight }}</li>
							</ul>

							<div :class="{ 'panel-buy-mobile': $mq == 'xs' }">

								<template v-if="product.skus.length > 1">
									<div class="mb-1">
										<span>Chọn phân loại: </span>
										<el-radio-group v-model="selected" size="small">
											<el-radio-button v-for="op,index in product.skus" :key="op.name" :label="index">{{ op.name }}</el-radio-button>
										</el-radio-group>
									</div>
								</template>

								<div v-if="skuSelected.quantity > 0">
									<div class="hidden-xs-only">
										<el-input-number :min="1" v-model="quantity" :max="skuSelected.quantity"></el-input-number>
										<el-button type="primary"  @click="addToCart">
											<font-awesome-icon icon="cart-plus" />
										Thêm vào giỏ hàng
										</el-button>
									</div>

									<div class="hidden-lg-only hidden-md-only hidden-sm-only">
										<el-input-number size="small" :min="1" v-model="quantity" :max="skuSelected.quantity"></el-input-number>
										<el-button type="primary" size="small" @click="addToCart">
											<font-awesome-icon icon="cart-plus" />
										Thêm vào giỏ hàng
										</el-button>
									</div>
								</div>
								<div v-else>
									<el-button type="danger">
										Đã hết hàng
									</el-button>
								</div>
							</div>

							<div class="mt-1" v-if="skuSelected.promotion">
								<dialog-info type-button="warning" :content="skuSelected.promotion" title="Khuyến mại"/>
							</div>

							<div class="mt-1">
								<el-button type="success" size="small" @click="addCompareProduct">
									<font-awesome-icon  icon="columns" /> So sánh sản phẩm
								</el-button>
							</div>

							<div class="mt-1">
								<social-sharing
								:url="productHref"
								:title="product.name"
								:description="'Sản phẩm: ' + product.name"
								:quote="'Sản phẩm: ' + product.name"
								inline-template
								>
									<div>
										<font-awesome-icon icon="share-square" size="1x" style="color: rgb(2, 136, 209)"/>
										<network network="facebook">
									      <font-awesome-icon :icon="{ prefix: 'fab', iconName: 'facebook-square' }" size="2x" style="color: #3B5998"/>
									    </network>
									    <network network="googleplus">
									      <font-awesome-icon :icon="{ prefix: 'fab', iconName: 'google-plus-square' }" size="2x" style="color: #EA4335"/>
									    </network>
									    <network network="twitter">
									      <font-awesome-icon :icon="{ prefix: 'fab', iconName: 'twitter-square' }" size="2x" style="color: #55ACEE" />
									    </network>
									    <network network="pinterest">
									      <font-awesome-icon :icon="{ prefix: 'fab', iconName: 'pinterest-square' }" size="2x" style="color: #ad081b"/>
									    </network>
									</div>
								</social-sharing>
							</div>

						</div>
					</el-col>
				</el-row>
			</div>

			<div class="panel">
				<div class="panel-heading">
					<h5><i class="el-icon-edit"></i> Mô tả sản phẩm</h5>
				</div>
				<div class="panel-body">
					<div v-html="product.description" v-click-more="'300'" class="description">
					</div>
				</div>
				</el-col>
			</div>

			<div class="panel">
				<div class="panel-heading">
					<h5><i class="el-icon-tickets"></i> Thông số sản phẩm</h5>
				</div>
				<div class="panel-body">
					<el-table :data="product.attributes" border :style="{width: $mq == 'xs' ? '100%' : '50%'}" :show-header="false">
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
				</div>
			</div>

			<div class="panel rate">
				<div class="panel-heading highlight">
					<h5>Nhận xét, đánh giá</h5>
				</div>
				<div class="panel-body">
					<rate-box
					:slug="product.slug"
					></rate-box>
				</div>
			</div>

			<div class="panel">
				<div class="panel-heading highlight">
					<h5>Bình luận</h5>
				</div>
				<div class="panel-body">
					<comment-box
					:slug="product.slug"
					></comment-box>
				</div>
			</div>

			<div class="panel">
				<div class="panel-heading highlight">
					<h5>Sản phẩm liên quan</h5>
				</div>
				<div class="panel-body">
					<product-list-by-category
					:data="product.category"
					mode="carousel"
					>
					</product-list-by-category>
				</div>
			</div>

			<product-list-by-history />
		</div>
		<div v-else>
			<div class="panel panel-body" v-if="loaded">
				<h5 class="text-center">Sản phẩm không tồn tại</h5>
			</div>
		</div>
	</div>
</template>

<script>

	import CartMixin from '../mixins/cart.js'
	import ProductMixin from '../mixins/product.js'
	import TimeMixin from '../mixins/time.js'
	import CommentBox from '../components/CommentBox.vue'
	import RateBox from '../components/RateBox.vue'
	import ProductListByCategory from '../components/ProductListByCategory.vue'
	import ProductListByHistory from '../components/ProductListByHistory.vue'
	import DialogInfo from '../components/DialogInfo.vue'
	import Countdown from '../components/Countdown.vue'
	import { Carousel, Slide } from 'vue-carousel'
	import Viewer from 'v-viewer/src/component.vue'
	
	export default {
		mixins: [CartMixin, ProductMixin, TimeMixin],
		data(){
			return {
				product: null,
				categories: null,
				selected: 0,
				quantity: 1,
				is_user_rate: false,
				productHref: location.href,
				historyProducts: [],
				loaded: false
			}
		},
		beforeRouteUpdate(to, from, next){
			this.init(to).then(() => {
				window.scrollTo(0, 0);
				next();
			});
		},
		created(){
			this.init(this.$route);
		},
		computed: {
			getImages()
			{
				return this.skuSelected.urls.length > 0 ? this.skuSelected.urls : [this.$_storage_getImageFromApp('NO_IMAGE')()];
			},
			skuSelected() {
				return this.product.skus[this.selected];
			}
		},
		methods: {
			addToCart(){

				this.$_cart_addItem(this.product, this.skuSelected, this.quantity);
			},
			viewerInited(viewer)
			{
				this.$viewer = viewer;
			},
			init(route){

				this.product = null;
				return this.axios.get(this.api.products.get() + '/' + route.params.slug)
				.then((res) => {

					this.product = res.data.product;
					this.categories = res.data.categories;

					this.product.skus.forEach((sku, index) => {

						if(sku.quantity > 0)
						{
							this.selected = index;
							return;
						}

					});

					let historyProducts = [];

					if(localStorage.getItem('historyProducts'))
					{
						historyProducts = JSON.parse(localStorage.getItem('historyProducts'));
					}

					if(historyProducts.indexOf(this.product.id) > -1)
					{
						historyProducts.splice(historyProducts.indexOf(this.product.id), 1);
					}

					historyProducts.unshift(this.product.id);

					localStorage.setItem('historyProducts', JSON.stringify(historyProducts));

					this.is_user_rate = res.data.is_user_rate;

					this.$_app_setTitle(this.product.name);
				}).
				catch(() => {

					this.$_app_setTitle('Sản phẩm không tồn tại');
					this.loaded = true;

				});
			},
			addCompareProduct(){

				if(this.$store.state.compare_product.length < 3)
					this.$store.dispatch('addCompareProduct', this.product.id);
				else
					this.$notify({
						'title': 'Thông báo',
						'message': 'Không so sánh quá 3 sản phẩm',
						'type': 'warning'
					});
			}
		},
		components: { CommentBox, RateBox, ProductListByCategory, DialogInfo, ProductListByHistory, Carousel, Slide, Viewer, Countdown },
		directives: {
			ClickMore: {
				inserted(el, binding)
				{
					if(el.clientHeight < binding.value)
						return;

					el.innerHTML = `<div class='more__content'>${el.innerHTML}</div>`;
					el.innerHTML += `<div class='text-center text-click' style='font-size: 30px;'><i class='el-icon-arrow-down'></i></div>`;
					let content = el.childNodes[0];
					let button = el.childNodes[1];
					let content_style = `height: ${binding.value}px; overflow: hidden`;

					content.style = content_style;
					button.style='font-size: 30px';
					button.addEventListener('click', function(){

						if(button.childNodes[0].className == 'el-icon-arrow-down')
						{
							content.style='height: 100%';
							button.childNodes[0].className = 'el-icon-arrow-up';
						}
						else
						{
							content.style = content_style;
							button.childNodes[0].className = 'el-icon-arrow-down';
						}
					});
				}
			}
		}
	}

</script>

<style scoped>

	.img-product{
		height: 300px;
		width: auto;
		margin: auto;
	}

	.product-header .summary, .product-header .price{
		line-height: 30px;
	}

	.panel-buy-mobile{
		position: fixed;
		bottom: 0;
		left: 0;
		z-index: 2000;
		background: white;
		border: 1px solid #eee;
		text-align: center;
		right: 0;
		padding-top: 10px;
		padding-bottom: 10px;
	}
	
	@media screen and (max-width: 768px){
		.img-product{
			height: 250px;
			width: auto;
		}
	}

	.el-breadcrumb__item{
		cursor: pointer;
	}

</style>