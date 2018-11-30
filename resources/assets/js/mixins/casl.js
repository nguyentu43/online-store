export default{
	methods: {
		$_casl_check(model, action, data=null){

			let f = this.$can(action, model, data);

			if(!f)
			{
				this.$message({
					type: 'warning',
					message: 'Tài khoản chưa cấp quyền để thực hiện'
				});
			}

			return f;
		},
		$_casl_open_dialog(model, data)
		{
			if(data.id)
			{
				return this.$_casl_check(model, 'update', data);
			}
			else
			{
				return this.$_casl_check(model, 'create', data);
			}
		},
		$_casl_update()
		{
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

                    if(split[3] == 'owner' )
                    	rules.push({ subject: split[0], action: split[1], user_id: user.id });
                    else	
                    	rules.push({ subject: split[0], action: split[1] });
                });

                this.$ability.update(rules);
            }
            else
            {
            	this.$ability.update([]);
            	this.$ability.update([
            		{ subject: 'product', action: 'read'},
            		{ subject: 'comment', action: 'read'},
            		{ subject: 'rate', action: 'read'}
            	]);
            }
        }
	}
}