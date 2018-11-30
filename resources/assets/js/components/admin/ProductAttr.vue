<template>
	<div class="panel panel-body">
		<el-form ref="formAttr" :model="formAttr" :rules="formAttrRules" inline>
			<el-form-item prop="product_attribute_id" label="Thuộc tính">
				<el-select size="small" filterable v-model="formAttr.product_attribute_id">
					<el-option
					v-for="item in attrOptions"
					:key="item.id"
					:value="item.id"
					:label="item.name"
					></el-option>
				</el-select>
			</el-form-item>
			<el-form-item prop="value" label="Giá trị">
				<el-input size="small" v-model="formAttr.value"></el-input>
			</el-form-item>
			<el-form-item>
				<el-button type="primary" size="small" icon="el-icon-circle-plus-outline" @click="addAttr">Thêm</el-button>
			</el-form-item>
		</el-form>
		<el-table
		:data="attributes"
		>
			<el-table-column
			label="#"
			width="80px"
			>
				<template slot-scope="scope">
					{{ scope.$index + 1 }}
				</template>
			</el-table-column>

			<el-table-column
			label="Thuộc tính"
			>
				<template slot-scope="scope">
					{{ scope.row.name }}
				</template>
			</el-table-column>

			<el-table-column
			label="Giá trị"
			>
				<template slot-scope="scope">
					<el-input size="small" v-model="scope.row.pivot.value">
						<el-button slot="append" size="small" type="success" @click="changeAttr(scope.row.pivot)" icon="el-icon-edit">Cập nhật</el-button>
					</el-input>
				</template>
			</el-table-column>

			<el-table-column
			label="Thao tác"
			>
				<template slot-scope="scope">
					<el-button @click="deleteAttr(scope.row.pivot.product_attribute_id)" type="danger" icon="el-icon-delete" size="small">Xoá
					</el-button>
				</template>
			</el-table-column>

		</el-table>
	</div>
</template>

<script>

	export default{
		props: ['slug'],
		data(){

			return {
				attributes: [],
				formAttr: {},
				formAttrRules: {
					product_attribute_id: [ { required: true, trigger: 'blur' }],
					value: [ { required: true, trigger: 'blur' } ]
				},
				attrOptions: []
			}
			
		},
		methods: {
			getAttrData(){
				this.axios.get(this.api.products.getAttributes(this.slug))
				.then(res => this.attributes = res.data)
				.then(() => this.getAttrOptions());
			},
			getAttrOptions(){
					this.axios.get(this.api.products.getAttributes(this.slug), {params: { filter: 'not_in' }})
					.then(res=>{
						this.attrOptions = res.data;
					});
			},
			addAttr(){

				this.$refs.formAttr.validate(valid=>{

					if(!valid) return;

					this.axios.post(this.api.products.getAttributes(this.slug), this.formAttr)
					.then((res) => {

						this.$refs.formAttr.resetFields();

						this.$notify({
							type: 'success',
							message: 'Đã thêm thuộc tính',
							title: 'Thông báo'
						})

						this.getAttrData();
						this.getAttrOptions();
					})
				})
			},
			changeAttr(data){
				this.axios.put(this.api.products.getAttributes(this.slug) + '/' + data.product_attribute_id, data)
				.then((res) => {
					this.$notify({
						type: 'success',
						message: 'Đã cập nhật thuộc tính',
						title: 'Thông báo'
					})

					this.getAttrData();
				})
			},
			deleteAttr(product_attribute_id){

				this.$confirm('Bạn có muốn xoá?', 'Xoá', {
					confirmButtonText: 'Có',
					cancelfirmButtonText: 'Không',
					type: 'warning'
				}).then(() => {
					this.axios.delete(this.api.products.getAttributes(this.slug) + '/' + product_attribute_id)
					.then((res) => {
						this.$notify({
							type: 'success',
							message: 'Đã xoá thuộc tính',
							title: 'Thông báo'
						})

						this.getAttrData();
						this.getAttrOptions();
					})
				})

				
			}
		},
		mounted(){
			this.getAttrData();
		}
	}
</script>