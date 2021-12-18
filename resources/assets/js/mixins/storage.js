const IMAGE = {
	ALL_PRODUCT: 'all-product.png',
	NO_IMAGE: 'no-image.png',
	PAGE_404: 'page-404.png',
	ORDER_EMPTY: 'order-empty.png',
	CART_EMPTY: 'cart-empty.png',
	RATE_EMPTY: 'rate-empty.png',
	COMMENT_EMPTY: 'comment-empty.png',
	ESHOP: 'eshop-icon.png'
}

export default {
	methods: {
		$_storage_getImageFromApp(type)
		{
			return '/images/' + IMAGE[type];
		}
	}
}