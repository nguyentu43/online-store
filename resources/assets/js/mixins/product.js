import moment from 'moment'
export default{
	methods: {
		$_product_checkExpDiscount(discount){
			let d_today = moment();
			let d_start = moment(discount.start_datetime);
			let d_end = moment(discount.end_datetime);
			return d_today.isBefore(d_end) && d_today.isAfter(d_start)
		}
	}
}