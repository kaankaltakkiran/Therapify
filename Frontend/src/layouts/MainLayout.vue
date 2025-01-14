<template>
  <q-layout view="lHh LpR fFf">
    <!-- Header -->
    <q-header elevated class="bg-white text-dark">
      <q-toolbar class="container">
        <!-- Logo -->
        <q-toolbar-title class="row no-wrap items-center">
          <div class="logo-container">
            <q-btn flat no-caps to="/" class="logo-btn">
              <span class="text-h5 logo-text">Therapify</span>
            </q-btn>
          </div>

          <!-- Desktop Navigation -->
          <DesktopNavigation />

          <!-- Mobile Navigation -->
          <MobileNavigation @toggle-drawer="toggleDrawer" />
        </q-toolbar-title>
      </q-toolbar>
    </q-header>

    <!-- Mobile Drawer -->
    <q-drawer v-model="drawerOpen" side="right" overlay bordered :width="250" class="bg-white">
      <q-scroll-area class="fit">
        <q-list padding>
          <q-item clickable v-ripple to="/" exact class="mobile-nav-item">
            <q-item-section>{{ $t('Ana Sayfa') }}</q-item-section>
          </q-item>
          <q-item clickable v-ripple @click="navigateAndScroll('services')" class="mobile-nav-item">
            <q-item-section>{{ $t('Hizmetlerimiz') }}</q-item-section>
          </q-item>
          <q-item
            clickable
            v-ripple
            @click="navigateAndScroll('therapists')"
            class="mobile-nav-item"
          >
            <q-item-section>{{ $t('Terapistler') }}</q-item-section>
          </q-item>
          <q-item clickable v-ripple to="/contact" exact class="mobile-nav-item">
            <q-item-section>{{ $t('İletişim') }}</q-item-section>
          </q-item>
          <q-separator class="q-my-md" />
          <q-item
            v-if="!isAuthenticated"
            clickable
            v-ripple
            to="/login"
            exact
            class="mobile-nav-item"
          >
            <q-item-section>{{ $t('Giriş') }}</q-item-section>
          </q-item>
          <q-item
            v-if="!isAuthenticated"
            clickable
            v-ripple
            to="/register"
            exact
            class="mobile-nav-item"
          >
            <q-item-section>{{ $t('Kayıt Ol') }}</q-item-section>
          </q-item>
          <q-item v-if="isAuthenticated && user">
            <!-- Profile Dropdown -->
            <q-btn-dropdown
              class="glossy full-width"
              color="secondary"
              :label="$t('Hoş Geldin') + ' ' + user.first_name"
            >
              <div class="row no-wrap q-pa-md">
                <div class="column items-center">
                  <div class="text-h6 q-mb-md">Profil</div>
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
          </q-item>
        </q-list>
      </q-scroll-area>
    </q-drawer>

    <!-- Page Content -->
    <q-page-container>
      <router-view />
    </q-page-container>

    <!-- Footer -->
    <TheFooter />
  </q-layout>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import DesktopNavigation from 'components/layout/DesktopNavigation.vue'
import MobileNavigation from 'components/layout/MobileNavigation.vue'
import TheFooter from 'components/layout/TheFooter.vue'
import { useAuthStore } from 'stores/auth'
import { storeToRefs } from 'pinia'
import { Notify } from 'quasar'
import { useRouter } from 'vue-router'




const getFileUrl = (path: string | undefined) => {
  if (!path) return 'https://cdn.quasar.dev/img/boy-avatar.png'

  // Check if the path is a base64 image
  if (path.startsWith('data:image')) {
    return path
  }

  // Handle file path images
  const filename = path.split('/').pop()
  return `http://localhost/uploads/profile_images/${filename}`
}

const drawerOpen = ref(false)
const authStore = useAuthStore()
const { isAuthenticated, user } = storeToRefs(authStore)
const router = useRouter()

const handleLogout = async () => {
  drawerOpen.value = false
  Notify.create({
    color: 'info',
    message: 'çıkış yapıldı',
    position: 'top-right',
  })
  await authStore.logout()
}

const toggleDrawer = () => {
  drawerOpen.value = !drawerOpen.value
}

const navigateAndScroll = async (sectionId: string) => {
  drawerOpen.value = false // Close drawer first

  if (router.currentRoute.value.path !== '/') {
    await router.push('/')
    // Wait for navigation and DOM update
    setTimeout(() => {
      const element = document.getElementById(sectionId)
      if (element) {
        element.scrollIntoView({ behavior: 'smooth' })
      }
    }, 100)
  } else {
    const element = document.getElementById(sectionId)
    if (element) {
      element.scrollIntoView({ behavior: 'smooth' })
    }
  }
}
</script>

<style lang="scss" scoped>
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

.q-header {
  background: rgba(255, 255, 255, 0.95) !important;
  backdrop-filter: blur(10px);
  border-bottom: 1px solid rgba($primary, 0.1);
  box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);

  .q-toolbar {
    height: 70px;
  }

  .q-toolbar-title {
    padding: 0;
  }
}

.logo-container {
  flex: 0 0 auto;
  margin-right: 2rem;

  .logo-btn {
    font-weight: 700;
    padding: 0;
    min-height: unset;

    .logo-text {
      letter-spacing: -0.5px;
      background: linear-gradient(135deg, $primary, $secondary);
      background-clip: text;
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      font-size: 1.8rem;
      line-height: 1;
    }
  }
}

.mobile-nav-item {
  transition: all 0.3s ease;
  border-radius: 8px;
  margin: 4px 0;

  &:hover {
    background: linear-gradient(135deg, rgba($primary, 0.1), rgba($secondary, 0.1));
    color: $primary;
  }

  &.q-router-link-exact-active {
    background: linear-gradient(135deg, rgba($primary, 0.1), rgba($secondary, 0.1));
    color: $primary;
    font-weight: 500;
  }
}

:deep(.q-page-container) {
  min-height: calc(100vh - 70px);
  display: flex;
  flex-direction: column;
  background: linear-gradient(135deg, $soft-bg, white);

  .q-page {
    flex: 1 0 auto;
  }
}

@media (max-width: 1200px) {
  .container {
    padding: 0 2rem;
  }
}

@media (max-width: 767px) {
  .container {
    padding: 0 1rem;
  }

  .logo-container {
    margin-right: 0;
  }
}
</style>
