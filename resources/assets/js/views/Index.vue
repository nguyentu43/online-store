<template>
    <div>
        <campaign mode='grid'/>
        <div class="panel">
            <div class="panel-heading highlight highlight-red">
                <h5>Danh mục chính</h5>
            </div>
            <el-row>
                <el-col :sm="6" :md="4" :xs="8" v-for="c in categories" :key="c.id">
                    <div class="category-box text-click" @click="moveToCategory(c)">
                            <img :src="c.url === null ? $_storage_getImageFromApp('NO_IMAGE')() : c.url" />
                            <h5>{{ c.name }}</h5>
                    </div>
                </el-col>
            </el-row>
        </div>
        <div v-for="item in categories" class="panel">
            <div class="panel-heading highlight">

                <h5 class="text-click" @click="moveToCategory(item)">
                    {{ item.name }}
                </h5>

            </div>
            <div class="panel-body">
                <template v-if='item.children.length > 0'>
                    <el-tabs>
                        <el-tab-pane :lazy="true">
                            <span slot="label">Tất cả</span>
                            <campaign :category="item.id" mode="mini"/>
                            <product-list-by-category
                            :data="item"
                            mode="carousel"
                            ></product-list-by-category>
                        </el-tab-pane>
                        <el-tab-pane
                        v-for="c in item.children"
                        :lazy="true"
                        :key="c.id">
                            <span slot="label"> {{ c.name }}</span>
                            <campaign :category="c.id" mode="mini"/>
                            <product-list-by-category
                            :data="c"
                            mode="carousel"
                            ></product-list-by-category>
                        </el-tab-pane>
                    </el-tabs>
                </template>
                <product-list-by-category
                 v-else
                :data="item"
                :key="item.id"
                mode="carousel"
                >
                </product-list-by-category>
            </div>
        </div>
        <product-list-by-history />
    </div>
</template>

<script>

    import Campaign from '../components/Campaign.vue'
    import ProductListByCategory from '../components/ProductListByCategory.vue'
    import ProductListByHistory from '../components/ProductListByHistory.vue'

    export default {
        data(){
            return {
                categories: []
            }
        },
        created(){
            
            this.axios.get(this.api.categories.get())
            .then(res => {
                this.categories = res.data;
            })
        },
        methods:{
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
            }
        },
        components: { Campaign, ProductListByCategory, ProductListByHistory}
    }
</script>

<style scoped>

    .more-btn{
        border-radius: 50%;
    }

    .category-box{
        padding: 20px;
        margin: 10px;
        text-align: center;
        font-weight: bold;
    }

    .category-box img{
        height: 70px;
        width: auto;
        vertical-align: middle;
        margin-bottom: 10px;
    }

    @media screen and (max-width: 768px){
        .category-box img{
            height: 40px;
        }
    }

</style>
