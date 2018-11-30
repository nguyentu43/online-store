<template>
    <div id="header">
        <el-row align="middle" type="flex" justify="space-between" class="hidden-xs-only hidden-sm-only">
            <el-col :sm="3">
                <div class="title-header text-center">
                    <router-link to="/">
                        <font-awesome-icon icon="shopping-bag"/> EShop
                    </router-link>
                </div>
            </el-col>
            <el-col :sm="5">
                <el-menu
                mode="horizontal"
                background-color="#0288D1"
                text-color="white"
                active-text-color="white"
                >
                    <el-submenu index="1">
                        <template slot="title">
                            <i class="el-icon-menu"></i>
                            <span class="title-header">Danh mục chính</span>
                        </template>

                        <category-nav v-for="c in categories" :key="c.id" :category="c"></category-nav>

                    </el-submenu>
                </el-menu>
            </el-col>
            <el-col :sm="8">

                <el-autocomplete 
                v-model="keyword"
                placeholder="Bạn muốn tìm sản phẩm gì?"
                :fetch-suggestions="queryProduct"
                @select="selectSuggestion"
                value-key="name"
                @keyup.enter.native="search"
                :clearable="true"
                >
                </el-autocomplete>

            </el-col>
            <el-col :sm="3" class="text-center">
                <el-badge :value="cart.length" :max="20">
                    <el-button @click="$router.push({path: '/cart'})">
                        <font-awesome-icon icon="shopping-cart"/>
                    </el-button>
                </el-badge>
            </el-col>
            <el-col :sm="5">
                <template v-if="!user">
                    <el-menu 
                    background-color="#0288D1"
                    text-color="white"
                    active-text-color="white"
                    mode="horizontal"
                    :router="true"
                    >
                        <el-submenu index="1">
                            <template slot="title">
                                <span class="title-header"><font-awesome-icon icon="user"/> Tài khoản</span>
                            </template>
                            <el-menu-item index="2" :route="{name: 'login'}">
                                <font-awesome-icon icon="sign-in-alt"/> Đăng nhập
                            </el-menu-item>

                            <el-menu-item index="3" :route="{name: 'register'}">
                                <font-awesome-icon icon="user"/> Đăng ký
                            </el-menu-item>
                        </el-submenu>
                    </el-menu>
                </template>
                <template v-else>
                    <el-menu mode="horizontal" 
                    background-color="#0288D1"
                    text-color="white"
                    active-text-color="white"
                    >
                        <el-submenu index="1">
                            <template slot="title"><span class="title-header"><font-awesome-icon icon="user"/> {{ user.name }} </span></template>
                            <el-menu-item v-if="$can('read', 'admin-page')" index="1-1" @click="adminPage">
                                <font-awesome-icon icon="cog"/> Trang quản lí
                            </el-menu-item>
                            <el-menu-item index="1-2" @click="$router.push({path: '/user'})">
                                <font-awesome-icon icon="address-card"/> Thông tin
                            </el-menu-item>
                            <el-menu-item index="1-3" @click="logout">
                                <font-awesome-icon icon="sign-out-alt"/> Đăng xuất
                            </el-menu-item>
                        </el-submenu>
                        
                    </el-menu>
                </template>
            </el-col>
        </el-row>

        <el-row class="hidden-lg-only hidden-md-only" align="middle" type="flex" justify="space-between">
            <el-col :xs="2" :sm="2">
                <div class="title-header">
                    <router-link to="/">
                        <font-awesome-icon icon="shopping-bag"/>
                    </router-link>
                </div>
            </el-col>

            <el-col :xs="14" :sm="14" class="text-center">

                <el-autocomplete 
                v-model="keyword"
                placeholder="Bạn muốn tìm sản phẩm gì?"
                :fetch-suggestions="queryProduct"
                @select="selectSuggestion"
                value-key="name"
                size="small"
                @keypress.enter.native="search"
                :clearable="true"
                >
                </el-autocomplete>

            </el-col>

            <el-col :xs="4" :sm="4" class="text-center">
                <el-badge :value="cart.length" :max="20">
                    <div class="text-center">
                        <el-button size="small" @click="$router.push({path: '/cart'})">
                            <font-awesome-icon icon="shopping-cart"/>
                        </el-button>
                    </div>
                </el-badge>
            </el-col>

            <el-col :xs="4" :sm="4">
                <div class="text-center">
                    <el-button size="small" ref="btnNav" @click="toggleBtnNav"><font-awesome-icon icon="bars"/></el-button>
                </div>
            </el-col>

        </el-row>

        <el-row class="hidden-lg-only hidden-md-only menubar-collapse" v-if="showMenu">
            <el-col :xs="24">
                <el-menu 
                background-color="#0288D1"
                text-color="white"
                active-text-color="white"
                >
                    <template v-if="!user">
                        <el-menu-item index="1" @click="$router.push({ name: 'login' }); toggleBtnNav();">
                            <font-awesome-icon icon="sign-in-alt"/> Đăng nhập
                        </el-menu-item>
                        <el-menu-item index="2" @click="$router.push({ name: 'register' }); toggleBtnNav();">
                            <font-awesome-icon icon="user"/> Đăng ký
                        </el-menu-item>
                    </template>
                    <template v-else>
                        <el-submenu index="3">
                            <template slot="title"><font-awesome-icon icon="user"/> {{ user.name }} </template>
                            <el-menu-item v-if="$can('read', 'admin-page')" index="3-1" @click="adminPage">
                                <font-awesome-icon icon="cog"/> Trang quản lí
                            </el-menu-item>
                            <el-menu-item index="3-2" @click="$router.push({ name: 'userinfo' }); toggleBtnNav();">
                                <font-awesome-icon icon="address-card"/> Thông tin
                            </el-menu-item>
                            <el-menu-item index="3-3" @click="logout">
                                <font-awesome-icon icon="sign-out-alt"/> Đăng xuất
                            </el-menu-item>
                        </el-submenu>
                    </template>

                    <category-nav 
                    v-for="c in categories" 
                    :key="c.id" 
                    :category="c"
                    @click-btn-nav="toggleBtnNav()"
                    ></category-nav>

                </el-menu>
            </el-col>
        </el-row>
    </div>
