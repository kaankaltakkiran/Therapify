<template>
  <q-page class="register-page">
    <div class="container q-pa-md">
      <div class="row justify-center">
        <div class="col-12 col-md-8 col-lg-6">
          <q-card class="register-card q-pa-lg">
            <!-- Logo and Title -->
            <div class="text-center q-mb-xl">
              <router-link to="/" class="logo-link">
                <h4 class="text-h4 logo-text q-mb-md">Therapify</h4>
              </router-link>
              <h5 class="text-h5 text-weight-medium q-mb-sm">Hesap Oluştur</h5>
            </div>

            <!-- Registration Form -->
            <q-form @submit="onSubmit" class="q-gutter-md">
              <!-- Personal Information -->
              <div class="row q-col-gutter-md">
                <div class="col-12 col-sm-6">
                  <q-input
                    v-model="form.firstName"
                    label="Ad *"
                    outlined
                    :rules="[(val: string) => !!val || 'Ad alanı zorunludur']"
                  />
                </div>
                <div class="col-12 col-sm-6">
                  <q-input
                    v-model="form.lastName"
                    label="Soyad *"
                    outlined
                    :rules="[(val: string) => !!val || 'Soyad alanı zorunludur']"
                  />
                </div>
              </div>

              <q-input
                v-model="form.email"
                label="E-posta *"
                type="email"
                outlined
                :rules="[
                  (val: string) => !!val || 'E-posta alanı zorunludur',
                  (val: string) => isValidEmail(val) || 'Geçerli bir e-posta adresi giriniz',
                ]"
              />
              <!-- Password Fields -->
              <q-input
                v-model="form.password"
                label="Şifre *"
                :type="showPassword ? 'text' : 'password'"
                outlined
                :rules="[
                  (val: string) => !!val || 'Şifre alanı zorunludur',
                  (val: string) =>
                    val.length >= 8 || 'Şifre en az 8 karakter uzunluğunda olmalıdır',
                ]"
              >
                <template v-slot:append>
                  <q-icon
                    :name="showPassword ? 'visibility_off' : 'visibility'"
                    class="cursor-pointer"
                    @click="showPassword = !showPassword"
                  />
                </template>
              </q-input>

              <q-input
                v-model="form.confirmPassword"
                label="Şifre Tekrar *"
                :type="showConfirmPassword ? 'text' : 'password'"
                outlined
                :rules="[
                  (val: string) => !!val || 'Şifre tekrar alanı zorunludur',
                  (val: string) => val === form.password || 'Şifreler eşleşmiyor',
                ]"
              >
                <template v-slot:append>
                  <q-icon
                    :name="showConfirmPassword ? 'visibility_off' : 'visibility'"
                    class="cursor-pointer"
                    @click="showConfirmPassword = !showConfirmPassword"
                  />
                </template>
              </q-input>

              <q-input
                v-model="form.phone"
                label="Telefon *"
                outlined
                mask="(###) ### ## ##"
                :rules="[(val: string) => !!val || 'Telefon alanı zorunludur']"
              >
                <template v-slot:prepend>
                  <div class="text-grey-7">+90</div>
                </template>
              </q-input>

              <q-input
                v-model="form.birthDate"
                label="Doğum Tarihi *"
                outlined
                type="date"
                mask="####/##/##"
                :rules="[(val: string) => !!val || 'Doğum tarihi zorunludur']"
              >
              </q-input>

              <q-input
                v-model="form.address"
                label="Adres *"
                type="textarea"
                outlined
                autogrow
                :rules="[(val: string) => !!val || 'Adres alanı zorunludur']"
              />

              <!-- Profile Image Upload -->
              <div class="row items-center q-col-gutter-md">
                <div class="col-auto">
                  <q-avatar size="100px">
                    <img :src="imagePreview || 'https://cdn.quasar.dev/img/avatar.png'" />
                  </q-avatar>
                </div>
                <div class="col">
                  <q-file
                    v-model="form.userImg"
                    label="Profil Fotoğrafı *"
                    outlined
                    accept=".jpg,.jpeg,.png"
                    :rules="[(val: File | null) => !!val || 'Profil fotoğrafı zorunludur']"
                    @update:model-value="onImageSelected"
                  >
                    <template v-slot:prepend>
                      <q-icon name="attach_file" />
                    </template>
                  </q-file>
                  <div class="text-grey-7 text-caption q-mt-sm">
                    JPG veya PNG formatında, maksimum 5MB
                  </div>
                </div>
              </div>

              <!-- Terms and Conditions -->
              <q-checkbox
                v-model="form.acceptTerms"
                label="Kullanım koşullarını ve gizlilik politikasını kabul ediyorum *"
                :rules="[(val: boolean) => !!val || 'Kullanım koşullarını kabul etmelisiniz']"
              />

              <!-- Submit Button -->
              <q-btn
                type="submit"
                color="primary"
                class="full-width q-mt-lg"
                size="lg"
                :loading="submitting"
                label="Kayıt Ol"
              />

              <!-- Login Link -->
              <div class="text-center q-mt-sm">
                Zaten hesabınız var mı?
                <router-link to="/login" class="text-primary">Giriş Yap</router-link>
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

