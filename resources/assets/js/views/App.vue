<template>
    <router-view></router-view>
</template>

<script>

    export default {
        created(){
            this.$_casl_update();
            this.axios.interceptors.response
            .use((response) => {
              return response
            }, (err) => {

                if(err.response && err.response.status == 401)
                {
                    localStorage.removeItem('token');
                    localStorage.removeItem('user');
                    this.$router.push('login');

                    this.$notify({
                        title: 'Lỗi!',
                        message: 'Token đã hết hạn',
                        type: 'error',
                        position: 'top-right'
                    });

                    return;
                }

                let message = err.message;

                if(err.response)
                    message = /*err.response.data.message*/ "Lỗi hệ thống. Hãy thử lại!";
                
                this.$notify({
                    title: 'Lỗi!',
                    message,
                    type: 'error',
                    position: 'top-right'
                });
            })


            let token = localStorage.getItem('token');

            if(!(token === "undefined" || token == null))
            {
                this.axios.get(this.api.user.get()).then(res => {
                    localStorage.setItem('user', JSON.stringify(res.data));
                    this.$store.dispatch('setUser', res.data);
                });
            }
            else
            {
                localStorage.removeItem('token');
            }
        }
    }
</script>

<style>
    .mt-1{
        margin-top: 10px;
    }

    .mb-1{
        margin-bottom: 10px;
    }
</style>