</template>

<script>

	import Vue from 'vue'
    import CartMixin from '../mixins/cart.js'
    import moment from 'moment'

    let CategoryNav = {
        template: `
        <el-submenu 
        v-if="category.children.length > 0" 
        :index="category.slug">
            <template slot="title">
                {{ category.name }}
            </template>
            <el-menu-item 
            :index="category.slug"
            @click="moveToCategory(category)"
            >
                Tất cả sản phẩm 
            </el-menu-item>
            <el-menu-item 
            v-for="i in category.children"
            @click="moveToCategory(i)"
            :index="i.slug"
            :key="i.id">
                {{ i.name }} 
            </el-menu-item>
        </el-submenu>
        <el-menu-item 
        v-else 
        :index="category.slug"
        @click="moveToCategory(category)"
        :key="category.id">
            {{ category.name }}
        </el-menu-item>`,
        props: {
            category: {
                type: Object,
                required: true
            }
        },
        methods: {
            moveToCategory(c)
            {
                if(c.children.length > 0)
                    this.$router.push({
                        name: 'categories', params: { slug: c.slug }
                    });
                else
                    this.$router.push({
                        name: 'search', query: { category: c.id }
                    });
                this.$emit('click-btn-nav');
            }
        }
    };

    export default {
    	mixins: [CartMixin],
        data(){
            return {
                showMenu: false,
                categories: [],
                keyword: ''
            }
        },
        computed: {
        	user(){
        		return this.$store.state.user;
        	}
        },
        created(){
            this.axios.get(this.api.categories.get()).then(res=>this.categories=res.data);
            this.keyword = this.$route.query.keyword;
        },
        beforeRouteUpdate(to, from, next)
        {
            this.keyword = this.$route.query.keyword;
        },
        methods: {
        	logout(){
    			this.$store.dispatch('logout');
                this.$_casl_update();
                this.$router.push({ name: 'home' });
        	},
            queryProduct(queryString, cb){

                if(!queryString || (queryString && queryString.trim().length < 4))
                {
                    cb([]);
                    return;
                }

                this.axios.get(this.api.products.get(), { params: { keyword: queryString, take: 6} }).then((res)=>{

                    cb(res.data.products)

                });
            },
            selectSuggestion(item){
                this.$router.push({ name: 'product', params: { slug: item.slug } })
            },
            search(){
                this.$router.replace({ name: 'search', query: { keyword: this.keyword, page: 1 } });
            },
            adminPage(){
                this.$router.push({ path: '/admin' });
                this.toggleBtnNav();
            },
            toggleBtnNav()
            {
                this.showMenu = !this.showMenu;
            },
        },
        components: { CategoryNav }
    }
</script>

<style>

    #header .el-submenu__title i{
        color: white;
    }

    .title-header{
        font-size: 20px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        width: 100%;
    }

    .el-menu--horizontal{
        border-bottom: 0
    }

    #header{
        background-color: #0288D1;
        padding: 0 20px;
        color: white;
    }

    @media screen and (max-width: 768px){

        #header{
            background-color: #0288D1;
            color: white;
            height: 100%;
            padding: 14px;
        }

        .menubar-collapse{
            padding-left: 0px;
            padding-right: 0px;
            margin-top: 10px;
            z-index: 99;
            margin-left: -14px;
            margin-right: -14px;
        }

        .title-header{
            font-size: 15px;
        }

    }

    @media screen and (max-width: 992px) and (min-width: 768px){

        #header{
            background-color: #0288D1;
            color: white;
            height: 100%;
            padding: 14px;
        }

        .menubar-collapse{
            padding-left: 0px;
            padding-right: 0px;
            margin-top: 10px;
            z-index: 99;
            margin-left: -14px;
            margin-right: -14px;
        }

        .title-header{
            font-size: 20px;
        }

    }

    .el-autocomplete{
        width: 100%;
    }

</style>