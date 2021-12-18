<template>
	<editor
	:value="value"
	ref="editor"
	@input="change"
	this.api-key="g7cbz5kvpxltqmo388zs6in2ffjvjf6604khnm8nyd5w9n05"
	:init="configTinyMce"
	@onKeyDown="removeImage"
	>
	</editor>
</template>

<script>

	import Editor from '@tinymce/tinymce-vue';

	export default{
		props: {
			value: {
				required: true
			}
		},
		data(){
			return {
				configTinyMce: {
					paste_as_text: true,
					image_upload_url: this.api.upload.get(),
					plugins: [
					'advlist autolink lists link image charmap print preview anchor textcolor',
					'searchreplace visualblocks code fullscreen',
					'insertdatetime media table contextmenu paste code help wordcount'], 
					toolbar: 'insert | undo redo | fullscreen |  formatselect | bold italic backcolor  | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
					images_upload_handler:(blobInfo, success, failure)=>{
						var formData = new FormData();
						formData.append('file', blobInfo.blob(), blobInfo.filename());

						var vm = this;
						this.axios.post(this.api.upload.get(), formData, { 'Content-Type': 'multipart/form-data' }).then((res) => {
							success(res.data.url);
						});
				    }
				}
			}
		},
		methods: {
			change(val)
			{
				this.$emit('input', val);
			},
			removeImage(e)
			{
				var editor = this.$refs.editor.editor;
				if((e.keyCode == 8 || e.keyCode == 46) && editor.selection)
				{
					var selectedNode = editor.selection.getNode();
					if(selectedNode && selectedNode.nodeName == 'IMG'){

						var src = selectedNode.src;
						if(src.startsWith(this.api.root + 'storage'))
						{
							this.axios.post(this.api.upload.get() + '/remove', { name: src.substr(31) });
						}

					}
				}
			}
		},
		components: { Editor }
	}
</script>