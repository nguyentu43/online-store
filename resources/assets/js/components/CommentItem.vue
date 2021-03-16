<template>
	<el-row class="comment-item" v-if="show">
		<template v-if="!data.is_edit">
			<comment :data="data" @rely="relyComment" @edit="editComment(data)" @delete="deleteComment(data.id)" />
		</template>
		<comment-edit
		v-else
		:type="type"
		@send-success="reload"
		:data="data"
		@cancel="data.is_edit=false;"
		></comment-edit>
		<el-col :sm='{ span: 21, offset: 3}' :xs="{ span: 21, offset: 3 }">
			<el-row class="sub-comment" 
			v-for="item in data.children"
			:key="item.id"
			>
				<template v-if="!item.is_edit">
					<comment :data="item" @rely="relyComment" @edit="editComment(item)" @delete="deleteComment(item.id)" />
				</template>
				<template v-else>
					<comment-edit
					:type="type"
					@send-success="reload"
					:data="item"
					></comment-edit>
				</template>
			</el-row>
		</el-col>
		<el-col :sm='{ span: 22, offset: 2 }' :xs="{ span: 22, offset: 2 }">
			<el-row class="sub-comment" v-if="show_reply">
				<comment-edit
				:type="type"
				:parent_id="data.id"
				@send-success="reload"
				@cancel="show_replay=false;"
				></comment-edit>
			</el-row>
		</el-col>
	</el-row>
</template>

<script>

	import CommentEdit from './CommentEdit.vue'
	import Vue from 'vue'

	let Comment = {
		template: `
		<div>
			<el-col>
				<el-tag><font-awesome-icon icon="user"/> {{ data.user_name }} | 
				<span class="meta-info text-muted">
					<i class="el-icon-time"></i> {{ data.updated_at }}
					<span class="text-success" v-if="data.created_at != data.updated_at">
						 | <i class="el-icon-edit"></i> Đã sửa
					</span>
				</span></el-tag>
			</el-col>
			<el-col>
				<div class="mt-1">{{ data.content }}</div>
			</el-col>
			<el-col>
				<el-button
				type="text"
				size="small"
				@click="$emit('rely')"
				>Trả lời</el-button>
				<template>
					<el-button
					type="text"
					size="small"
					@click="$emit('edit')"
					v-if="$can('update', 'comment', data)"
					>Sửa</el-button>
					<el-button
					type="text"
					size="small"
					@click="$emit('delete')"
					v-if="$can('delete', 'comment', data)"
					>Xoá</el-button>
				</template>
			</el-col>
		</div>
		`,
		props: [ 'data' ],
		computed: {
			user(){
				return this.$store.state.user;
			}
		}
	}

	export default{
		props: {
			data: Object,
			type: Object
		},
		data(){
			return {
				show_reply: false,
				show: true
			}
		},
		computed:{
			user(){
				return this.$store.state.user;
			}
		},
		methods:{
			relyComment()
			{
				this.show_reply = true;
			},
			reload()
			{
				this.$emit('reload'); 
				this.show_reply = false;
			},
			editComment(data){
				data.is_edit = true;
				this.show = false;
				Vue.nextTick(() => this.show = true);
			},
			deleteComment(id)
			{
				this.$confirm('Bạn có muốn xoá?', 'Xoá', {
					confirmButtonText: 'Có',
					cancelButtonText: 'Không',
					type:'warning'
				}).then(() => {

					this.axios.delete(this.api.comments.get() + '/' + id).then(()=>{
						this.$notify({
							title:'Thông báo',
							message: 'Bạn đã xoá bình luận',
							type: 'success'
						})
						this.$emit('reload'); 
					});
				});
			}
		},
		components: { 
			CommentEdit, Comment
		}
	}
</script>

<style scoped>

	.comment-item{
		border-bottom: 1px solid #eee;
		padding: 10px;
	}

	.comment-item:last-child{
		border-bottom: 0;
	}

	.sub-comment{
		border-top: 1px solid #eee;
		padding: 10px;
	}

</style>