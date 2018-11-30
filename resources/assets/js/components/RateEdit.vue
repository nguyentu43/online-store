<template>
	<el-row class="rate-edit">
		<div class="mb-1"><strong>Đánh giá sản phẩm:</strong></div>
		<template v-if="$can('create', 'rate')">
			<el-col>
				<el-tag><font-awesome-icon icon="user"/> {{ user.name }}</el-tag>
			</el-col>
			<el-col>
				<el-rate
				v-model="rate.rate"
				show-score
				class="mt-1"
				>
				</el-rate>
				<el-input 
				v-model="rate.comment"
				class="mt-1"
				type="textarea"
				placeholder="Để lại lời đánh giá"
				></el-input>
				<div class="mt-1 mb-1">Hình ảnh sản phẩm: </div>
				<file-upload :limit="5" @success="successUpload" @remove="removeUpload" :file-list="fileList"/>
				<el-button class="mt-1" size="small" icon="el-icon-check" type="success" @click="addRate">
					Gửi đánh giá
				</el-button>
				<el-button v-if="data" size="small" @click="$emit('cancel')">
					Huỷ
				</el-button>
			</el-col>
		</template>
		<div v-else>
			Đăng nhập để thực hiện tính năng này
		</div>
	</el-row>
</template>

<script>
	import FileUpload from './FileUpload.vue'

	export default{
		props: {
			data: Object
		},
		data(){
			return {
				rate: {},
				edit: false,
				fileList: []
			}
		},
		computed:{
			user(){
				return this.$store.state.user;
			}
		},
		created(){
			if(this.data)
			{
				this.rate = Object.assign({}, this.data);

				if(this.rate.comment)
					this.rate.comment = this.rate.comment.content;

				if(!this.rate.images)
					this.rate.images = [];
				else
				{
					this.rate.images.forEach(item => {
						this.fileList.push({name: item, url: this.$_storage_getImagePath(item) } );
					});
				}
			}
		},
		methods: {
			addRate()
			{
				if(!this.user)
				{
					this.$notify({
						type: 'warning',
						message: 'Bạn chưa đăng nhập',
						title: 'Thông báo'
					});
				}

				let data = {...this.rate, user_id: this.user.id };

				if(this.rate.id)
				{
					this.axios.put(this.api.rate.get() + '/' + this.rate.id, data)
					.then(() => {
						this.$notify({
							type: 'success',
							message: 'Cảm ơn bạn đã đánh giá sản phẩm',
							title: 'Thông báo'
						});

						this.$emit('send-success');
					});
				}
				else
				{
					if(this.rate.rate)
					{
						this.axios.post(this.api.rate.get(), data)
						.then(() => {

							this.$notify({
								type: 'success',
								message: 'Cảm ơn bạn đã đánh giá sản phẩm',
								title: 'Thông báo'
							});

							this.$emit('send-success');
						});
					}
					else
					{
						this.$notify({
							type: 'warning',
							message: 'Bạn chưa chọn đánh giá',
							title: 'Thông báo'
						});
					}
				}
			},
			successUpload(res)
			{
				this.rate.images.push(res.path);
			},
			removeUpload(file)
			{
				this._.remove(this.rate.images, item => item == file.name);
			}
		},
		components: { FileUpload }
	}
</script>

<style scoped>

	.rate-edit{
		border: 1px solid rgb(2, 136, 209);
		border-radius: 10px;
		padding: 10px;
		margin: 15px 0;
	}

</style>