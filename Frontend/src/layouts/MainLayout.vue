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
    <q-drawer
      v-model="drawerOpen"
      side="right"
      overlay
      bordered
      :width="250"
      class="bg-white"
    >
      <q-scroll-area class="fit">
        <q-list padding>
          <q-item clickable v-ripple @click="scrollToSection('services')" class="mobile-nav-item">
            <q-item-section>Nasıl Çalışır?</q-item-section>
          </q-item>
          <q-item clickable v-ripple @click="scrollToSection('therapists')" class="mobile-nav-item">
            <q-item-section>Terapistler İçin</q-item-section>
          </q-item>
          <q-item clickable v-ripple to="/therapists" class="mobile-nav-item">
            <q-item-section>Terapistlerimiz</q-item-section>
          </q-item>
          <q-item clickable v-ripple to="/contact" class="mobile-nav-item">
            <q-item-section>İletişim</q-item-section>
          </q-item>
          <q-separator class="q-my-md" />
          <q-item clickable v-ripple to="/auth/login" class="mobile-nav-item">
            <q-item-section>Giriş</q-item-section>
          </q-item>
          <q-item clickable v-ripple to="/auth/register" class="mobile-nav-item">
            <q-item-section>Kayıt Ol</q-item-section>
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

const drawerOpen = ref(false)

const toggleDrawer = () => {
  drawerOpen.value = !drawerOpen.value
}

const scrollToSection = (sectionId: string) => {
  const element = document.getElementById(sectionId)
  if (element) {
    element.scrollIntoView({ behavior: 'smooth' })
    drawerOpen.value = false
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
