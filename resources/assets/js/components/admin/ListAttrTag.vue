<template>
	
	<div>
	Các thuộc tính: 
		<el-tag
		  :key="tag.id"
		  v-for="tag in attributes"
		  closable
		  :disable-transitions="false"
		  @close="detachAttribute(tag)">
		  {{ tag.name }}
		</el-tag>

		<el-select
		class="ml-1"
		size="small"
		allow-create
		filterable
		value-key="name"
		v-model="select"
		placeholder="Thêm thuộc tính mới"
		@change="addAttr"
		>
			<el-option
			v-for="item in attributeOptions"
			:key="item.id"
			:label="item.name"
			:value="item"
			></el-option>
		</el-select>
	</div>

</template>

<script>

	export default{
		props: ['ptype'],
		data(){
			return {
				attributes: [],
				attributeOptions: [],
				select: {}
			}
		},
		methods: {
			detachAttribute(attr)
			{
				this.axios.delete(this.api.products.types.getDetach(this.ptype.id) + '/' + attr.id).then(() => {

					this.$notify({
						title: 'Thông báo',
						message: 'Đã bỏ thuộc tính thành công',
						type: 'success'
					});

					this.getData();

				})
			},
			attachAttribute(attr)
			{
				this.axios.put(this.api.products.types.getAttach(this.ptype.id) + '/' + attr.id).then(() => {

					this.$notify({
						title: 'Thông báo',
						message: 'Đã thêm thuộc tính thành công',
						type: 'success'
					});

					this.getData();
				})
			},
			addAttr(val)
			{
				if(typeof(val) == 'object')
				{
					this.attachAttribute(val);
				}
				else
				{
					this.axios.post(this.api.attributes.get(), { name: val, product_type_id: this.ptype.id })
					.then(res=>{

						this.$notify({
							title: 'Thông báo',
							message: 'Đã thêm thuộc tính thành công',
							type: 'success'
						});

						this.getData();
						
					})
				}

				this.select = {};
			},
			getData()
			{
				this.axios.get(this.api.attributes.get(), { params: { product_type_id: this.ptype.id } })
				.then((res) => {
					this.attributes = res.data;
				})
				.then(() => {

					this.axios.get(this.api.attributes.get())
					.then((res)=>{
						this.attributeOptions = this._.filter(res.data, item => {

							return !this._.find(this.attributes, { id: item.id });

						});
					})

				})

				
			}
		},
		created(){
			this.getData();
		}
	}

</script>