import { defineBoot } from '#q-app/wrappers'
import axios, { type AxiosInstance } from 'axios'
import { useAuthStore } from 'src/stores/auth'
import { Notify } from 'quasar'
import { useRouter } from 'vue-router'

declare module 'vue' {
  interface ComponentCustomProperties {
    $axios: AxiosInstance
    $api: AxiosInstance
  }
}

// Get API URL from environment variables
const apiUrl = process.env.VITE_API_URL

if (!apiUrl) {
  console.error('API URL is not defined in environment variables')
}

//console.log('API URL:', apiUrl)

const api = axios.create({ baseURL: process.env.VITE_API_URL || '' })

// api control
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

// Response interceptor for API calls
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
      router.push('/auth/login')
    }
    return Promise.reject(error)
  },
)

export default defineBoot(({ app }) => {
  app.config.globalProperties.$axios = axios
  app.config.globalProperties.$api = api
})

export { api }
