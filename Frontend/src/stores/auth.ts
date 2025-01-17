import { defineStore } from 'pinia'
import { api } from 'src/boot/axios'
import { useRouter } from 'vue-router'

import { Notify } from 'quasar'

//Kullanıcı bilgileri
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

//api den gelen response
interface AuthResponse {
  success: boolean
  message?: string
  error?: string
  token?: string
  user?: User
}

interface ApiError extends Error {
  response?: {
    data?: {
      error?: string
    }
  }
}

//kullanıcı ve token bilgileri
interface AuthState {
  user: User | null
  token: string | null
}

export const useAuthStore = defineStore('auth', {
  //state de kullanıcı ve token bilgileri
  state: (): AuthState => ({
    user: null,
    token: null,
  }),

  //getters ile kullanıcı ve token bilgilerini al
  getters: {
    isAuthenticated: (state) => !!state.token,
    isTherapist: (state) => state.user?.user_role === 'therapist',
    isAdmin: (state) => state.user?.user_role === 'admin',
  },
  //token ve kullanıcı bilgilerini sessionStorage'a kaydet
  actions: {
    initAuth() {
      const storedToken = sessionStorage.getItem('token')
      const storedUser = sessionStorage.getItem('user')
      if (storedToken && storedUser) {
        this.token = storedToken
        this.user = JSON.parse(storedUser)
      }
    },

    async therapistRegister(formData: FormData) {
      try {
        formData.set('method', 'therapist-register')

        const response = await api.post<AuthResponse>('/auth.php', formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
          },
          timeout: 30000, // 30 seconds timeout for file uploads
          onUploadProgress: (progressEvent) => {
            const percentCompleted = Math.round(
              (progressEvent.loaded * 100) / (progressEvent.total ?? progressEvent.loaded),
            )
            // You could emit this progress if needed
            console.log('Upload Progress:', percentCompleted + '%')
          },
        })

        if (response.data.success) {
          Notify.create({
            color: 'positive',
            message: response.data.message || 'Registration successful!',
            position: 'top-right',
          })
          return true
        }

        Notify.create({
          color: 'negative',
          message: response.data.error || 'Registration failed',
          position: 'top-right',
        })
        return false
      } catch (error: unknown) {
        console.error('Therapist Register error:', error)

        // Handle specific error types
        if (error instanceof Error) {
          if (error.message.includes('timeout')) {
            Notify.create({
              color: 'negative',
              message:
                'Dosya yükleme zaman aşımına uğradı. Lütfen daha küçük dosyalar yüklemeyi deneyin.',
              position: 'top-right',
            })
          } else if (error.message.includes('Network Error')) {
            Notify.create({
              color: 'negative',
              message: 'Ağ hatası. İnternet bağlantınızı kontrol edin.',
              position: 'top-right',
            })
          } else {
            Notify.create({
              color: 'negative',
              message: 'Kayıt sırasında bir hata oluştu: ' + error.message,
              position: 'top-right',
            })
          }
        } else {
          Notify.create({
            color: 'negative',
            message: 'Beklenmeyen bir hata oluştu',
            position: 'top-right',
          })
        }
        return false
      }
    },

    //register işlemi
    async register(formData: FormData) {
      try {
        const response = await api.post('/auth.php', formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
          },
        })

        if (response.data.success) {
          Notify.create({
            type: 'positive',
            message: 'Kayıt başarılı. Giriş yapabilirsiniz.',
            position: 'top-right',
          })
          return true
        } else {
          throw new Error(response.data.error || 'Kayıt işlemi başarısız.')
        }
      } catch (error: unknown) {
        const apiError = error as ApiError
        Notify.create({
          type: 'negative',
          message: apiError.response?.data?.error || apiError.message || 'Kayıt işlemi başarısız.',
          position: 'top-right',
        })
        return false
      }
    },
    //login işlemi
    async login(email: string, password: string) {
      const router = useRouter()
      try {
        const response = await api.post<AuthResponse>('/auth.php', {
          method: 'login',
          email,
          password,
        })

        if (response.data.success && response.data.token && response.data.user) {
          // Ensure user image URL is using production URL
          if (response.data.user.user_img && !response.data.user.user_img.startsWith('https://')) {
            const filename = response.data.user.user_img.split('/').pop()
            response.data.user.user_img = `https://therapify-api.kaankaltakkiran.com/uploads/profile_images/${filename}`
          }

          this.token = response.data.token
          this.user = response.data.user

          // Store in session storage
          sessionStorage.setItem('token', response.data.token)
          sessionStorage.setItem('user', JSON.stringify(response.data.user))

          if (response.data.success) {
            Notify.create({
              color: 'positive',
              message: response.data.message || 'Login successful!',
              position: 'top-right',
            })
            return true
          }

          // Redirect based on user role
          if (this.user.user_role === 'admin') {
            router.push('/admin/overview')
          } else if (this.user.user_role === 'therapist') {
            router.push('/therapist/dashboard')
          } else {
            router.push('/')
          }
        } else {
          Notify.create({
            color: 'negative',
            message: response.data.error || 'Login failed',
            position: 'top-right',
          })
        }
      } catch (error) {
        console.error('Login error:', error)
        Notify.create({
          color: 'negative',
          message: 'Error logging in',
          position: 'top-right',
        })
      }
    },
    //logout işlemi
    logout() {
      this.user = null
      this.token = null
      sessionStorage.removeItem('token')
      sessionStorage.removeItem('user')
    },
  },
})
