<template>
	<div class="panel">
		<div class="panel-heading highlight">
			<h5>Sản phẩm đã xem</h5>
		</div>
		<div class="panel-body">
			<product-list-box mode='carousel' :data='products'/>
		</div>
	</div>
</template>

<script>
	import ProductListBox from './ProductListBox.vue';

	export default{
		data(){
			return {
				products: []
			}
		},
		created(){

			let historyProducts = JSON.parse(localStorage.getItem('historyProducts'));
			if(!historyProducts || historyProducts.length == 0)
				return;

			this.axios.get(this.api.products.get(), { params: { list: historyProducts.join(",") } })
			.then((res) => {
				this.products = res.data.products;
			});

		},
		components: { ProductListBox }
	}
</script>