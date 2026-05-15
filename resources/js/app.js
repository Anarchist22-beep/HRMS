import { createApp } from 'vue'
import App from './App.vue'
import router from './router'

import VueApexCharts from 'vue3-apexcharts'
import Toast from 'vue-toastification'
import axios from 'axios'
/* Bootstrap */
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap/dist/js/bootstrap.bundle.min.js'
import 'vue3-easy-data-table/dist/style.css'
import 'vue-toastification/dist/index.css'





/* Bootstrap Icons */
import 'bootstrap-icons/font/bootstrap-icons.css'

/* OverlayScrollbars (CSS only) */
import 'overlayscrollbars/styles/overlayscrollbars.css'

/* AdminLTE */
import './admin-lte/adminlte.js'
import '../css/adminlte.css'

/*  AXIOS GLOBAL CONFIG */
axios.defaults.baseURL = '/api'

// Attach token automatically
axios.interceptors.request.use(config => {
  const token = localStorage.getItem('token')

  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }

  return config
})


const app = createApp(App)

app.use(router)
app.use(VueApexCharts)
app.use(Toast) //  enable toast globally

/*  OPTIONAL: make axios global (so no import needed) */
app.config.globalProperties.$axios = axios



app.mount('#app')