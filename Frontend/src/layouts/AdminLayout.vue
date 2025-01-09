<template>
  <!-- Admin Kullanıcının Göreceği Layout -->
  <q-layout view="lHh LpR fFf">
    <!-- Header -->
    <q-header elevated class="bg-white text-dark">
      <q-toolbar class="container">
        <q-btn flat round dense icon="menu" @click="toggleLeftDrawer" />

        <q-toolbar-title class="row no-wrap items-center">
          <div class="logo-container">
            <q-btn flat no-caps to="/admin" class="logo-btn">
              <span class="text-h5 logo-text">Therapify Admin</span>
            </q-btn>
          </div>
        </q-toolbar-title>

        <!-- Admin Profile Menu -->
        <q-btn-dropdown flat no-caps>
          <template v-slot:label>
            <div class="row items-center no-wrap">
              <q-avatar size="32px">
                <img src="https://cdn.quasar.dev/img/avatar.png" />
              </q-avatar>
              <div class="text-subtitle1 q-ml-sm">{{ admin.name }}</div>
            </div>
          </template>

          <q-list>
            <q-item clickable v-close-popup @click="onProfile">
              <q-item-section avatar>
                <q-icon name="person" />
              </q-item-section>
              <q-item-section>Profil</q-item-section>
            </q-item>

            <q-item clickable v-close-popup @click="onSettings">
              <q-item-section avatar>
                <q-icon name="settings" />
              </q-item-section>
              <q-item-section>Ayarlar</q-item-section>
            </q-item>

            <q-separator />

            <q-item clickable v-close-popup @click="onLogout">
              <q-item-section avatar>
                <q-icon name="logout" />
              </q-item-section>
              <q-item-section>Çıkış Yap</q-item-section>
            </q-item>
          </q-list>
        </q-btn-dropdown>
      </q-toolbar>
    </q-header>

    <!-- Left Drawer -->
    <q-drawer
      v-model="leftDrawerOpen"
      show-if-above
      bordered
      :width="280"
      :breakpoint="500"
      class="bg-white"
    >
      <q-scroll-area class="fit">
        <q-list padding>
          <!-- Dashboard -->
          <q-item clickable v-ripple to="/admin" exact>
            <q-item-section avatar>
              <q-icon name="dashboard" />
            </q-item-section>
            <q-item-section>Dashboard</q-item-section>
          </q-item>

          <!-- Users -->
          <q-item clickable v-ripple to="/admin/users">
            <q-item-section avatar>
              <q-icon name="people" />
            </q-item-section>
            <q-item-section>Kullanıcılar</q-item-section>
          </q-item>

          <!-- Therapist Applications -->
          <q-item clickable v-ripple to="/admin/therapist-applications">
            <q-item-section avatar>
              <q-icon name="assignment" />
            </q-item-section>
            <q-item-section>
              <div>Terapist Başvuruları</div>
              <div class="text-caption">{{ pendingApplicationsCount }} yeni başvuru</div>
            </q-item-section>
            <q-item-section side v-if="pendingApplicationsCount > 0">
              <q-badge color="primary" floating>{{ pendingApplicationsCount }}</q-badge>
            </q-item-section>
          </q-item>

          <!-- Settings -->
          <q-item clickable v-ripple to="/admin/settings">
            <q-item-section avatar>
              <q-icon name="settings" />
            </q-item-section>
            <q-item-section>Site Ayarları</q-item-section>
          </q-item>

          <!-- System Stats -->
          <q-expansion-item
            icon="trending_up"
            label="Sistem İstatistikleri"
            caption="Performans ve kullanım"
          >
            <q-list class="q-pl-lg">
              <q-item clickable v-ripple to="/admin/stats/users">
                <q-item-section>Kullanıcı İstatistikleri</q-item-section>
              </q-item>
              <q-item clickable v-ripple to="/admin/stats/sessions">
                <q-item-section>Seans İstatistikleri</q-item-section>
              </q-item>
              <q-item clickable v-ripple to="/admin/stats/revenue">
                <q-item-section>Gelir İstatistikleri</q-item-section>
              </q-item>
            </q-list>
          </q-expansion-item>
        </q-list>
      </q-scroll-area>
    </q-drawer>

    <!-- Page Content -->
    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { useAuthStore } from 'stores/auth'

const router = useRouter()
const $q = useQuasar()
const authStore = useAuthStore()

// State
const leftDrawerOpen = ref(false)
const pendingApplicationsCount = ref(5)

// Get admin user data from sessionStorage
const userStr = sessionStorage.getItem('user')
const userData = userStr ? JSON.parse(userStr) : null

const admin = ref({
  name: userData?.name || 'Admin User',
  email: userData?.email || 'admin@therapify.com',
  user_role: userData?.user_role || 'admin',
})

// Methods
const toggleLeftDrawer = () => {
  leftDrawerOpen.value = !leftDrawerOpen.value
}

const onProfile = () => {
  router.push('/admin/profile')
}

const onSettings = () => {
  router.push('/admin/settings')
}

const onLogout = async () => {
  try {
    await authStore.logout()

    router.push('/')

    $q.notify({
      type: 'positive',
      message: 'Başarıyla çıkış yapıldı',
      position: 'top',
    })
  } catch (error) {
    console.error('Logout error:', error)
    $q.notify({
      type: 'negative',
      message: 'Çıkış yapılırken bir hata oluştu',
      position: 'top',
    })
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
      font-size: 1.5rem;
      line-height: 1;
    }
  }
}

.q-drawer {
  .q-item {
    border-radius: 8px;
    margin: 0 8px;
    padding: 8px 16px;

    &.q-item--active {
      background: linear-gradient(135deg, rgba($primary, 0.1), rgba($secondary, 0.1));
      color: $primary;
      font-weight: 500;

      .q-icon {
        color: $primary;
      }
    }

    &:hover {
      background: rgba($primary, 0.05);
    }
  }

  .q-expansion-item {
    .q-item {
      padding-left: 8px;
      margin-left: 0;
      margin-right: 0;
      border-radius: 0;
    }
  }
}

:deep(.q-page-container) {
  background: linear-gradient(135deg, $soft-bg, white);
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
