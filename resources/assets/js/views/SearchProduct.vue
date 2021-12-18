<template>
	<el-row>
		<el-col :sm="7" :xs="24">

			<div class="panel btn-mobile-filter" v-if="$mq == 'xs'">
				<el-button type="text" size="small" @click="showFilter = !showFilter"><font-awesome-icon icon="filter"/> Lọc</el-button>
			</div>
			
			<div class="panel filter-box" :class="{ 'filter-box-mobile': $mq == 'xs' }" v-if="showFilter">
				<div class="filter">
					<div class='filter-header'>
						Danh mục sản phẩm
					</div>
					<div class="filter-item">
						<el-cascader
							size="small"
							v-if="categoryOptions.length > 0"
							style="width: 100%"
						    :options="categoryOptions"
						    v-model="filter.category"
						    @change="changeFilter('category')"
						    >
						</el-cascader>
					</div>
				</div>

				<div class="filter" v-if="brandOptions.length > 0">
					<div class='filter-header' >
						Thương hiệu
					</div>
					<div class="filter-item">
						<el-select
						size="small"
						style="width: 100%" 
						v-model="filter.brand" 
						placeholder="Chọn thương hiệu"
						@change="changeFilter('brand')"
						@clear="removeFilter('brand')"
						:clearable="true"
						>
						    <el-option
						      v-for="item in brandOptions"
						      :key="item.id"
						      :value="item.name"
						      >
						      	<span>{{ item.name }}</span>
						      	<img class="img-brand" v-if="item.image" :src="item.url"/>
						    </el-option>
						</el-select>
					</div>
				</div>

				<div class="filter" v-if="rangePriceOptions.length > 0">
					<div class='filter-header'>
						Giá tiền
					</div>
					<div class="filter-item">
						<el-select
						size="small"
						style="width: 100%" 
						v-model="filter.range" 
						placeholder="Chọn giá tiền"
						@change="changeFilter('range')"
						@clear="removeFilter('range')"
						:clearable="true"
						>
						    <el-option
						      v-for="item in rangePriceOptions"
						      :key="item.value"
						      :value="item.value"
						      >
						      {{ item.range[0] | currency('đ', 0, { symbolOnLeft: false, spaceBetweenAmountAndSymbol: true }) }} - {{ item.range[1] | currency('đ', 0, { symbolOnLeft: false, spaceBetweenAmountAndSymbol: true }) }}
						    </el-option>
						</el-select>
					</div>
				</div>

				<template v-for="(attr, key) in attrOptions">
					<div class='filter-header'>
						{{ attr.name }}
					</div>
					<div class="filter-item">
						<el-select
						size="small"
						style="width: 100%" 
						v-model="filter.attrs[key]" 
						:placeholder="'Chọn ' + attr.name.toLowerCase()"
						@change="changeFilter('attrs')"
						@clear="removeAttrFilter(key)"
						:clearable="true"
						>
						    <el-option
						      v-for="item in attrOptions.values"
						      :key="item"
						      :value="item"
						      >
						      	{{ item }}
						    </el-option>

						</el-select>
					</div>
				</template>
			</div>

		</el-col>

		<el-col :sm="17" :xs="24">

			<campaign :key="$route.query.category" :category="$route.query.category"/>

			<div class="panel">
				<div class="filterbar">
					<el-select v-model="filter.sort" placeholder="Lựa chọn sắp xếp" @change="sort" size="small">
					    <el-option
					      v-for="item in sortOptions"
					      :key="item.value"
					      :label="item.label"
					      :value="item.value">
					    </el-option>
					</el-select>
				</div>

				<div class="box" v-loading="loading">
					<el-row>
						<template v-if="products.length > 0">
							<el-col :sm="6" :xs="12" v-for="p in products" :key="p.id">
				                <product-item :data="p"></product-item>
				            </el-col>
				        </template>
			            <el-col v-else :xs="24" class="mt-1 mb-1 text-center">
			            	Không có sản phẩm nào phù hợp
			            </el-col>
					</el-row>

					<div class="text-center" v-if="pagination.total > 0">
						<el-pagination
						  background
						  
						  layout="prev, pager, next"
						  :total="pagination.total"
						  :page-size="pagination.perPage"
						  @prev-click="changePage()"
						  @next-click="changePage()"
						  @current-change="changePage()"
						  :current-page.sync="pagination.page"
						  >
						</el-pagination>
						<div class="text-center mt-1">Tìm được {{ pagination.total }} sản phẩm phù hợp</div>
					</div>

				</div>
			</div>
		</el-col>

	</el-row>

</template>

