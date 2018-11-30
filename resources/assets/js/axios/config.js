import axios from 'axios'

axios.defaults.headers.common['Accept'] = 'application/json'
axios.interceptors.request
.use((config) => {
  
  config.headers['Authorization'] = 'Bearer ' + localStorage.getItem('token')

  return config
}, (error) => {
  return Promise.reject(error)
})