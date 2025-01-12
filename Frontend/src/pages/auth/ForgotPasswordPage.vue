<template>
  <q-page class="flex flex-center">
    <div class="auth-form-container">
      <div v-if="!passwordUpdated" class="q-pa-md">
        <h4 class="text-h4 text-center q-mb-md">Şifre Güncelleme</h4>
        <p class="text-body1 text-center q-mb-lg">
          Şifrenizi güncellemek için lütfen aşağıdaki bilgileri doldurun.
        </p>

        <q-form @submit="onSubmit" class="q-gutter-md">
          <q-input
            v-model="email"
            type="email"
            label="E-posta"
            :rules="[
              (val) => !!val || 'E-posta gerekli',
              (val) => /^[^@]+@[^@]+\.[^@]+$/.test(val) || 'Geçerli bir e-posta adresi girin'
            ]"
            outlined
          />

          <q-input
            v-model="oldPassword"
            :type="showOldPassword ? 'text' : 'password'"
            label="Mevcut Şifre"
            :rules="[(val) => !!val || 'Mevcut şifre gerekli']"
            outlined
          >
            <template v-slot:append>
              <q-icon
                :name="showOldPassword ? 'visibility_off' : 'visibility'"
                class="cursor-pointer"
                @click="showOldPassword = !showOldPassword"
              />
            </template>
          </q-input>

          <q-input
            v-model="newPassword"
            :type="showNewPassword ? 'text' : 'password'"
            label="Yeni Şifre"
            :rules="[
              (val) => !!val || 'Yeni şifre gerekli',
              (val) => val.length >= 8 || 'Şifre en az 8 karakter olmalıdır'
            ]"
            outlined
          >
            <template v-slot:append>
              <q-icon
                :name="showNewPassword ? 'visibility_off' : 'visibility'"
                class="cursor-pointer"
                @click="showNewPassword = !showNewPassword"
              />
            </template>
          </q-input>

          <q-input
            v-model="confirmPassword"
            :type="showConfirmPassword ? 'text' : 'password'"
            label="Yeni Şifre (Tekrar)"
            :rules="[
              (val) => !!val || 'Şifre tekrarı gerekli',
              (val) => val === newPassword || 'Şifreler eşleşmiyor'
            ]"
            outlined
          >
            <template v-slot:append>
              <q-icon
                :name="showConfirmPassword ? 'visibility_off' : 'visibility'"
                class="cursor-pointer"
                @click="showConfirmPassword = !showConfirmPassword"
              />
            </template>
          </q-input>

          <div class="row justify-center q-mt-md">
            <q-btn
              type="submit"
              color="primary"
              label="Şifreyi Güncelle"
              :loading="submitting"
              class="full-width"
            />
          </div>

          <div class="row justify-center q-mt-sm">
            <q-btn
              flat
              color="primary"
              label="Giriş Sayfasına Dön"
              to="/login"
            />
          </div>
        </q-form>
      </div>

      <div v-else class="q-pa-md text-center">
        <q-icon name="check_circle" color="positive" size="4rem" />
        <h4 class="text-h4 q-mt-md">Şifre Güncellendi!</h4>
        <p class="text-body1 q-mb-lg">
          Şifreniz başarıyla güncellendi. Yeni şifrenizle giriş yapabilirsiniz.
        </p>
        <q-btn
          color="primary"
          label="Giriş Yap"
          to="/login"
          class="full-width"
        />
      </div>
    </div>
  </q-page>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { api } from 'src/boot/axios'
import { useQuasar } from 'quasar'

const $q = useQuasar()

const email = ref('')
const oldPassword = ref('')
const newPassword = ref('')
const confirmPassword = ref('')
const submitting = ref(false)
const passwordUpdated = ref(false)

const showOldPassword = ref(false)
const showNewPassword = ref(false)
const showConfirmPassword = ref(false)

const onSubmit = async () => {
  if (newPassword.value !== confirmPassword.value) {
    $q.notify({
      type: 'negative',
      message: 'Yeni şifreler eşleşmiyor.',
      position: 'top'
    })
    return
  }

  submitting.value = true
  try {
    const response = await api.post('/auth.php', {
      method: 'update-password',
      email: email.value,
      old_password: oldPassword.value,
      new_password: newPassword.value
    })

    if (response.data.success) {
      passwordUpdated.value = true
    } else {
      $q.notify({
        type: 'negative',
        message: response.data.error || 'Şifre güncelleme başarısız oldu. Lütfen tekrar deneyin.',
        position: 'top'
      })
    }
  } catch (err: unknown) {
    const errorMessage = err instanceof Error 
      ? err.message 
      : err instanceof Object && 'message' in err 
        ? String(err.message)
        : 'Bir hata oluştu. Lütfen daha sonra tekrar deneyiniz.'
        
    $q.notify({
      type: 'negative',
      message: errorMessage,
      position: 'top'
    })
  } finally {
    submitting.value = false
  }
}
</script>

<style lang="scss" scoped>
.auth-form-container {
  width: 100%;
  max-width: 400px;
  margin: 0 auto;
  padding: 2rem;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.12);
}
</style> 