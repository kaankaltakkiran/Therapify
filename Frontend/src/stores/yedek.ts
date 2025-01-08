import { defineStore } from 'pinia'
import { api } from 'src/boot/axios'
import { useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { Notify } from 'quasar'

interface User {
  id: number
  first_name: string
  last_name: string
  email: string
  user_role: string
  user_img?: string
  therapist_details?: {
    title: string
    about_text: string
    session_fee: number
    session_duration: number
    languages_spoken: string[]
    video_session_available: boolean
    face_to_face_session_available: boolean
    office_address: string
    application_status: string
    specialties: Array<{ id: number; name: string }>
  }
}

interface AuthResponse {
  success: boolean
  message?: string
  error?: string
  token?: string
  user?: User
}

interface AuthState {
  user: User | null
  token: string | null
}

export const useAuthStore = defineStore('auth', {
  state: (): AuthState => ({
    user: null,
    token: null,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    isTherapist: (state) => state.user?.user_role === 'therapist',
    isAdmin: (state) => state.user?.user_role === 'admin',
  },

  actions: {
    initAuth() {
      const storedToken = localStorage.getItem('token')
      const storedUser = localStorage.getItem('user')
      if (storedToken && storedUser) {
        this.token = storedToken
        this.user = JSON.parse(storedUser)
      }
    },

    async register(userData: {
      first_name: string
      last_name: string
      email: string
      password: string
      address: string
      phone_number: string
      birth_of_date: string
      user_img?: string
    }) {
      try {
        const response = await api.post<AuthResponse>('/auth.php', {
          method: 'register',
          ...userData,
        })

        if (response.data.success) {
          Notify.create({
            color: 'positive',
            message: response.data.message || 'Registration successful!', // Added fallback message
            position: 'top-right',
          })
          return true
        }

        // Always show error message if success is false
        Notify.create({
          color: 'negative',
          message: response.data.error || 'Registration failed',
          position: 'top-right',
        })
        return false
      } catch (error) {
        console.error('Register error:', error)
        Notify.create({
          color: 'negative',
          message: 'Error registering',
          position: 'top-right',
        })
        return false
      }
    },

    async login(email: string, password: string) {
      const router = useRouter()
      const $q = useQuasar()

      try {
        const response = await api.post<AuthResponse>('/auth.php', {
          method: 'login',
          email,
          password,
        })

        if (response.data.success && response.data.token && response.data.user) {
          this.token = response.data.token
          this.user = response.data.user
          localStorage.setItem('token', response.data.token)
          localStorage.setItem('user', JSON.stringify(response.data.user))

          $q.notify({
            type: 'positive',
            message: response.data.message || 'Login successful!',
          })

          // Redirect based on user role
          if (this.user.user_role === 'admin') {
            router.push('/admin/overview')
          } else if (this.user.user_role === 'therapist') {
            router.push('/therapist/dashboard')
          } else {
            router.push('/')
          }
        } else {
          throw new Error(response.data.error || 'Login failed')
        }
      } catch (error) {
        $q.notify({
          type: 'negative',
          message: error instanceof Error ? error.message : 'Login failed',
        })
        throw error
      }
    },

    logout() {
      const router = useRouter()
      const $q = useQuasar()

      this.user = null
      this.token = null
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      router.push('/login')
      $q.notify({
        type: 'info',
        message: 'You have been logged out',
      })
    },
  },
})
