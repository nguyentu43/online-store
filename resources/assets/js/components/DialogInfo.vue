<template>
	<div>
		<el-button
		@click="show = true"
		:type="typeButton"
		>{{ title }}</el-button>
		
		<el-dialog
		:title="'Thông tin về ' + title"
		:visible.sync="show"
		:fullscreen="$mq=='xs'"
		>
			<div v-html="content"></div>
		</el-dialog>
	</div>
</template>

<script>
	export default{
		props: {
			title: {
				type: String,
				required: true
			},
			content: {
				type: String,
				required: true
			},
			typeButton: {
				type: String,
				default: 'text'
			}
		},
		data(){
			return {
				show: false
			}
		}
	}
</script>