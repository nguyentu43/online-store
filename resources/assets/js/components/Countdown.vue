<template>
	<div class="text-danger">
		<font-awesome-icon :icon="{ prefix: 'far', iconName: 'clock'}" :size="style.sizeClock" style="vertical-align: middle;" />
		<strong :style="{ fontSize: style.fontSize }" style="vertical-align: middle;">
			<span v-if="duration">{{ duration.days() }} ngày {{ duration.hours() }}:{{ duration.minutes() }}:{{ duration.seconds() }} {{ size == 'sm' || size == 'xs' ? '' : ' kết thúc khuyến mãi'}}</span>
			<span v-else>{{ content }}</span>
		</strong>
	</div>
</template>

<script>
	import moment from 'moment';

	export default{
		props: {
			start: String,
			end: String,
			size: {
				type: String,
				default: 'lg'
			}
		},
		data(){
			return {
				dt_start: '',
				dt_end: '',
				content: '',
				duration: null,
				interval: null
			};
		},
		computed: {
			style(){

				if(this.size == 'lg')
				{
					return {
						fontSize: '30px',
						sizeClock: '3x'
					}
				}
				else if(this.size == 'md')
				{
					return {
						fontSize: '20px',
						sizeClock: '2x'
					}
				}
				else if(this.size == 'sm')
				{
					return {
						fontSize: '14px',
						sizeClock: '1x'
					}
				}
				else if(this.size == 'xs')
				{
					return {
						fontSize: '12px',
						sizeClock: '1x'
					}
				}
			}
		},
		methods: {
			check(){

				if(this.dt_end.isBefore(moment()))
				{
					this.content = 'Đã hết hiệu lực';
					clearInterval(this.interval);
				}
				else
				{
					if(this.dt_start.isAfter(moment()))
					{
						this.content = 'Còn ' + this.dt_start.fromNow(true) + ' bắt đầu';
					}
					else
					{
						let duration = moment.duration(this.dt_end.diff(moment()));
						if(duration.months() == 0)
							this.duration = duration;
						else
						{
							this.duration = null;
							this.content = 'Còn lại hơn ' + duration.months() + ' tháng kết thúc';
						}
					}
				}

			}
		},
		created()
		{
			this.dt_start = moment(this.start);
			this.dt_end = moment(this.end);
		},
		mounted()
		{
			this.interval = setInterval(()=>{

				this.check();

			}, 1000);
		}
	}
</script>

<style scoped>
	div{
		overflow: hidden;
		white-space: nowrap;
		text-overflow: ellipsis;
	}
</style>