<template>
	<el-row class="rate-item">
		<template v-if="!edit">
			<el-col>
				<el-tag>
					<font-awesome-icon icon="user"/> {{ rate.user_name }} | 
					<span class="meta-info text-muted">
						<i class="el-icon-time"></i> {{ rate.updated_at }}
						<span class="text-success" v-if="rate.created_at != rate.updated_at">
							 | <i class="el-icon-edit"></i> Đã sửa
						</span>
					</span>
				</el-tag>
			</el-col>
			<el-col>
				<el-tag type="success" class="mt-1">Loại: {{ rate.sku_name }}</el-tag>
				<el-rate
				show-score
				disabled
				v-model="rate.rate"
				class="mt-1"
				>
				</el-rate>
				<div v-if="rate.comment" class="mt-1">{{ rate.comment.content }}</div>
				<div v-if="rate.images" v-viewer>
					<img class="img-product" v-for="url in rate.urls" :src="url" />
				</div>
			</el-col>
			<el-col>
				<el-button
				type="text"
				size="small"
				@click="editRate"
				v-if="$can('update', 'rate', rate)"
				>Sửa</el-button>
				<el-button
				type="text"
				size="small"
				@click="deleteRate"
				v-if="$can('delete', 'rate', rate)"
				>Xoá</el-button>
			</el-col>
		</template>
		<template v-else>
			<rate-edit
			:data="rate"
			@send-success="$emit('reload');edit=false;"
			@cancel="edit=false;"
			>
			</rate-edit>
		</template>
	</el-row>
</template>

<script>

	import RateEdit from './RateEdit.vue';

	export default{
		props: {
			rate: Object
		},
		data() {
			return {
				edit: false
			}
		},
		computed:{
			user()
			{
				return this.$store.state.user;
			}
		},
		methods: {
			editRate(){
				this.edit = true;
			},
			deleteRate(){
				this.$confirm('Bạn có muốn xoá?', 'Xoá', {
					confirmButtonText: 'Có',
					cancelButtonText: 'Không',
					type:'warning'
				}).then(() => {

					this.axios.delete(this.api.rate.get() + '/' + this.rate.id)
					.then(() => {

						this.$notify({
							type: 'success',
							message: 'Đã xoá đánh giá',
							title: 'Thông báo'
						});

						this.$emit('reload');

					});
				});
			}
		},
		components: { RateEdit }
	}
</script>

<style scoped>

	.rate-item{
		padding: 10px;
		border-bottom: 1px solid #eee;
	}

	.rate-item:last-child{
		border-bottom: 0;
	}

	.img-product{
		width: 70px;
		height: 70px;
		object-fit: fill;
		border: 1px solid #eee;
		margin-right: 5px;
		margin-top: 5px;
		padding: 5px;
	}
	
</style>