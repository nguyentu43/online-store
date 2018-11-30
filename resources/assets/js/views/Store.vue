<template>
    <el-container style="position: relative;">
        <el-header>
            <nav-bar></nav-bar>
        </el-header>
        <el-main>
            <router-view></router-view>
        </el-main>
        <el-footer>
            EShop
        </el-footer>
        <compare-product/>
    </el-container>
</template>

<script>
    import NavBar from '../components/NavBar.vue'
    import CompareProduct from '../components/CompareProduct.vue'
    import CartMixin from '../mixins/cart.js'

    export default {
        mixins: [CartMixin],
        components: {NavBar, CompareProduct},
        created(){
            let token = localStorage.getItem('token');
            if(!(token === "undefined" || token == null))
            {
                this.$_cart_sync();
            }

            if(!(localStorage.getItem('cart') === "undefined" || localStorage.getItem('cart') == null))
                this.$store.dispatch('setDataCart', JSON.parse(localStorage.getItem('cart')));
        }
    }
</script>

<style scoped>

    #app{
        margin: 0;
    }

    .el-header{
        padding: 0;
    }

    .el-main{
        padding: 0 100px;
        background-color: #E0E0E0;
        min-height: calc(100vh - 120px);
    }

    @media screen and (max-width: 992px) {
      .el-main{
        padding: 0;
      }
    }

    .el-footer{
        background: #004D40;
        color: white;
        line-height: 60px;
    }

</style>
