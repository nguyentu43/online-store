<template>

	<div class="panel" v-loading="loadData">

		<div class="panel-heading">
			<h5>Quản lí sản phẩm</h5>
		</div>

		<div class="panel-body">
			<el-button
			type="primary"
			icon="el-icon-circle-plus-outline"
			size="small"
			class="mb-1"
			@click="$router.push({ name: 'product-create-admin' })"
			>
				Thêm sản phẩm
			</el-button>

			<div class="mb-1">
				<el-autocomplete
				  v-model="searchText"
				  :fetch-suggestions="querySearch"
				  placeholder="Sản phẩm cần tìm là?"
				  value-key="name"
				  :clearable="true"
				  @clear="handleSelect"
				  @select="handleSelect"
				>
					<el-button slot="append" @click="handleSearch">
						<i class="el-icon-search"></i>
					</el-button>
				</el-autocomplete>
			</div>

			<el-table
			:data="productData"
			border
			:row-class-name="rowClassName"
			>

				<el-table-column
				label="#"
				width="80px"
				>
					<template slot-scope="scope">
						<span class="col">{{ scope.$index + (pagination.page - 1)*pagination.perPage + 1 }}</span>
					</template>
				</el-table-column>

				<el-table-column
				label="Hiện/ẩn"
				width="80px"
				>
					<template slot-scope="scope">
						<span class="col">
							<el-checkbox v-model="scope.row.enable" @change="setActive(scope.row)"></el-checkbox>
						</span>
					</template>
				</el-table-column>

				<el-table-column
				label="Tên sản phẩm"
				width="220px"
				>
					<template slot-scope="scope">
						<span class="col">{{ scope.row.name }}</span>
					</template>
				</el-table-column>

				<el-table-column
				label="Giá (*)"
				width="120px"
				sortable
				:sort-method="sortPrice"
				>
					<template slot-scope="scope">
						<span class="col">
							{{ scope.row.skus[0].price | currency('đ', 0, { symbolOnLeft: false, spaceBetweenAmountAndSymbol: true })}}
						</span>
					</template>
				</el-table-column>

				<el-table-column
				label="Danh mục"
				width="200px"
				>
					<template slot-scope="scope">
						<span class="col">{{ scope.row.category.name }}</span>
					</template>
				</el-table-column>

				<el-table-column
				label="Thương hiệu"
				width="200px"
				>
					<template slot-scope="scope">
						<span class="col">{{ scope.row.brand.name }}</span>
					</template>
				</el-table-column>

				<el-table-column
				label="Chi tiết"
				type="expand"
				width="90px"
				>
					<template slot-scope="scope">
						<el-row :gutter="20">
							<el-col :key="sku.id" :xs="24" :sm="8" class="col" v-for="(sku, index) in scope.row.skus">
								<div class="box">
									<div><strong>SKU {{ index + 1 }}: </strong> {{ sku.sku }}</div>
									<div><strong>Phân loại: </strong>{{ sku.name }}</div>
									<div><strong>Giá: </strong> {{ sku.price |currency('đ', 0, { symbolOnLeft: false, spaceBetweenAmountAndSymbol: true }) }}</div>
									<div><strong>Số lượng: </strong> {{ sku.quantity }}</div>
									<div v-if="sku.discount">
										<div><strong>Giảm giá: </strong> {{ sku.price * (1-sku.discount.value) |currency('đ', 0, { symbolOnLeft: false, spaceBetweenAmountAndSymbol: true }) }} <el-tag size="small">{{ sku.discount.value * 100}}%</el-tag></div>
										<div><i class=el-icon-time></i> <strong>Bắt đầu:</strong> {{sku.discount.start_datetime}}</div>
										<div><i class=el-icon-time></i> <strong>Kết thúc:</strong> {{sku.discount.end_datetime}}</div>
									</div>
									<div v-else><strong>Giảm giá: </strong>Không có</div>
									<div v-if="sku.media.length > 0" v-viewer>
										<img class="img-product" v-for="item in sku.media" :src="$_storage_getImagePath(item.url)" />
									</div>
								</div>
							</el-col>
						</el-row>
					</template>
				</el-table-column>

				<el-table-column
				label="Thao tác"
				fixed="right"
				width="200px"
				>
					<template slot-scope="scope">
						<span class="col">
							<el-button
							type="success" 
							icon="el-icon-edit"
							size="small"
							@click.stop="$router.push({ name: 'product-edit-admin', params: { slug: scope.row.slug } })"
							>Sửa</el-button>
							<el-button 
							type="danger" 
							icon="el-icon-delete"
							size="small"
							@click.stop="deleteProduct(scope.row)"
							>Xoá</el-button>
						</span>
					</template>
				</el-table-column>
			</el-table>

			<div class="text-right">
				<el-pagination
				  v-if="pagination.total > 0"
				  background
				  layout="prev, pager, next"
				  :total="pagination.total"
				  @current-change="changePage"
				  @prev-click="changePage"
				  @next-click="changePage"
				  :current-page="pagination.page"
				  :page-size="pagination.perPage"
				  >
				</el-pagination>
			</div>
		</div>

	</div>

