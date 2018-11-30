<template>
	<el-menu
	background-color="#006064"
	text-color="white"
	style='height: 100%'
	:router="true"
	index="1"
	>
		<template v-for="item in menu">
			<template v-if="item.submenu">
				<el-submenu :index="item.index" v-if="$can('read', item.model)">
					<template slot="title">
						<font-awesome-icon :icon="item.icon"/>
						{{ item.name }}
					</template>
					<el-menu-item :index="sub.index" v-for="sub in item.submenu" :route="sub.route" :key="sub.index" v-if="$can('read', item.model)">
						<template slot="title">
							<font-awesome-icon :icon="sub.icon"/>
							{{ sub.name }}
						</template>
					</el-menu-item>
				</el-submenu>
			</template>
			<template v-else>
				<el-menu-item :route="item.route" :index="item.index" v-if="$can('read', item.model)">
					<template slot="title">
						<font-awesome-icon :icon="item.icon"/>
						{{ item.name }}
					</template>
				</el-menu-item>
			</template>
		</template>
	</el-menu>
</template>

<script>
	
	export default {
		data(){
			return {
				menu: [
					{ index: "2", name: 'Dashbroad', icon: 'home', route: { name: 'dashbroad-admin' }, model: 'dashbroad'},
					{ index: "3", model: 'category', name: 'Danh mục', route: { name: 'category-admin' }, icon: 'sitemap'},
					{ index: "4", model: 'product', name: 'Sản phẩm', icon: 'clipboard-list', submenu: [
							{ index: "4-1", name: 'Quản lí', route: { name: 'product-admin' }, icon: 'box', model: 'product'},
							{ index: "4-2", name: 'Loại sản phẩm', route: { name: 'producttype-admin' }, icon: 'bookmark', model: 'producttype'},
							{ index: "4-3", name: 'Thương hiệu', route: { name: 'brand-admin' }, icon: 'city', model: 'brand'}
						]},
					{ index: "5", model: 'order', name: 'Đơn hàng', route: { name: 'order-admin' }, icon: 'boxes' },
					{ index: "10", model: 'discount-code', name: 'Mã giảm giá', route: { name: 'discount-code-admin' }, icon: 'dollar-sign' },
					{ index: "6", model: 'campaign', name: 'Banner', route: { name: 'campaign-admin' }, icon: 'columns' },
					{ index: "7", model: 'statistical', name: 'Báo cáo, thống kê', route: { name: 'report-admin' }, icon: 'chart-bar' },
					{ index: "8", model: 'user', name: 'Tài khoản', route: { name: 'user-admin' }, icon: 'user-cog' },
					{ index: "9", model: 'role', name: 'Phân quyền', route: { name: 'role-admin' }, icon: 'user-lock' }
					
					
				]
			}
		}
	}

</script>