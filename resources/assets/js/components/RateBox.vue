<template>
	<div v-loading="rate.loading" class="rate-box">
		<el-row v-if="rate_product">
			<el-col :sm="12">
			<div class="rate-total text-center">
				<el-rate
				v-model="rate_product"
				disabled
				>
				</el-rate>
				<div class="rate-value">
					{{ rate_product.toFixed(1) }}/5
				</div>
				<div class="text-muted">({{ rate.total }} lượt đánh giá)</div>
			</div>
			</el-col>
			<el-col :sm="12">
					<div v-for="item, index in rate.group">
						<el-col :sm="3" :xs="5">
							<i class="el-icon-star-on star-color mt-1"></i> {{index + 1}} <span class="text-muted">({{ item }})</span>
						</el-col>
						<el-col :sm="21" :xs="19">
							<el-progress 
							:text-inside="true" 
							:stroke-width="18" 
							:percentage="Math.round((item / rate.total) * 100)"
							:status="index > 2 ? 'success' : 'exception'"
							class="mt-1"
							></el-progress>
						</el-col>
					</div>
			</el-col>
		</el-row>
		<div v-else class="text-center">
			<img class="img-empty" :src="$_storage_getImageFromApp('RATE_EMPTY')"/><br/>
			<el-tag>Chưa có đánh giá nào</el-tag>
		</div>

		<div v-if="rate.list.length > 0">
			<div class="text-right mt-1">
				<el-select
				v-model="order"
				@change="getData"
				size="small"
				placeholder="Sắp xếp đánh giá"
				>
					<el-option
					:value="'-date'"
					:label="'Mới nhất'"
					>
					</el-option>
				</el-select>
			</div>

			<div class="rate-list">
				<rate-item
				v-for="item in rate.list"
				:rate="item"
				:key="item.id"
				:slug="slug"
				@reload="getData"
				></rate-item>
			</div>
			<div class="pagination">
				<div class="text-right">
					<el-pagination
					background
					layout="prev, pager, next"
					:total="rate.total"
					:page-size="rate.per_page"
					:current-page.sync="rate.current_page"
					@current-change="getData"
					@next-click="getData"
					@prev-click="getData"
					>
					</el-pagination>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	import RateItem from './RateItem.vue';

	export default{
		props: {
			slug: String,
		},
		data(){
			return {
				rate: {
					total: 1,
					list: [],
					loading: false,
					group: [],
					current_page: 1
				},
				rate_product: 0,
				order: null
			}
		},
		methods: {
			getData()
			{
				this.rate.loading = true;
				this.axios.get(this.api.rate.get(), { params: { page: this.rate.current_page, per_page: this.rate.per_page, order: this.order, product_slug: this.slug }})
				 .then(res => {
				 	this.rate.list = res.data.list.data;
				 	this.rate.total = res.data.list.total;
				 	this.rate.loading = false;

				 	this.rate.group = [];
					let group = res.data.group;
					for(let i = 1; i<=5; ++i)
					{
						let x = _.find(group, { rate: i });
						if(x)
							this.rate.group.push(x.count);
						else
							this.rate.group.push(0);
					}
					this.rate_product = res.data.rate_product;
				});
			}
		},
		created(){
			this.getData();
		},
		components: { RateItem }
	}
</script>

<style scoped>

	.rate-value{
		font-size: 30px;
		font-weight: bold;
		margin-top: 10px;
	}

	.rate-box{
		margin: 0 20px;
	}

	@media screen and (max-width: 768px)
	{
		.rate-box{
			margin: 0 10px;
		}
	}

	.rate-edit, .rate-list, .pagination{
		margin-top: 10px;
		margin-bottom: 10px;
	}

	.rate-list{
		border: 1px solid #eee;
		border-radius: 10px;
	}
	
</style>