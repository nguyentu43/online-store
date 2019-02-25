const urlRoot = 'http://homestead.com/';
const api = urlRoot + 'api/';

export default {
	root: urlRoot,
	products: {
		get(){
			return api + 'products';
		},
		types: {
			getAttach(id)
			{
				return api + `producttypes/${id}/attach`;
			},
			getDetach(id)
			{
				return api + `producttypes/${id}/detach`;
			}
		},
		getAttributes(slug)
		{
			return `${api}products/${slug}/attributes`;
		},
		getSku(slug)
		{
			return `${api}products/${slug}/skus`;
		}
	},
	skus: {
		get()
		{
			return api + 'skus';
		}
	},
	rate: {
		get()
		{
			return api + 'rate';
		}
	},
	producttypes: {
		get()
		{
			return api + 'producttypes';
		}
	},
	categories: {
		get()
		{
			return api + 'categories';
		}
	},
	cart: {
		get()
		{
			return api + 'cart';
		}
	},
	brands: {
		get()
		{
			return api + 'brands';
		}
	},
	attributes: {
		get(){
			return api + 'attributes';
		}
	},
	campaigns: {
		get(){
			return api + 'campaigns';
		}
	},
	comments: {
		get(){
			return api + 'comments';
		}
	},
	users: {
		get()
		{
			return api + 'users';
		}
	},
	user: {
		get()
		{
			return api + 'user';
		}
	},
	orders: {
		get()
		{
			return api + 'orders';
		}
	},
	upload: {
		get(){
			return api + 'upload';
		}
	},
	roles: {
		get(){
			return api + 'roles';
		}
	},
	payment: {
		get()
		{
			return api + 'payment-methods';
		}
	},
	login: {
		get()
		{
			return api + 'login';
		}
	},
	login_provider: {
		get()
		{
			return api + 'login-provider';
		}
	},
	register: {
		get()
		{
			return api + 'register';
		}
	},
	request_reset: {
		get()
		{
			return api + 'request-reset';
		}
	},
	reset_password: {
		get()
		{
			return api + 'reset-password';
		}
	},
	statistic: {
		getOrder(){
			return api + 'statistic/order';
		},
		getProductSoldOut()
		{
			return api + 'statistic/products-sold-out';
		}
	},
	discount_code:{
		get()
		{
			return api + 'discount-code';
		},
		check()
		{
			return api + 'check-discount-code';
		}
	}
}