<template>
	<div v-if="campaign">
		<div class="panel">
			<div class="panel-heading highlight">
				<h5> {{ campaign.name }} </h5>
			</div>
			<div class="banner" :style="{backgroundImage: 'url(' + $_storage_getImagePath(campaign.banner) + ')', height: $mq == 'xs' ? '150px' : '300px' }">
			</div>
			<div class="panel-body">
				<div class="text-center">
					<countdown :size="$mq != 'xs' ? 'lg' : 'md' " :start="campaign.start_datetime" :end="campaign.end_datetime"/>
				</div>
				<div v-if="campaign.description" v-html="campaign.description"></div>
			</div>
		</div>

		<div class="panel">
			<div class="panel-heading highlight">
				<h5> Các sản phẩm </h5>
			</div>
			<el-row class="panel-body">
				<template v-if="campaign.products.length > 0">
			        <el-col :sm="8" :xs="12" :md="4" v-for="p in campaign.products" :key="p.id">
			            <product-item :data="p"></product-item>
			        </el-col>
				</template>
			</el-row>
		</div>

		<div class="panel">
			<div class="panel-heading highlight">
				<h5>Bình luận</h5>
			</div>
			<div class="panel-body">
				<comment-box
				:slug="campaign.slug"
				type="campaign"
				></comment-box>
			</div>
		</div>

	</div>
	<div v-else>
		<div class="panel panel-body text-center" v-if="notfound"><h5>Banner này không tồn tại</h5></div>
	</div>
</template>

<script>
	import TimeMixin from '../mixins/time.js';
	import ProductItem from '../components/ProductItem.vue';
	import CommentBox from '../components/CommentBox.vue';
	import Countdown from '../components/Countdown.vue';

	export default {
		mixins: [ TimeMixin ],
		data(){
			return {
				campaign: null,
				notfound: false
			}
		},
		methods:{
			init(){
				this.axios.get(this.api.campaigns.get() + '/' + this.$route.params.slug).then(res=>{
					this.campaign = res.data;
					this.$_app_setTitle(this.campaign.name);
				})
				.catch(() => this.notfound = true);
			}
		},
		created(){
			this.init();
		},
		components: { ProductItem, CommentBox, Countdown }
	}
</script>

<style scoped>
	.banner{
		background-position: center;
		background-repeat: no-repeat;
		background-size: cover;
	}
</style>