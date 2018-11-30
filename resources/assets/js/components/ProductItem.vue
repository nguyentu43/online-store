<template>
	<div class="product" ref="product">
		<div class="img">
      		<img :src="$_storage_getImagePath(skuSelected.media[0] ? skuSelected.media[0].url : null)" class="text-click" @click="$router.push({name: 'product', params: { slug: data.slug }})">
      	</div>
      	<div class="body">
        	<div class="title text-click" @click="$router.push({name: 'product', params: { slug: data.slug }})">{{ data.name }}</div>
        	<template v-if="skuSelected.discount && $_product_checkExpDiscount(skuSelected.discount)">
				<div class="price">
					<span class="text-warning">{{ skuSelected.price * (1 - skuSelected.discount.value) | currency('đ', 0, { symbolOnLeft: false, spaceBetweenAmountAndSymbol: true }) }}
					</span>

					<del class="text-muted">({{ skuSelected.price | currency('đ', 0, { symbolOnLeft: false, spaceBetweenAmountAndSymbol: true }) }})</del>

					<el-tag class="tag-discount" size="small" type="danger">Giảm {{ skuSelected.discount.value*100 }}%</el-tag>

					<el-popover
					class="btn-promotion"
				    placement="right"
				    title="Khuyến mại"
				    width="200"
				    trigger="click"
				    v-if="skuSelected.promotion"
				    >
				    	<div v-html="skuSelected.promotion"></div>
				    	<el-button slot="reference" type="warning" size="mini" circle><font-awesome-icon icon="gift" /></el-button>
				  	</el-popover>

				</div>
			</template>
			<template v-else>
				<div class="price">{{ skuSelected.price | currency('đ', 0, { symbolOnLeft: false, spaceBetweenAmountAndSymbol: true }) }}
				</div>
			</template>

		    <span v-if="data.rate">
		    	<i class="el-icon-star-on star-color"></i> {{ data.rate.toFixed(1) }}
			</span>

			<span v-if="data.comment_count">
		    	<font-awesome-icon :icon="{prefix: 'far', iconName: 'comments'}" /> {{ data.comment_count }}
			</span>

			<div v-if="skuSelected.discount && $_product_checkExpDiscount(skuSelected.discount)">
				<countdown 
				:start="skuSelected.discount.start_datetime"
				:end="skuSelected.discount.end_datetime"
				:big="false"
				:size="$mq != 'xs' ? 'sm' : 'xs'"
				/>
			</div>

      	</div>
      	<div class="footer" :class="{ 'sold-out': skuSelected.quantity == 0 }">
      		<template v-if="skuSelected.quantity > 0">
      			<el-button v-if="data.skus.length == 1" style="width: 100%" size="small" type="primary" @click='addItemToCart()'><font-awesome-icon icon="cart-plus" /></el-button>
      			<el-popover
      			v-else
			    placement="right"
			    title="Chọn phân loại"
			    :width="150"
			    trigger="click"
			    >
			    	<el-radio-group v-model="selected" size="small">
						<el-radio-button v-for="op, index in data.skus" :key="op.name" :label="index">{{ op.name }}</el-radio-button>
					</el-radio-group>
					<div class="mt-1">
						<el-button style="width: 100%" size="small" type="primary" @click='addItemToCart()'><font-awesome-icon icon="cart-plus" /></el-button>
					</div>
			    	<el-button style="width: 100%" size="small" type="success" slot="reference">
			    		Chọn phân loại
			    	</el-button>
			    </el-popover>
      		</template>
      		<template v-else>
      			<el-button size="small" type="text"> Đã hết hàng</el-button>
      		</template>
      	</div>
    </div>

</template>

<script>

	import CartMixin from '../mixins/cart.js'
	import ProductMixin from '../mixins/product.js'
	import TimeMixin from '../mixins/time.js'
	import Countdown from './Countdown.vue'
	import moment from 'moment'

	export default {
		mixins: [CartMixin, ProductMixin, TimeMixin],
		props: ['data'],
		data(){
			return {
				selected: 0
			}
		},
		methods: {
			addItemToCart()
			{
				let sku = this.skuSelected;

				this.$_cart_addItem(this.data, sku, 1);
			}
		},
		computed: {
			skuSelected()
			{
				return this.data.skus[this.selected];
			}
		},
		mounted()
		{
			this.data.skus.forEach((sku, index) => {

				if(sku.quantity > 0)
				{
					this.selected = index;
					return;
				}

			});
		},
		components: { Countdown }
	}
</script>

<style scoped>
	.product{
		height: 280px;
		box-sizing: border-box;
		position: relative;
		margin-bottom: 5px;
		padding: 5px;
		margin: 0 2.5px 5px 2.5px;
		border-radius: 5px;
		transition: border 0.5s;
		border: 2px solid white;
	}

	.product:hover{
		border: 2px solid rgb(2, 136, 209);
	}

	.product img{
		height: 120px;
		width: auto;
	}

	.product .img{
		text-align: center;
	}

	.product .footer{
		width: 100%;
		position: absolute;
		bottom: 0px;
		margin-left: -5px;
		border-radius: 5px;
	}

	.product .footer.sold-out{
		border-top: 1px solid #f56c6c;
		background: #f56c6c;
		text-align: center;
	}

	.product .footer .el-button{
		color: white;
		font-weight: bold;
	}

	.product .body{
		font-size: 14px;
		line-height: 25px;
	}

	.product .title{
		text-transform: uppercase;
		font-weight: bold;
		text-overflow: ellipsis;
		white-space: nowrap;
		overflow: hidden;
		width: 100%;
	}

	.tag-discount{
		position: absolute;
		right: 5px;
		top: 5px;
	}

	.btn-promotion{
		position: absolute;
		right: 5px;
		top: 35px;
	}

	.el-tag--danger{
		background-color: rgb(245,108,108);
		border-color: rgb(245,108,108);
		color: white;
	}
</style>