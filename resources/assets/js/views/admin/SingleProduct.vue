<template>

	<div class="panel panel-body">
		<el-tabs
		v-loading="loading"
		v-model="currentTab"
		:before-leave="handleBeforeLeave"
		:stretch="true"
		>
			<el-tab-pane
			label="Tổng quan"
			name="overview"
			:lazy="true"
			>
				<el-row>
					<el-col :xs="24" :sm="16">
						<div class="panel panel-body">
							<el-form
							ref="formProduct"
							:model="formProduct"
							:rules="formProductRules"
							label-width="160px"
							
							>
								<el-form-item
								label="Hiện/ẩn"
								>
									<el-checkbox v-model="formProduct.enable"></el-checkbox>
								</el-form-item>

								<el-form-item
								prop="name"
								label="Tên sản phẩm"
								>
									<el-input size="small" v-model="formProduct.name" placeholder="Nhập tên sản phẩm"></el-input>
								</el-form-item>

								<el-form-item
								prop="category_id"
								label="Danh mục"
								>
									<el-cascader
										v-if="categoryOptions.length > 0"
									    :options="categoryOptions"
									    v-model="formProduct.category_id"
									    size="small"
									    >
									</el-cascader>
								</el-form-item>

								<el-form-item
								prop="product_type_id"
								label="Loại sản phẩm"
								>
									<el-select filterable v-model="formProduct.product_type_id" size="small">
										<el-option
										v-for="item in productTypeOptions"
										:key="item.id"
										:value="item.id"
										:label="item.name"
										></el-option>
									</el-select>
								</el-form-item>

								<el-form-item
								prop="brand_id"
								label="Thương hiệu"
								>
									<el-select filterable v-model="formProduct.brand_id" size="small">
										<el-option
										v-for="item in brandOptions"
										:key="item.id"
										:value="item.id"
										:label="item.name"
										></el-option>
									</el-select>
								</el-form-item>

								<el-form-item
								prop="weight"
								label="Trọng lượng, kích thước"
								>
									<el-input size="small" v-model="formProduct.weight" />
								</el-form-item>

								<el-form-item
								prop="description"
								label="Mô tả"
								>
									<html-editor 
									v-model="formProduct.description" 
									></html-editor>
								</el-form-item>

								<el-form-item>
									<el-button size="small" type="success" icon="el-icon-check" native-type="submit" @click.prevent="saveProduct">Lưu</el-button>
								</el-form-item>

							</el-form>
						</div>
					</el-col>
				</el-row>
				
			</el-tab-pane>

			<el-tab-pane
			label="Thuộc tính"
			name="prop"
			:lazy="true"
			>
				<product-attr 
				v-if="productData.slug" 
				:slug="productData.slug"></product-attr>
			</el-tab-pane>

			<el-tab-pane
			label="Phân loại, hình ảnh, giá"
			name="variant"
			:lazy="true"
			>
				<product-sku 
				v-if="productData.slug" 
				:slug="productData.slug"></product-sku>
			</el-tab-pane>

			<el-tab-pane
			label="Bình luận"
			name="comments"
			:lazy="true"
			>
				<comment-box
				v-if="productData.slug"
				:slug="productData.slug">
				</comment-box>
			</el-tab-pane>

			<el-tab-pane
			label="Đánh giá"
			name="rate"
			:lazy="true"
			>
				<rate-box
				v-if="productData.slug"
				:slug="productData.slug">
				</rate-box>
			</el-tab-pane>	

		</el-tabs>
	</div>
</template>

