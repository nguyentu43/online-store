<template>
	<div v-loading="comments.loading" class="comment-box">
		<comment-edit
		:type="{ name: type, slug: slug }"
		@send-success="getData"
		>
		</comment-edit>

		<div v-if="comments.list">
			<div class="text-right">
				<el-select
				v-model="order"
				@change="getData"
				size="small"
				placeholder="Sắp xếp bình luận"
				>
					<el-option
					:value="'-date'"
					:label="'Mới nhất'"
					>
					</el-option>
				</el-select>
			</div>

			<div class="comment-list">
				<comment-item
				v-for="item in comments.list"
				:data="item"
				:key="item.id"
				:type="{name: type, slug: slug}"
				@reload="getData"
				>
				</comment-item>
			</div>
			<div class="pagination">
				<div class="text-right">
					<el-pagination
					background
					layout="prev, pager, next"
					:total="comments.total"
					:page-size="comments.per_page"
					:current-page.sync="comments.current_page"
					@current-change="getData"
					@next-click="getData"
					@prev-click="getData"
					>
					</el-pagination>
				</div>
			</div>
		</div>
		<div v-else class="text-center">
			<img class="img-empty" :src="$_storage_getImageFromApp('COMMENT_EMPTY')"/><br/>
			<el-tag>Chưa có bình luận nào</el-tag>
		</div>
	</div>
</template>

<script>

	import CommentEdit from './CommentEdit.vue'
	import CommentItem from './CommentItem.vue'

	export default{
		props: {
			slug: String,
			type: {
				type: String,
				default: 'product'
			}
		},
		data(){
			return {
				comments: {
					total: 1,
					list: null,
					current_page: 1,
					loading: false
				},
				order: null
			}
		},
		methods: {
			getData(){
				this.comments.loading = true;
				this.axios.get(this.api.comments.get(), { params: { type: this.type, slug: this.slug, page: this.comments.current_page, per_page: this.comments.per_page, order: this.order } })
				.then((res) => {
					this.comments.list = res.data.data;
					this.comments.total = res.data.meta.total;
					this.comments.loading = false;
				});
			}
		},
		created(){
			this.getData();
		},
		components: { CommentEdit, CommentItem }
	}
</script>

<style scoped>
	.comment-box{
		margin: 0 20px;
	}

	@media screen and (max-width: 768px)
	{
		.comment-box{
			margin: 0 10px;
		}
	}

	.comment-list, .comment-edit, .pagination{
		margin-top: 10px;
		margin-bottom: 10px;
	}

	.comment-list{
		border: 1px solid #eee;
		border-radius: 10px;
	}
</style>