//Form bilgileri
interface RegisterForm {
  firstName: string
  lastName: string
  email: string
  phone: string
  birthDate: string
  address: string
  password: string
  confirmPassword: string
  userImg: File | null
  acceptTerms: boolean
}

const showPassword = ref(false)
const showConfirmPassword = ref(false)
const submitting = ref(false)
const imagePreview = ref('')

const form = ref<RegisterForm>({
  firstName: '',
  lastName: '',
  email: '',
  phone: '',
  birthDate: '',
  address: '',
  password: '',
  confirmPassword: '',
  userImg: null,
  acceptTerms: false,
})

const isValidEmail = (email: string) => {
  const emailPattern =
    /^(?=[a-zA-Z0-9@._%+-]{6,254}$)[a-zA-Z0-9._%+-]{1,64}@(?:[a-zA-Z0-9-]{1,63}\.){1,8}[a-zA-Z]{2,63}$/
  return emailPattern.test(email)
}

const onImageSelected = (file: File | null) => {
  if (file) {
    const reader = new FileReader()
    reader.onload = (e: ProgressEvent<FileReader>) => {
      imagePreview.value = e.target?.result as string
    }
    reader.readAsDataURL(file)
  } else {
    imagePreview.value = ''
  }
}

//form submit islemi
const onSubmit = async () => {
  // Check all required fields
  if (!form.value.acceptTerms) {
    $q.notify({
      type: 'negative',
      message: 'You need to accept the license and terms first',
      position: 'top',
    })
    return
  }

  // Validate email format
  if (!isValidEmail(form.value.email)) {
    $q.notify({
      type: 'negative',
      message: 'Geçerli bir e-posta adresi giriniz.',
      position: 'top',
    })
    return
  }

  // Validate password match
  if (form.value.password !== form.value.confirmPassword) {
    $q.notify({
      type: 'negative',
      message: 'Şifreler eşleşmiyor.',
      position: 'top',
    })
    return
  }

  submitting.value = true

  try {
    // Convert form data to match API format
    const formData = {
      first_name: form.value.firstName,
      last_name: form.value.lastName,
      email: form.value.email,
      password: form.value.password,
      address: form.value.address,
      phone_number: form.value.phone,
      birth_of_date: form.value.birthDate,
    }

    // Add user_img only if it exists
    if (form.value.userImg) {
      Object.assign(formData, {
        user_img: await convertFileToBase64(form.value.userImg),
      })
    }
    //pinia storedan register fonksiyonuna gonderecegimiz veriler
    const response = await authStore.register(formData)

    //işlem basarılıysa login sayfasına yonlendir
    if (response) {
      router.push('/login')
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

// Add helper function to convert File to base64
const convertFileToBase64 = (file: File): Promise<string> => {
  return new Promise((resolve, reject) => {
    const reader = new FileReader()
    const img = new Image()

    reader.onload = (e) => {
      img.src = e.target?.result as string
      img.onload = () => {
        const canvas = document.createElement('canvas')
        const MAX_WIDTH = 800
        const MAX_HEIGHT = 800
        let width = img.width
        let height = img.height

        // Calculate new dimensions while maintaining aspect ratio
        if (width > height) {
          if (width > MAX_WIDTH) {
            height *= MAX_WIDTH / width
            width = MAX_WIDTH
          }
        } else {
          if (height > MAX_HEIGHT) {
            width *= MAX_HEIGHT / height
            height = MAX_HEIGHT
          }
        }

        canvas.width = width
        canvas.height = height
        const ctx = canvas.getContext('2d')
        ctx?.drawImage(img, 0, 0, width, height)

        // Convert to base64 with reduced quality
        const base64String = canvas.toDataURL('image/jpeg', 0.7)
        resolve(base64String)
      }
      img.onerror = reject
    }
    reader.onerror = reject
    reader.readAsDataURL(file)
  })
}
</script>

<style lang="scss" scoped>
.register-page {
  background: linear-gradient(135deg, $soft-bg, white);
  min-height: 100vh;
  padding: 2rem 0;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
}

.register-card {
  border-radius: $generic-border-radius;
  box-shadow: $card-shadow;
}

.logo-link {
  text-decoration: none;
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

.user-type-toggle {
  width: 100%;
  max-width: 300px;

  :deep(.q-btn) {
    flex: 1;
  }
}

@media (max-width: 599px) {
  .register-page {
    padding: 1rem 0;
  }

  .register-card {
    padding: 1.5rem !important;
  }
}
</style>