<script>

	import ProductItem from '../components/ProductItem.vue';
	import Campaign from '../components/Campaign.vue';

	export default{
		data(){
			return {
				sortOptions: [
					{ value: 'price', label: 'Giá tăng dần' },
					{ value: '-price', label: 'Giá giảm dần' },
					{ value: '-discount', label: 'Giảm giá nhiều' },
					{ value: 'newest', label: 'Mới nhất' }
				],
				categoryOptions: [],
				rangePriceOptions: [],
				brandOptions: [],
				attrOptions: [],
				filter: {
					attrs: {},
					category: [-2, -1]
				},
				products: [],
				pagination: {
					total: 0,
					page: 1,
					perPage: 1
				},
				showFilter: false,
				loading: false,
			}
		},
		created(){
			this.init(this.$route);
			this.createCategoryOptions().then(() => this.getProduct(this.$route));
			if(this.$mq != 'xs')
				this.showFilter = true;
		},
		beforeRouteUpdate(to, from, next){
			this.init(to);
			this.getProduct(to).then(() => next());
		},
		methods: {
			getProduct(route){

				if(this.$route.query.keyword)
					this.$_app_setTitle(this.$route.query.keyword + ' | Tìm kiếm');

				this.pagination.page = parseInt(route.query.page);
				this.pagination.perPage = parseInt(route.query.per_page);

				if(this.$mq == 'xs')
					this.showFilter = false;

				this.loading = true;

				return this.axios.get(this.api.products.get(), { params: {...route.query} })
					.then((res) => {

						let data = res.data;

						this.products = data.products;
						this.pagination.total = data.meta.total;
						this.attrOptions = data.meta.attr_filter;
						this.filter.category = data.meta.category;

						let max_price = data.meta.max_price;

						let pow = max_price < Math.pow(10, 5) ? 5 : 6;

						this.rangePriceOptions = [];
						for(let i = 1; i<= Math.ceil(max_price / Math.pow(10, pow)); ++i )
							this.rangePriceOptions.push({ 
								value: `${(i - 1) * Math.pow(10, pow)} - ${i* Math.pow(10, pow)}` , 
								range: [ (i - 1) * Math.pow(10, pow), i * Math.pow(10, pow) ] });
						
						if(data.meta.brands.length > 0)
							this.axios.get(this.api.brands.get(), { params: { list: data.meta.brands.join(',')} })
							.then((res) => {
								this.brandOptions = res.data;
							});

						window.scrollTo(0, 0);

						this.loading = false;
				})
			},
			changePage()
			{
				let query = Object.assign({}, this.$route.query, { page: this.pagination.page });
				this.$router.replace({
					query
				})
			},
			sort(){
				let query = Object.assign({}, this.$route.query, {sort: this.filter.sort, page: 1});
				this.$router.replace({
					query
				})
			},
			changeFilter(name){
				let query = Object.assign({}, this.$route.query);
				switch(name)
				{
					case 'category':
						let last = _.last(this.filter.category);
						if(last > 0)
							query[name] = _.last(this.filter.category)
						else
							delete query[name];
						break;
					case 'attrs':
						query[name] = JSON.stringify(this.filter.attrs);
						break;
					default:
						query[name] = this.filter[name];
				}

				this.$router.replace({
					query
				})
			},
			removeFilter(name){
				delete this.filter[name];
				this.changeFilter(name);
			},
			removeAttrFilter(key)
			{
				delete this.filter.attrs[key];
				this.changeFilter('attrs');
			},
			createCategoryOptions()
			{
			   return this.axios.get(this.api.categories.get())
		       .then((res) => {
					function build_children(category)
					{
						if(category.children.length == 0)
						{
							return [{ value: category.id, label: 'Tất cả' }]
						}
						let options = _.map( category.children, (item) => {

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
						});

						options.push({ value: category.id, label: 'Tất cả' })
						return options;
					}

					let options = [];
					_.each(res.data, (item) => {
						options.push({
							value: item.id,
							label: item.name,
							children: build_children(item)
						})
					})
					options.push({
						value: -1,
						label: 'Tất cả'
					})
					this.categoryOptions = [{value: -2, label: 'Danh mục chính', children: options}];
				})
			},
			init(route){

				for(let key in route.query)
				{
					if([ 'keyword', 'category' ].indexOf(key) > -1)
						continue;
					else if(key == 'page')
					{
						this.pagination.page = parseInt(route.query['page']);
					}
					else if(key == 'per_page')
					{
						this.pagination.perPage = parseInt(route.query['per_page']);
					}
					else if(key == 'attrs')
					{
						this.filter[key] = JSON.parse(route.query[key]);
					}
					else
					{
						this.filter[key] = route.query[key];
					}
				}
			}
		},
		components: {ProductItem, Campaign}
	}

</script>

<style scoped>
	
	.filterbar{
		padding: 10px;
		text-align: right;
	}

	.box{
		padding: 10px;
	}

	.el-submenu__title{
        font-size: 15px;
    }

    .filter-box{
    	border-right: 2px solid #eee;
    }

    .filter-box .filter-item{

    	padding: 10px 20px;
    	border-bottom: 2px solid #eee;
    }

    .filter-item-overflow{
    	max-height: 100px;
    	overflow-y: scroll;
    }

    .filter-box .el-radio{
    	margin-left: 0px;
    	padding: 10px 0px;
    	display: block;
    }

    .filter-box .filter-header{
    	padding: 10px 20px;
    	border-bottom: 2px solid #eee;
    }

    .filter-box-mobile{
    	position: fixed;
    	top: 60px;
    	z-index: 2000;
    	height: calc(100% - 60px - 40px);
    	width: 100%;
    	margin: 0;
    	overflow-y: scroll;
    }

    .btn-mobile-filter{
    	z-index: 2001;
    	position: fixed;
    	bottom: 0px;
    	border: 1px solid #eee;
    	text-align: center;
    	width: 100%;
    	height: 40px;
    	line-height: 40px;
    	margin: 0;
    }

    .img-brand{
    	width: auto;
    	height: 25px;
    	vertical-align: middle;
    }

</style>