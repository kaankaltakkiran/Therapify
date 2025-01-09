<template>
  <q-page class="login-page">
    <div class="container q-pa-md">
      <div class="row justify-center">
        <div class="col-12 col-md-6 col-lg-4">
          <q-card class="login-card q-pa-lg">
            <!-- Logo and Title -->
            <div class="text-center q-mb-xl">
              <router-link to="/" class="logo-link">
                <h4 class="text-h4 logo-text q-mb-md">Therapify</h4>
              </router-link>
              <h5 class="text-h5 text-weight-medium q-mb-sm">Giriş Yap</h5>
              <p class="text-grey-8">Hesabınıza giriş yapın</p>
            </div>

            <!-- Login Form -->
            <q-form @submit="onSubmit" class="q-gutter-md">
              <q-input
                v-model="form.email"
                label="E-posta"
                type="email"
                outlined
                :rules="[
                  (val: string) => !!val || 'E-posta alanı zorunludur',
                  (val: string) => isValidEmail(val) || 'Geçerli bir e-posta adresi giriniz',
                ]"
              >
                <template v-slot:prepend>
                  <q-icon name="email" />
                </template>
              </q-input>

              <q-input
                v-model="form.password"
                label="Şifre"
                :type="showPassword ? 'text' : 'password'"
                outlined
                :rules="[(val: string) => !!val || 'Şifre alanı zorunludur']"
              >
                <template v-slot:prepend>
                  <q-icon name="lock" />
                </template>
                <template v-slot:append>
                  <q-icon
                    :name="showPassword ? 'visibility_off' : 'visibility'"
                    class="cursor-pointer"
                    @click="showPassword = !showPassword"
                  />
                </template>
              </q-input>

              <!-- Remember Me & Forgot Password -->
              <div class="row items-center justify-between q-mt-sm">
                <router-link to="/auth/forgot-password" class="text-primary">
                  Şifremi Unuttum
                </router-link>
              </div>

              <!-- Submit Button -->
              <q-btn
                type="submit"
                color="primary"
                class="full-width q-mt-lg"
                size="lg"
                :loading="submitting"
                label="Giriş Yap"
              />

              <!-- Register Link -->
              <div class="text-center q-mt-sm">
                Hesabınız yok mu?
                <router-link to="/register" class="text-primary">Kayıt Ol</router-link>
              </div>
            </q-form>
          </q-card>
        </div>
      </div>
    </div>
  </q-page>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useQuasar } from 'quasar'
import { useRouter } from 'vue-router'
import { useAuthStore } from 'stores/auth'

const $q = useQuasar()
const router = useRouter()
const authStore = useAuthStore()

//form elamanları
interface LoginForm {
  email: string
  password: string
}

const showPassword = ref(false)
const submitting = ref(false)

const form = ref<LoginForm>({
  email: '',
  password: '',
})

const isValidEmail = (email: string) => {
  const emailPattern =
    /^(?=[a-zA-Z0-9@._%+-]{6,254}$)[a-zA-Z0-9._%+-]{1,64}@(?:[a-zA-Z0-9-]{1,63}\.){1,8}[a-zA-Z]{2,63}$/
  return emailPattern.test(email)
}

const onSubmit = async () => {
  submitting.value = true
  //pinia storedan login fonksiyonuna gonderecegimiz veriler
  try {
    const response = await authStore.login(form.value.email, form.value.password)

    // işlem basarılıysa login sayfasına yonlendir
    if (response) {
      router.push('/')
    }
  } catch (error: unknown) {
    console.error('Registration error:', error)
    $q.notify({
      type: 'negative',
      message: 'Kayıt sırasında bir hata oluştu. Lütfen tekrar deneyiniz.',
      position: 'top',
    })
  } finally {
    submitting.value = false
  }
}
</script>

<style lang="scss" scoped>
.login-page {
  background: linear-gradient(135deg, $soft-bg, white);
  min-height: 100vh;
  padding: 2rem 0;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
}

.login-card {
  border-radius: $generic-border-radius;
  box-shadow: $card-shadow;

  .q-input {
    .q-field__control {
      height: 56px;
    }
  }
}

.logo-link {
  text-decoration: none !important;
  color: inherit;

  .logo-text {
    background: linear-gradient(135deg, $primary, $secondary);
    background-clip: text;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-weight: 700;
    margin: 0;
  }
}

a {
  text-decoration: none;
  font-weight: 500;
  transition: color 0.3s ease;

  &:hover {
    text-decoration: underline;
  }
}

@media (max-width: 599px) {
  .login-page {
    padding: 1rem 0;
  }

  .login-card {
    padding: 1.5rem !important;
  }
}
</style>
