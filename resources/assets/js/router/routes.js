import Vue from 'vue'
import VueRouter from 'vue-router'
import Cart from '../views/Cart.vue'
import NotFound from '../views/NotFound.vue'
import Store from '../views/Store.vue'
import Index from '../views/Index.vue'
import Checkout from '../views/Checkout.vue'
import UserSetting from '../views/UserSetting.vue'
import UserInfo from '../views/UserInfo.vue'
import UserAddressBooks from '../views/UserAddressBooks.vue'
import UserOrder from '../views/UserOrder.vue'
import Category from '../views/Category.vue'
import SingleProduct from '../views/SingleProduct.vue'
import SearchProduct from '../views/SearchProduct.vue'
import RequestReset from '../views/RequestReset.vue'
import ResetPassword from '../views/ResetPassword.vue'
import Login from '../views/Login.vue'
import Register from '../views/Register.vue'
import Campaign from '../views/Campaign.vue'

import Admin from '../views/admin/Admin.vue'
import Dashboard from '../views/admin/Dashboard.vue'
import CategoryManager from '../views/admin/CategoryManager.vue'
import ProductTypeManager from '../views/admin/ProductTypeManager.vue'
import BrandManager from '../views/admin/BrandManager.vue'
import ProductManager from '../views/admin/ProductManager.vue'
import SingleProductAdmin from '../views/admin/SingleProduct.vue'
import OrderManager from '../views/admin/OrderManager.vue'
import UserManager from '../views/admin/UserManager.vue'
import CampaignManager from '../views/admin/CampaignManager.vue'
import Report from '../views/admin/Report.vue'
import RoleManager from '../views/admin/RoleManager.vue'
import DiscountCodeManager from '../views/admin/DiscountCodeManager.vue'
import DenyPermission from '../views/DenyPermission.vue'

Vue.use(VueRouter)

const routes = [
	{ 
		path: '/', component: Store,
		children: [
			{ path: '/', component: Index, name: 'home', meta: { title: 'Trang chủ' }},
			{ path: '/cart', component: Cart, name: 'cart', meta: { title: 'Giỏ hàng' }},
			{ path: '/checkout', component: Checkout, name: 'checkout', meta: { title: 'Đặt hàng' }},
			{ 
				path: '/user', component: UserSetting,
				children: [
					{ path: '/', component: UserInfo, name: 'userinfo', meta: { title: 'Thông tin tài khoản', permission: 'user.update.owner' }},
					{ path: 'addressbooks', component: UserAddressBooks, name: 'useraddressbooks', meta: { title: 'Sổ địa chỉ', permission: 'user.update.owner' }},
					{ path: 'orders', component: UserOrder, name: 'userorders', meta: { title: 'Danh sách đơn hàng', permission: 'order.update.owner' }}
				]
			},
			{ path: '/categories/:slug', component: Category, name: 'categories', meta: { title: 'Danh mục' }},
			{ path: '/products/:slug', component: SingleProduct, name: 'product', meta: { title: 'Sản phẩm' } },
			{ path: '/campaign/:slug', component: Campaign, name: 'campaign', meta: { title: 'Khuyến mại' } },
			{ path: '/search', component: SearchProduct, name: 'search', meta: { title: 'Tìm kiếm' } },
			{ path: '/login', component: Login, name: 'login', meta: { title: 'Đăng nhập' } },
			{ path: '/register', component: Register, name: 'register', meta: { title: 'Đăng ký' } },
			{ path: '/request-reset', component: RequestReset, name: 'request-reset', meta: { title: 'Yêu cầu khôi phục mật khẩu' } },
			{ path: '/reset-password/:token', component: ResetPassword, name: 'reset-password', meta: { title: 'Khôi phục mật khẩu' } }
		]
		
	},
	{ 
		path: '/admin', component: Admin,
		children: [
			{ path: '/', component: Dashboard, name:'dashboard-admin', meta: { title: 'Dashboard', permission: 'dashboard.read' } },
			{ path: 'category-manager', component: CategoryManager, name:'category-admin', meta: { title: 'Quản lí danh mục', permission: 'category.read' } },
			{ path: 'product-types', component: ProductTypeManager, name:'producttype-admin', meta: { title: 'Quản lí loại sản phẩm', permission: 'producttype.read'} },
			{ path: 'brands', component: BrandManager, name:'brand-admin', meta: { title: 'Quản lí nhãn hàng', permission: 'brand.read' } },
			{ path: 'product-manager', component: ProductManager, name:'product-admin', meta: { title: 'Quản lí sản phẩm', permission: 'product.read' } },
			{ path: 'product-edit/:slug', component: SingleProductAdmin, name:'product-edit-admin', meta: { title: 'Chỉnh sửa sản phẩm', permission: 'product.read' } },
			{ path: 'product-create', component: SingleProductAdmin, name:'product-create-admin', meta: { title: 'Tạo sản phẩm', permission: 'product.create' } },
			{ path: 'order-manager', component: OrderManager, name:'order-admin', meta: { title: 'Quản lí đơn hàng', permission: 'order.read' } },
			{ path: 'user-manager', component: UserManager, name:'user-admin', meta: { title: 'Quản lí tài khoản', permission: 'order.read' } },
			{ path: 'campaign-manager', component: CampaignManager, name:'campaign-admin', meta: { title: 'Quản lí banner', permission: 'campaign.read' } },
			{ path: 'report', component: Report, name:'report-admin', meta: { title: 'Báo cáo, thống kê', permission: 'statistical.read'} },
			{ path: 'role-manager', component: RoleManager, name:'role-admin', meta: { title: 'Phân quyền', permission: 'role.read' } },
			{ path: 'discount-code-manager', component: DiscountCodeManager, name:'discount-code-admin', meta: { title: 'Mã giảm giá', permission: 'discount-code.read' } },
			{ path: '/deny', component: DenyPermission, name: 'deny', meta: { title: 'Deny' }},
		],
		meta: {
			permission: 'admin-page.read'
		}
	},
	{ path: '*', name: 'notfound', component: NotFound, meta: { title: 'Lỗi không tìm thấy trang' }}
]

export default new VueRouter({
	mode: 'history',
	routes
});