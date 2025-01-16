<template>
  <div class="gt-sm row items-center nav-links">
    <q-btn flat no-caps :label="$t('Ana Sayfa')" to="/" class="nav-link" />
    <q-btn
      flat
      no-caps
      :label="$t('Hizmetlerimiz')"
      @click="scrollToSection('services')"
      class="nav-link"
    />
    <q-btn
      flat
      no-caps
      :label="$t('Terapistler')"
      @click="scrollToSection('therapists')"
      class="nav-link"
    />
    <!--   <q-btn flat no-caps label="Terapistlerimiz" to="/therapists" class="nav-link" /> -->
    <q-btn flat no-caps :label="$t('İletişim')" to="/contact" class="nav-link" />

    <!--Dil seçimi-->
    <q-select
      v-model="locale"
      :options="localeOptions"
      dense
      borderless
      emit-value
      map-options
      options-dense
      class="language-selector"
    >
      <template v-slot:selected>
        <q-item v-if="locale">
          <q-item-section avatar>
            <q-avatar size="20px">
              <img :src="`/images/flags/${locale}.svg`" :alt="locale" />
            </q-avatar>
          </q-item-section>
          <q-item-section>
            <q-item-label>{{ getLanguageName(locale) }}</q-item-label>
          </q-item-section>
        </q-item>
      </template>

      <template v-slot:option="scope">
        <q-item v-bind="scope.itemProps">
          <q-item-section avatar>
            <q-avatar size="20px">
              <img :src="`/images/flags/${scope.opt.value}.svg`" :alt="scope.opt.value" />
            </q-avatar>
          </q-item-section>
          <q-item-section>
            <q-item-label>{{ scope.opt.label }}</q-item-label>
          </q-item-section>
        </q-item>
      </template>
    </q-select>

    <!-- Auth Buttons -->
    <div v-if="!isAuthenticated" class="auth-buttons q-ml-md">
      <q-btn flat no-caps :label="$t('Giriş')" to="/login" class="auth-link q-mr-sm" />
      <q-btn
        unelevated
        no-caps
        :label="$t('Kayıt Ol')"
        to="/register"
        class="auth-cta"
        color="primary"
      />
    </div>
    <!-- Profile Dropdown -->
    <q-btn-dropdown
      v-if="isAuthenticated && user"
      class="glossy q-ml-md"
      color="secondary"
      :label="$t('Hoş Geldin') + ' ' + user.first_name"
    >
      <div class="row no-wrap q-pa-md">
        <div class="column items-center">
          <div class="text-h6 q-mb-md">Profile</div>
          <q-avatar size="72px">
            <img :src="getFileUrl(user.user_img)" />
          </q-avatar>
          <div class="text-subtitle1 q-mt-md q-mb-xs">
            {{ user.first_name }} {{ user.last_name }}
          </div>
          <div class="text-caption text-grey">{{ user.email }}</div>
          <div v-if="user.user_role" class="text-caption text-primary q-mb-md">
            {{ user.user_role }}
          </div>
          <q-btn
            color="negative"
            :label="$t('Çıkış Yap')"
            push
            size="sm"
            v-close-popup
            @click="handleLogout"
          />
        </div>
      </div>
    </q-btn-dropdown>
  </div>
</template>

<script setup lang="ts">
/* import { ref, computed }  from 'vue' */
import { useRouter } from 'vue-router'
import { useAuthStore } from 'stores/auth'
import { storeToRefs } from 'pinia'
import { Notify } from 'quasar'

import { useI18n } from 'vue-i18n'

const { locale } = useI18n({ useScope: 'global' })

const localeOptions = [
  { value: 'tr', label: 'Türkçe' },
  { value: 'en-US', label: 'English' },
]

const getLanguageName = (code: string) => {
  const option = localeOptions.find((opt) => opt.value === code)
  return option ? option.label : code
}

const router = useRouter()
const authStore = useAuthStore()
//tokene sahip kullanıcı var mı
const { isAuthenticated, user } = storeToRefs(authStore)

const getFileUrl = (path: string | undefined) => {
  if (!path) return 'https://cdn.quasar.dev/img/boy-avatar.png'

  // Check if the path is a base64 image
  if (path.startsWith('data:image')) {
    return path
  }

  // If path already contains the full URL, return it as is
  if (path.startsWith('https://therapify-api.kaankaltakkiran.com')) {
    return path
  }

  // Remove any leading slashes and 'uploads' from the path
  let cleanPath = path
  if (cleanPath.startsWith('/')) {
    cleanPath = cleanPath.substring(1)
  }
  if (cleanPath.startsWith('uploads/')) {
    cleanPath = cleanPath.substring(7)
  }

  // Always use the production URL
  const baseUrl = 'https://therapify-api.kaankaltakkiran.com/uploads'

  return `${baseUrl}/${cleanPath}`
}

// console.log(import.meta.env.VITE_UPLOAD_URL)

// Logout işlemi pinia storedaki fonksiyon
const handleLogout = async () => {
  Notify.create({
    color: 'info',
    message: 'çıkış yapıldı',
    position: 'top-right',
  })
  await authStore.logout()
}

const scrollToSection = async (sectionId: string) => {
  // If we're not on the home page, navigate there first
  if (router.currentRoute.value.path !== '/') {
    await router.push('/')
    // Wait for the navigation and DOM update
    await new Promise((resolve) => setTimeout(resolve, 100))
  }

  const element = document.getElementById(sectionId)
  if (element) {
    element.scrollIntoView({
      behavior: 'smooth',
      block: 'start',
    })
  }
}
</script>

<style lang="scss" scoped>
.nav-links {
  flex: 1;
  justify-content: center;
  margin-right: 2rem;

  .nav-link {
    margin: 0 0.5rem;
    font-weight: 500;
    padding: 0.5rem 1rem;
    color: $grey-8;
    border-radius: 12px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;

    &::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg, rgba($primary, 0.1), rgba($secondary, 0.1));
      opacity: 0;
      transition: opacity 0.3s ease;
      z-index: -1;
    }

    &:hover {
      color: $primary;
      transform: translateY(-1px);

      &::before {
        opacity: 1;
      }
    }
  }
}

.auth-buttons {
  .auth-link {
    font-weight: 500;
    color: $grey-8;
    padding: 0.5rem 1rem;
    border-radius: 12px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);

    &:hover {
      background: linear-gradient(135deg, rgba($primary, 0.1), rgba($secondary, 0.1));
      color: $primary;
      transform: translateY(-1px);
    }
  }

  .auth-cta {
    font-weight: 600;
    padding: 0.5rem 1.5rem;
    border-radius: 12px;
    background: linear-gradient(135deg, $primary, $secondary);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);

    &:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba($primary, 0.3);
    }
  }
}

.language-selector {
  margin: 0 0.5rem;
  min-width: 120px;

  :deep(.q-field__control) {
    padding: 0 8px;
    height: 36px;
    border-radius: 8px;
    background: rgba($primary, 0.05);
  }

  :deep(.q-field__marginal) {
    height: 36px;
  }

  :deep(.q-menu) {
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba($primary, 0.1);
  }
}

:deep(.q-list) {
  min-width: 150px;
  border-radius: 12px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
  border: 1px solid rgba($primary, 0.1);
  overflow: hidden;
}

:deep(.q-item) {
  min-height: 40px;
  transition: all 0.3s ease;

  &.q-item--active {
    color: $primary;
    background: linear-gradient(135deg, rgba($primary, 0.1), rgba($secondary, 0.1));
  }

  &:hover {
    background: linear-gradient(135deg, rgba($primary, 0.05), rgba($secondary, 0.05));
  }
}
</style>
