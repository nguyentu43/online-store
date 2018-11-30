import Vue from 'vue';
import App from './views/App.vue';
import router from './router/routes.js';
import store from './vuex/store.js';
import { library } from '@fortawesome/fontawesome-svg-core';
import { faCartPlus, faPlusSquare, faShoppingCart, faSearch, 
	faUser, faSignInAlt, faSignOutAlt, faShoppingBag, faEye, 
  faTrash, faAngleDoubleRight, faAngleDown, faBars, faAddressCard,
  faCog, faFilter, faHome, faSitemap, faClipboardList, faBox, faUserCog,
  faChartBar, faBoxes, faBookmark, faCity, faAddressBook, faEnvelope,
  faMoneyBill, faShareSquare, faColumns, faGift, faFire, faDollarSign,
  faUserLock} from '@fortawesome/free-solid-svg-icons';
import { faGoogle, faFacebook, faGooglePlusSquare, faFacebookSquare,
		faTwitterSquare, faPinterestSquare } from '@fortawesome/free-brands-svg-icons';
import { faComments, faClock } from '@fortawesome/free-regular-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import Vue2Filters from 'vue2-filters';
import axios from 'axios';
import './axios/config.js';
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
import 'element-ui/lib/theme-chalk/display.css';
import locale from 'element-ui/lib/locale/lang/vi'
import 'viewerjs/dist/viewer.css';
import Viewer from 'v-viewer';
import VueMoment from 'vue-moment'
import VueCarousel from 'vue-carousel';
import VueMq from 'vue-mq';
import SocialSharing from 'vue-social-sharing';
import VueLodash from 'vue-lodash';
import 'animate.css';
import CaslMixin from './mixins/casl.js';
import StorageMixin from './mixins/storage.js';
import { AbilityBuilder } from '@casl/ability';
import { abilitiesPlugin } from '@casl/vue';
import api from './common/url.js';
import commonApp from './common/app.js';

const ability = AbilityBuilder.define((can) => {
    let user = localStorage.getItem('user');
    if(user)
    {
        let permission = JSON.parse(user).roles.map(role => {
            return role.permission.join(', ');
        });

        let rules = [];

        permission = permission.join(', ').split(', ');

        permission.forEach(p => {
            let split = p.split('.');
            can(split[1], split[0]);
        });
    }
    else
    {
    	can('read', ['product', 'comment', 'rate']);
    }
});

Vue.use(abilitiesPlugin, ability);
Vue.use(VueCarousel);
Vue.use(Viewer);
Vue.use(ElementUI, {locale});
Vue.use(VueMoment);
Vue.use(Vue2Filters);
Vue.use(VueMq, {
  breakpoints: {
  	xs: 768,
    sm: 992,
    md: 1220,
    lg: 1920,
  }
});
Vue.use(SocialSharing);
Vue.use(VueLodash);

Vue.prototype.api = api;
Vue.prototype.axios = axios;

library.add(faCartPlus, faPlusSquare, faShoppingCart
	, faSearch, faUser, faSignInAlt, faSignOutAlt, faShoppingBag, faEye
  , faTrash, faAngleDoubleRight, faAngleDown, faBars, faAddressCard, faCog
  , faGoogle, faFacebook, faFilter, faHome, faSitemap, faClipboardList, faBox, 
  faUserCog, faChartBar, faBoxes, faBookmark, faCity, faAddressBook, faEnvelope,
  faMoneyBill, faComments, faGooglePlusSquare, faFacebookSquare, faTwitterSquare, 
  faPinterestSquare, faShareSquare, faColumns, faGift, faFire, faDollarSign, faUserLock,
  faClock);
Vue.component('font-awesome-icon', FontAwesomeIcon);
Vue.config.productionTip = false;
Vue.mixin(StorageMixin)
Vue.mixin(CaslMixin);
Vue.mixin({
	methods: {
		$_app_setTitle(title){
			commonApp.setTitle(title);
		}
	}
});

router.beforeEach((to, from, next) => {

	commonApp.setTitle(to.meta.title);

	if(to.name == 'notfound')
	{
		next();
		return;
	}

	if(to.path.indexOf('/admin') == 0 && to.name != 'deny')
	{
		if(ability.can('read', 'admin-page'))
		{
			let permission = to.meta.permission.split('.');

			if(ability.can(permission[1], permission[0]))
			{
				next();
			}
			else
			{
				next({ name: 'deny' });
			}
		}
		else
		{
			next({name: 'home'});
		}
	}
	else
	{
		if(to.path.indexOf('/user') == 0)
		{
			let permission = to.meta.permission.split('.');

			if(ability.can(permission[1], permission[0]))
			{
				next();
			}
			else
			{
				next({ name: 'home' });
			}
		}
		else
		{
			next();
		}
	}
});

const app = new Vue({
    el: '#app',
    //i18n,
    router,
    store,
    render: h => h(App)
})


