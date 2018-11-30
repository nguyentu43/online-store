<template>
	<el-row class="comment-edit">
		<template v-if="$can('create', 'comment')">
			<el-col>
				<el-tag>
					<font-awesome-icon v-if="user" icon="user"/> {{ user.name }}
				</el-tag>
			</el-col>
			<el-col>
				<el-input 
				v-model="comment.content"
				class="mt-1"
				type="textarea"
				placeholder="Để lại lời bình luận"
				></el-input>
				<div class="mt-1">
					<el-button  size="small" icon="el-icon-check" type="success" @click="sendComment">Gửi bình luận</el-button>
					<el-button v-if="data" size="small" @click="$emit('cancel')">Huỷ</el-button>
				</div>
			</el-col>
		</template>
		<div v-else>
			Đăng nhập để thực hiện tính năng này
		</div>
	</el-row>
</template>

<script>
	export default{
		props: {
			type: Object,
			parent_id: Number,
			data: Object
		},
		data(){
			return {
				comment: {}
			}
		},
		created(){
			if(this.data)
				this.comment = this.data;
		},
		computed:{
			user(){
				return this.$store.state.user;
			}
		},
		methods: {
			sendComment()
			{
				if(!this.comment.content)
				{
					this.$notify({
						title: 'Thông báo',
						message: 'Bạn chưa nhập bình luận',
						type: 'warning'
					});
					return;
				}

				let data = this.comment;

				data.type = this.type.name;
				data.slug = this.type.slug;

				if(this.comment.id)
				{
					this.axios.put(this.api.comments.get() + '/' + data.id , data).then((res)=>{

						this.$notify({
							title: 'Thông báo',
							message: 'Bạn đã cập nhật lời bình luận',
							type: 'success'
						});

						this.$emit('send-success');
					});
				}
				else
				{
					data.parent_id = this.parent_id;
					data.user_id = this.user.id;

					this.axios.post(this.api.comments.get(), data).then((res)=>{

						this.$notify({
							title: 'Thông báo',
							message: 'Bạn đã gửi lời bình luận',
							type: 'success'
						});

						this.comment = {};

						this.$emit('send-success');
					});
				}
			}
		}
	}
</script>

<style scoped>

	.comment-edit{
		border: 1px solid rgb(2, 136, 209);
		border-radius: 10px;
		padding: 10px;
	}
	
</style>