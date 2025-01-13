<template>
  <div class="lt-md">
    <!-- Language Selector for Mobile -->
    <q-btn-dropdown flat dense round icon="language" color="primary" class="q-mr-sm">
      <q-list>
        <q-item
          v-for="lang in languages"
          :key="lang.code"
          clickable
          v-close-popup
          @click="changeLanguage(lang.code)"
          :active="currentLanguage.code === lang.code"
        >
          <q-item-section avatar>
            <q-avatar size="20px">
              <img :src="lang.flag" :alt="lang.label" />
            </q-avatar>
          </q-item-section>
          <q-item-section>{{ lang.label }}</q-item-section>
          <q-item-section side v-if="currentLanguage.code === lang.code">
            <q-icon name="check" color="primary" />
          </q-item-section>
        </q-item>
      </q-list>
    </q-btn-dropdown>

    <q-btn flat round icon="menu" color="primary" @click="$emit('toggle-drawer')" />
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'

interface Language {
  code: 'tr' | 'en-US' | 'de' | 'fr'
  label: string
  flag: string
}

const { locale } = useI18n()
const currentLangCode = ref<Language['code']>(locale.value as Language['code'])

const languages = [
  {
    code: 'tr',
    label: 'Türkçe',
    flag: '/images/flags/tr.svg',
  },
  {
    code: 'en-US',
    label: 'English',
    flag: '/images/flags/en-US.svg',
  },
] as const satisfies readonly Language[]

const defaultLanguage: Language = languages[0]

const currentLanguage = computed((): Language => {
  const found = languages.find((lang) => lang.code === currentLangCode.value)
  return found ?? defaultLanguage
})

const changeLanguage = (langCode: Language['code']) => {
  currentLangCode.value = langCode
  locale.value = langCode
  localStorage.setItem('language', langCode)
}

defineEmits(['toggle-drawer'])
</script>

<style lang="scss" scoped>
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
