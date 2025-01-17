import { boot } from 'quasar/wrappers'
import axios from 'axios'
import type { AxiosInstance } from 'axios'
import { useAuthStore } from 'src/stores/auth'
import { Notify } from 'quasar'
import { useRouter } from 'vue-router'

declare module 'vue' {
  interface ComponentCustomProperties {
    $axios: AxiosInstance
    $api: AxiosInstance
  }
}

// API URLs based on environment
const isLocalhost = window.location.hostname === 'localhost'
const API_URL = isLocalhost
  ? 'http://localhost/Therapify/'
  : 'https://therapify-api.kaankaltakkiran.com/Therapify/'
const UPLOAD_URL = isLocalhost
  ? 'http://localhost/uploads'
  : 'https://therapify-api.kaankaltakkiran.com/uploads'

console.log('Environment:', isLocalhost ? 'Local Development' : 'Production')
console.log('Using API URL:', API_URL)
console.log('Using Upload URL:', UPLOAD_URL)

const api = axios.create({
  baseURL: API_URL,
  timeout: 30000, // 30 seconds timeout
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
})

// Request interceptor
api.interceptors.request.use(
  (config) => {
    const authStore = useAuthStore()
    if (authStore.token) {
      config.headers['Authorization'] = `Bearer ${authStore.token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  },
)

// Response interceptor
api.interceptors.response.use(
  (response) => response,
  async (error) => {
    const authStore = useAuthStore()
    const router = useRouter()

    if (error.response?.status === 401) {
      // Clear auth state
      authStore.logout()

      // Notify user
      Notify.create({
        type: 'negative',
        message: 'Your session has expired. Please log in again.',
        position: 'top',
      })

      // Redirect to login
      router.push('/login')
    }
    return Promise.reject(error)
  },
)

export default boot(({ app }) => {
  app.config.globalProperties.$axios = axios
  app.config.globalProperties.$api = api
})

export { api, UPLOAD_URL }
