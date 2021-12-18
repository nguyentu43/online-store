<template>
	<div class="panel">
		<div class="panel-heading">
			<h5><font-awesome-icon icon="shopping-cart"/> Giỏ hàng</h5>
		</div>
		<el-row v-loading="loading">
			<div class="panel-body">
				<template v-if="items.length > 0">
					<el-row class="cart-item" v-for="item in items" :key="item.id">
						<el-col :sm="5" :xs="24">
							<div class="text-center text-click" @click="$router.push({ name: 'product', params: { slug: item.product.slug }})">
								<img :src="item.product.url || $_storage_getImageFromApp('NO_IMAGE')() "/>
							</div>
						</el-col>
						<el-col :sm="7" :xs="24">
							<div class="detail">
								<strong class="text-uppercase text-click" @click="$router.push({ name: 'product', params: { slug: item.product.slug }})">{{ item.product.name }}</strong>
								<div class="text-muted">Phân loại: {{ item.sku.name }}</div>
								<div class="text-muted">SKU: {{ item.sku.code }}</div>
							</div>
							<el-button size="small" type="danger" @click="remove(item.cart)"><i class="el-icon-delete"></i></el-button>
						</el-col>

						<el-col :sm="{span: 5, offset: 1}" :xs="24">
							<div class="detail">
								<template v-if="item.discount && $_product_checkExpDiscount(item.discount)">
									<strong class="text-danger">{{ item.price * (1 - item.discount) | currency('đ', 0, { symbolOnLeft: false, spaceBetweenAmountAndSymbol: true }) }}</strong><br/>
									<del>({{ item.price | currency('đ', 0, { symbolOnLeft: false, spaceBetweenAmountAndSymbol: true }) }})</del>
									<el-tag type="danger"> {{ item.discount*100 }}% </el-tag>
								</template>
								<div v-else>
									{{ item.price | currency('đ', 0, { symbolOnLeft: false, spaceBetweenAmountAndSymbol: true }) }}
								</div>
							</div>
						</el-col>

						<el-col :sm="6" :xs="24">
							<div class="detail">
								<el-input-number size="small" v-model="item.cart.quantity" @change="change(item.cart)" :min="1" :max="item.sku.quantity"></el-input-number>

								<div class="mt-1"><strong >Thành tiền: {{ item.price * (1 - item.discount) * item.cart.quantity | currency('đ', 0, { symbolOnLeft: false, spaceBetweenAmountAndSymbol: true }) }}</strong></div>
							</div>
						</el-col>
					</el-row>
					<el-row class="text-right">
						<el-col>
							<h5 class="title-border-top">Tổng giá trị: {{ amount | currency('đ', 0, { symbolOnLeft: false, spaceBetweenAmountAndSymbol: true })  }}</h5>
						</el-col>
						<el-col>
							<el-button type="success" @click="checkout" icon="el-icon-check" class="mt-1">Tiến hành đặt hàng</el-button>
						</el-col>
					</el-row>	
				</template>
				<div class="text-center" v-else>
					<h5>Giỏ hàng của bạn đang trống</h5>
					<img id="cart-empty" :src="$_storage_getImageFromApp('CART_EMPTY')" />
					<div class="mt-1"><el-button size="small" icon="el-icon-back" type="success" @click="$router.push( { name: 'home' } )">Trở về trang chính</el-button></div>
				</div>
			</div>
		</el-row>
	</div>
</template>

<script>
	
	import CartMixin from '../mixins/cart.js'
	import ProductMixin from '../mixins/product.js'

	export default {
		mixins: [CartMixin, ProductMixin],
		data(){
			return {
				items: [],
				amount: 0,
				loading: false
			}
		},
		created(){
			this.initItems(this.cart);
		},
		methods: {
			change(cart){
				this.$_cart_changeItem(cart.sku_id, cart.quantity);
			},
			remove(cart){

				this.$confirm('Bạn có muốn xoá?', 'Xoá', {
					confirmButtonText: 'Có',
					cancelButtonText: 'Không',
					type: 'warning'
				}).then(() => {

					this.$_cart_removeItem(cart.sku_id).then(() => {

						this.initItems(this.cart);

					});

				});
			},
			initItems(cart){
				this.loading = true;
				this.$_cart_getProducts(cart).then( ({items,amount}) => {
					this.items = items
					this.amount = amount
					this.loading = false;
				})
			},
			checkout(){
				if(this.$store.state.user)
				{
					this.$router.push({ name: 'checkout'})
				}
				else
				{
					this.$notify({
						title: 'Thông báo!',
						message: 'Bạn cần đăng nhập để mua hàng',
						type: 'warning'
					})
				}
			}
		},
		watch:{
			cart(cart){
				this.initItems(cart);
			}
		}
	}

</script>

<style scoped>
	
	img{
		height: 150px;
		width: auto;
	}

	@media screen and (max-width: 768px){

		.list-item{
			line-height: 30px;
			text-align: center;
		}

	}

	div.bg-white{
		padding: 10px
	}

	.cart-item .detail{
		line-height: 25px;
		margin-bottom: 5px;
	}

	.cart-item{
		margin-bottom: 10px;
		border-bottom: 1px solid #eee;
	}

</style>