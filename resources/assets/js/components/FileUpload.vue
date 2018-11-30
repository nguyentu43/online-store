<template>
	<el-upload
	  list-type="picture-card"
	  :file-list="fileList"
	  :limit="limit"
	  :action="url"
	  :headers="headers"
	  :on-success="successUpload"
	  :on-remove="removeUpload"
	  :on-error="errorUpload"
	  ref="upload"
	  >
	  <i class="el-icon-plus"></i>
	</el-upload>
</template>

<script>

	export default{
		props: {
			fileList: Array,
			limit: Number
		},
		data(){
			return {
				url: this.api.upload.get(),
				headers: {
					'Authorization': 'Bearer ' + localStorage.getItem('token')
				}
			}
		},
		methods: {
			successUpload(res, file, fileList)
			{
				this.$emit('success', res);
			},
			removeUpload(file, fileList)
			{
				let name = file.name;
				if(this._.find(this.fileList, { name }) == -1)
					name = 'images/' + name; 
				this.axios.post(this.api.upload.get() + '/remove', { name })
				.then(() => {
					this.$emit('remove', { name });
				})
			},
			errorUpload(err, file, fileList)
			{
				this.$notify({
					type: "error",
					title: 'Lỗi',
					message: 'Lỗi tải file lên'
				})
			}
		}
	}
</script>