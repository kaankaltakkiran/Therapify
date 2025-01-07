<template>
  <q-layout view="lHh LpR fFf">
    <q-page-container class="auth-container">
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script setup lang="ts">
// No script needed
</script>

<style lang="scss" scoped>
.auth-container {
  min-height: 100vh;
  background: linear-gradient(135deg, $soft-bg, white);
}
</style>
