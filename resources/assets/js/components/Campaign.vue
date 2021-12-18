<template>

  <div class="panel banner" v-if="campaignData.length > 0">
    <template v-if="mode == 'grid' && $mq != 'xs'">
      <div class="grid-banner">
        <div class="grid1">
          <carousel :per-page="perPage">
            <slide 
            v-for="item in campaignCarousel" :key="item.id"
            >
              <div class="slide-banner text-click"
              @click="$router.push({ name: 'campaign', params: { slug: item.slug } })"
              :style="{backgroundImage: 'url(' + item.url + ')'}"
              ></div>
            </slide>
          </carousel>
        </div>
        <div :class="'grid' + (index + 2)" v-for="item, index in campaignGrid"
        class="text-click"
        :style="{backgroundImage: 'url(' + item.url + ')'}"
        >
        </div>
      </div>
    </template>
    <carousel :per-page="perPage" v-else :class="{'gap-slide': category}">
      <slide 
      v-for="item in campaignData" :key="item.id"
      >
        <div class="slide-banner text-click"
        @click="$router.push({ name: 'campaign', params: { slug: item.slug } })"
        :style="{height: getHeight, backgroundImage: 'url(' + item.url + ')'}"
        ></div>
      </slide>
    </carousel>
  </div>

</template>

<script>

  import { Carousel, Slide } from 'vue-carousel';

	export default {
      props: [ 'category', 'mode' ],
      data(){
        return {
              campaignData: [],
              campaignGrid: [],
              campaignCarousel: [],
              perPage: 1
            };
      },
      methods: {
        getData(){
          this.axios.get(this.api.campaigns.get(), { params: { category: this.category } }).then(res => {
            this.campaignData = res.data;

            if(this.mode == 'grid' && this.$mq != 'xs')
            {
              this.campaignGrid = this.campaignData.slice(1, 6);
              this.campaignCarousel = this.campaignData.slice(6);
              this.campaignCarousel.unshift(this.campaignData[0]);
            }

          });
        }
      },
      computed:{
        getHeight(){
          if(this.category && this.mode == 'mini')
          {
            return this.$mq == 'xs' ? '100px' : '120px';
          }
          else
          {
            return this.$mq == 'xs' ? '150px' : '300px';
          }
        }
      },
      created(){
        this.getData();
        if(this.category && this.mode == 'mini')
        {
          if(this.$mq == 'sm')
            this.perPage = 2;
          else if(this.$mq == 'md')
            this.perPage = 3;
          else if(this.$mq == 'lg')
            this.perPage = 4;
        }
      },
      components: { Carousel, Slide }
	}
</script>

<style>

  .slide-banner{
    background-position: center;
    background-repeat: no-repeat;
    background-size: contain;
  }

  .grid-banner{
    display: grid;
    grid-template-columns: auto auto auto;
    grid-template-rows: auto auto auto;
    height: 400px;
  }

  .grid-banner .grid1{
    grid-area: 1/1/3/3;
  }

  .gap-slide .slide-banner{
    /*margin-left: 5px*/
  }

  .banner .VueCarousel-pagination{
    position: absolute;
    bottom: 0;
  }

  .grid-banner .slide-banner, .grid-banner .VueCarousel, .grid-banner .VueCarousel-wrapper, .grid-banner .VueCarousel-inner{
    height: 100%;
  }

</style>