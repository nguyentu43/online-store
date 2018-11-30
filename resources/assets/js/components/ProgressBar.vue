<template>
	<div class="progress-bar" :style="{height}" :class="{ 'progress-bar-round': round }">
		<div class="progress-value" :style="{width: value, height, background: color}"></div>
	</div>
</template>

<script>
	export default{
		props: {
			height: {
				type: String,
				default: '10px'
			},
			round: {
				type: Boolean,
				default: false
			},
			value: {
				type: String,
				default: '50%'
			},
			color: {
				type: String,
				default: '#ff424e'
			}
		}
	}
</script>

<style scoped>
	.progress-bar{
		border: 2px solid #eee;
		width: 100%;
		position: relative;
	}

	.progress-bar-round{
		border-radius: 10px;
	}

	.progress-bar-round .progress-value{
		border-radius: 10px;
	}

	.progress-value{
		position: absolute;
		top: 0;
		left: 0;
	}

	.progress-icon{
		position: absolute;
		z-index: 99;
		top: 0px;
		transform: translate(-92%, -50%);
	}

</style>