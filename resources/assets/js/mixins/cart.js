export default {
	computed: {
		cart(){
			return this.$store.state.cart;
		}
	},
	methods: {
		$_cart_addItem(product, sku, quantity){

			let product_name;

			if(product.skus.length > 2)
				product_name = product.name + ' ('+  sku.name + ')';
			else
				product_name = product.name;

			let cart_item = this._.find(this.cart, { product_sku_id: sku.id });
			let cart_quantity = cart_item ? cart_item.quantity : 0;

			if(cart_quantity + quantity <= sku.quantity)
			{
				this.$notify({
					title: 'Thông báo!',
					message: `Bạn vừa thêm ${quantity} sản phẩm ${product_name} vào giỏ hàng`,
					type: 'success'
				});

				return this.$store.dispatch('addItemCart', { product_sku_id: sku.id, quantity });
			}

			this.$notify({
				title: 'Thông báo!',
				message: `Bạn không thể thêm vì vượt quá số lượng mua`,
				type: 'warning'
			});
		},
		$_cart_changeItem(product_sku_id, quantity)
		{
			return this.$store.dispatch('changeItemCart', { product_sku_id, quantity })
		},
		$_cart_removeItem(id)
		{
			return this.$store.dispatch('removeItemCart', id)
		},
		$_cart_sync()
		{
            this.axios.get(this.api.cart.get()).then(res => {

            	let cart_db = [];

	            cart_db = res.data.products.map(function(value){
	                return {
	                    product_sku_id: value.pivot.product_sku_id,
	                    quantity: value.pivot.quantity
	                }
	            });

	            this.$store.dispatch('setDataCart', cart_db);

	            if(cart_db.length > 0)
	            	return;

                if(!(localStorage.getItem('cart') === "undefined" || localStorage.getItem('cart') == null))
                {
                    let cart = JSON.parse(localStorage.getItem('cart'));

                    if(cart.length > 0)
                    {
                        this.axios.post(this.api.cart.get() + '/items', {data: JSON.stringify(cart)})
                        .then(res => {
                            this.$store.dispatch('setDataCart', cart);
                        });
                    }
                }
	        })
		},
		$_cart_getProducts(cart)
		{
			if(cart.length == 0) return Promise.resolve( {items: [], amount: 0} );

			let skus = cart.map(p => p.product_sku_id);

			skus = skus.join(',');

			return this.axios.get(this.api.skus.get(), {params: {list: skus}}).then((res) => {

				let skus = res.data;
				let items = [];
				let amount = 0;

				this._.each(skus, (sku) => {

					let cartItem = this._.find(cart, { product_sku_id: sku.id })

					if(cartItem)
					{
						let item = {

							product:{ 
								name: sku.product.name, 
								url: sku.urls.length > 0 ? sku.urls[0] : '',
								slug: sku.product.slug
							},
							price: sku.price,
							discount: sku.discount ? sku.discount.value : 0,
							sku: {
								name: sku.name,
								code: sku.sku,
								quantity: sku.quantity
							},
							cart: {
								sku_id: cartItem.product_sku_id,
								quantity: cartItem.quantity
							}
						}

						items.push(item);
						amount += item.price * ( 1 - item.discount ) * item.cart.quantity
					}

				});

				return { items, amount };

			})
		}
	}
}