<script>

	import HtmlEditor from '../../components/HtmlEditor.vue'
	import ProductAttr from '../../components/admin/ProductAttr.vue'
	import ProductSku from '../../components/admin/ProductSku.vue'
	import CommentBox from '../../components/CommentBox.vue'
	import RateBox from '../../components/RateBox.vue'

	export default{
		data(){
			return {
				productData: {},
				formProduct: {
					enable: false,
					name: '',
					category_id: null,
					brand_id: null,
					product_type_id: null,
					weight: ''
				},
				formProductRules: {
					name: [{ required: true, message: 'Tên sản phẩm là bắt buộc', trigger: 'blur' }],
					brand_id: [{ required: true, message: 'Thương hiệu là bắt buộc', trigger: 'blur' }],
					category_id: [{ required: true, message: 'Danh mục là bắt buộc', trigger: 'blur' }],
					product_type_id: [{ required: true, message: 'Loại sản phẩm là bắt buộc', trigger: 'blur' }]
				},
				categoryOptions: [],
				brandOptions: [],
				productTypeOptions: [],
				currentTab: 'overview',
				loading: false
			}
		},
		methods: {
			getData(){

				this.axios.get(this.api.categories.get())
				.then((res) => {

					var self = this;

					function build_children(category)
					{
						let obj = {
							value: category.id,
							label: category.name
						}
						if(category.children.length == 0)
							return obj;

						obj.children = self._.map( category.children, (item) => {

							if(item.children.length == 0)
							{
								return {
									value: item.id,
									label: item.name
								}
							}
							return {
								value: item.id,
								label: item.name,
								children: build_children(item)
							}
							
						})
						return obj;
					}

					let options = [];

					this._.each(res.data, (item) => {

						options.push(build_children(item))

					})

					this.categoryOptions = options;
				});

				this.axios.get(this.api.brands.get()).then(res => this.brandOptions = res.data);

				this.axios.get(this.api.producttypes.get()).then(res =>this.productTypeOptions = res.data);

			},
			saveProduct()
			{
				this.loading = true;
				this.$refs.formProduct.validate(valid=>{

					if(valid)
					{
						let data = Object.assign({}, this.formProduct);
						data.category_id = this._.last(data.category_id);
						if(data.id)
						{
							this.axios.put(this.api.products.get() + '/' + data.slug, data)
							.then((res)=>{
								if(res.data.slug != data.slug)
								{
									this.formProduct.slug = res.data.slug;
									this.$router.replace({ name: 'product-edit-admin', params: { slug: res.data.slug } })
								}

								this.loading = false;

								

								this.$notify({
									type: 'success',
									message: 'Đã lưu thành công',
									title: 'Thông báo'
								});
							})
						}
						else
						{
							this.axios.post(this.api.products.get(), data)
							.then((res) => {
								location.href = this.api.root + 'admin/product-edit/' + res.data.slug;
							})
						}
					}

				})
			},
			handleBeforeLeave(newTab, oldTab)
			{
				if(oldTab == 'overview' && !this.formProduct.id)
				{
					this.$notify({
						type: 'warning',
						message: 'Bạn không thể xem tab khác khi chưa tạo sản phẩm này',
						title: 'Thông báo'
					})
					
					return false;
				}

				return true;
			}
		},
		created(){
			this.getData();
		},
		mounted(){

			if(!this.$route.params.slug) return;

			this.loading = true;
			this.axios.get(this.api.products.get() + '/' + this.$route.params.slug )
			.then(res => {

				let data = res.data.product;

				this.productData = Object.assign({}, data);

				this.formProduct.id = data.id;
				this.formProduct.slug = data.slug;
				this.formProduct.name = data.name;
				this.formProduct.weight = data.weight;
				this.formProduct.description = data.description;
				this.formProduct.enable = data.enable;

				let order = data.category.order ? data.category.order.split(',').map(item => parseInt(item)) : [];

				this.formProduct.category_id = order.concat(data.category.id);
				this.formProduct.brand_id = data.brand.id;
				this.formProduct.product_type_id = data.product_type.id;
				this.$refs.formProduct.clearValidate();

				this.loading = false;
			});
		},
		components: {HtmlEditor, ProductAttr, ProductSku, CommentBox, RateBox}
	}
</script>