</template>

<script>

	export default{
		data(){
			return {
				productData: [],
				pagination: {
					page: 1,
					total: 0,
					perPage: 25
				},
				searchText: '',
				loadData: false
			}
		},
		methods: {
			getData(){

				this.loadData = true;
				this.axios.get(this.api.products.get(), { params: { page: this.pagination.page, keyword: this.searchText, per_page: this.pagination.perPage, skip_prop: 'enable' } }).then(res=>{
					this.productData = res.data.products;
					this.pagination.total = res.data.meta.total;
					this.loadData = false;
				})

			},
			deleteProduct(product)
			{
				this.$confirm('Bạn có muốn xoá?', 'Xoá', {
					confirmButtonText: 'Có',
					cancelButtonText: 'Không',
					type: 'warning'
				}).then(()=>{

					this.axios.delete(this.api.products.get() + '/' + product.slug )
					.then(() => {
						this.$notify({
							type: "success",
							title: "Thông báo",
							message: "Đã xoá thành công"
						})

						this.getData();
					});

				})
			},
			changePage(page)
			{
				this.pagination.page = page;
				let query = Object.assign({}, this.$route.query);
				query.page = page;
				this.$router.replace({ name: 'product-admin', query})
			},
			sortPrice(a, b)
			{
				if(a.skus[0].price > b.skus[0].price)
					return 1;
				else if(a.skus[0].price == b.skus[0].price)
					return 0;
				return -1;
			},
			querySearch(queryString, cb){

				if(queryString.trim().length < 4)
				{
					cb([]); return;
				}

                this.axios.get(this.api.products.get(), { params: { keyword: queryString, take: 6, enable: 1} }).then((res)=>{
                    cb(res.data.products)
                })
                
            },
			handleSelect(item){
				this.$router.push({ name: 'product-edit-admin', params: { slug: item.slug } })
			},
			handleSearch(){

				this.pagination.page = 1;
				let query = Object.assign({}, this.$route.query);
				query.keyword = this.searchText;
				query.page = 1;
				this.$router.replace({ name: 'product-admin', query})

			},
			setActive(row){

				if(!this.$_casl_check('product', 'update')) return;

				this.axios.patch(this.api.products.get() + '/' + row.slug, { enable: row.enable } )
				.then(()=>{

					this.$notify({
						type: 'success',
						message: row.enable ? 'Đã bật sản phẩm' : 'Đã tắt sản phẩm',
						title: 'Thông báo'
					})

				})
			},
			rowClassName({row})
			{
				if(this._.find(row.skus, item => item.quantity < 10))
					return 'sold-out';
			}
		},
		beforeRouteUpdate(to, from, next)
		{
			this.getData();
			next();
		},
		created(){
			this.pagination.page = parseInt(this.$route.query.page) || 1;
			this.searchText = this.$route.query.keyword || '';
			this.getData();
		}

	}
</script>

<style>
	.box{
		line-height: 30px;
		border: 2px solid #eee;
		padding: 10px;
	}

	.img-product{
		height: 50px;
		width: auto;
	}

	.el-table .sold-out{
		background-color: hsla(0,87%,69%,.1);
	}
</style>