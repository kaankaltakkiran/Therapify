<template>
  <q-layout view="hHh lpR fFf">
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
          <MobileNavigation />
        </q-toolbar-title>
      </q-toolbar>
    </q-header>

    <!-- Page Content -->
    <q-page-container>
      <router-view />
    </q-page-container>

    <!-- Footer -->
    <TheFooter />
  </q-layout>
</template>

<script setup lang="ts">
import DesktopNavigation from 'components/layout/DesktopNavigation.vue'
import MobileNavigation from 'components/layout/MobileNavigation.vue'
import TheFooter from 'components/layout/TheFooter.vue'
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
