import moment from 'moment'

export default{
	methods: {
		$_time_calcDay(start, end)
		{
			moment.locale('vi');
			let d_today = moment();
			let d_start = moment(start);
			let d_end = moment(end);

			if(!d_start.isValid())
				return null;
			let percentage, content;

			if(d_end.isBefore(d_today))
			{
				content = 'Đã hết hiệu lực';
				percentage = 100;
			}
			else
			{
				if(d_start.isAfter(d_today))
				{
					content = 'Còn ' + d_start.fromNow(true) + ' bắt đầu';
					percentage = 0;
				}
				else
				{
					content = 'Còn ' + d_end.fromNow(true) + ' kết thúc';
					percentage = 100 - Math.round((d_end.diff(d_today) / d_end.diff(d_start))* 100);
				}
			}
			
			return { percentage, content };
		}
	}
}