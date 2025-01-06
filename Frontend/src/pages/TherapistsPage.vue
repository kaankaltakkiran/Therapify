<template>
  <q-page class="therapists-page">
    <div class="container q-pa-md">
      <!-- Page Header -->
      <div class="text-center q-mb-xl">
        <h1 class="text-h3 text-weight-bold q-mb-md">Terapistlerimiz</h1>
        <p class="text-h6 text-grey-8 q-mb-xl">
          Uzman terapistlerimizle tanışın ve size en uygun olanı seçin.
        </p>
      </div>

      <!-- Search and Filter Section -->
      <div class="row q-col-gutter-md q-mb-lg">
        <div class="col-12 col-md-6">
          <q-input
            v-model="search"
            outlined
            dense
            placeholder="Terapist ara..."
            class="search-input"
          >
            <template v-slot:prepend>
              <q-icon name="search" />
            </template>
          </q-input>
        </div>
        <div class="col-12 col-md-3">
          <q-select
            v-model="selectedSpecialty"
            :options="specialties"
            outlined
            dense
            label="Uzmanlık Alanı"
            emit-value
            map-options
            clearable
            options-dense
            class="specialty-select"
          />
        </div>
        <div class="col-12 col-md-3">
          <q-select
            v-model="selectedRating"
            :options="ratings"
            outlined
            dense
            label="Minimum Puan"
            emit-value
            map-options
            clearable
            options-dense
            class="rating-select"
          />
        </div>
      </div>

      <!-- Therapists Grid -->
      <div class="row q-col-gutter-lg">
        <div v-for="therapist in filteredTherapists" :key="therapist.id" class="col-12 col-md-4">
          <q-card class="therapist-card">
            <q-img
              :src="therapist.image"
              :ratio="1"
              class="therapist-image"
              style="height: 300px"
            />

            <q-card-section>
              <div class="row items-center q-mb-sm">
                <div class="text-h6">{{ therapist.name }}</div>
                <q-space />
                <div class="row items-center">
                  <q-icon name="star" color="amber" size="sm" />
                  <span class="q-ml-sm text-subtitle1">{{ therapist.rating }}/5</span>
                </div>
              </div>

              <div class="text-subtitle2 text-primary q-mb-sm">{{ therapist.specialty }}</div>

              <p class="text-body2 text-grey-8 q-mb-md">{{ therapist.description }}</p>

              <div class="row items-center q-gutter-x-md">
                <q-chip
                  v-for="tag in therapist.tags"
                  :key="tag"
                  outline
                  color="primary"
                  text-color="primary"
                  size="sm"
                >
                  {{ tag }}
                </q-chip>
              </div>
            </q-card-section>

            <q-separator />

            <q-card-actions align="right">
              <q-btn flat color="grey" label="Profili Görüntüle" />
              <q-btn unelevated color="primary" label="Randevu Al" />
            </q-card-actions>
          </q-card>
        </div>
      </div>
    </div>
  </q-page>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'

interface Therapist {
  id: number
  name: string
  image: string
  specialty: string
  rating: number
  description: string
  tags: string[]
}

interface Rating {
  label: string
  value: number
}

const search = ref('')
const selectedSpecialty = ref<string | null>(null)
const selectedRating = ref<number | null>(null)

const specialties = [
  'Klinik Psikoloji',
  'Aile Terapisi',
  'Çift Terapisi',
  'Çocuk ve Ergen',
  'Depresyon',
  'Anksiyete',
]

const ratings: Rating[] = [
  { label: '4 ve üzeri ★★★★☆', value: 4 },
  { label: '3 ve üzeri ★★★☆☆', value: 3 },
  { label: 'Tümü', value: 0 },
]

// Sample therapist data
const therapists = ref<Therapist[]>([
  {
    id: 1,
    name: 'Dr. Ayşe Yılmaz',
    image: 'https://cdn.quasar.dev/img/mountains.jpg', // Replace with actual therapist image
    specialty: 'Klinik Psikoloji',
    rating: 2.8,
    description:
      '10 yıllık deneyimle, depresyon, anksiyete ve stres yönetimi konularında uzmanlaşmış klinik psikolog.',
    tags: ['Depresyon', 'Anksiyete', 'Stres Yönetimi'],
  },
  {
    id: 2,
    name: 'Dr. Mehmet Demir',
    image: 'https://cdn.quasar.dev/img/parallax2.jpg', // Replace with actual therapist image
    specialty: 'Aile Terapisi',
    rating: 4.9,
    description:
      'Aile içi iletişim, evlilik sorunları ve ebeveynlik konularında 15 yıllık deneyime sahip uzman.',
    tags: ['Aile Terapisi', 'Evlilik', 'Ebeveynlik'],
  },
  {
    id: 3,
    name: 'Uzm. Psk. Zeynep Kaya',
    image: 'https://cdn.quasar.dev/img/parallax1.jpg', // Replace with actual therapist image
    specialty: 'Çocuk ve Ergen',
    rating: 4.7,
    description:
      'Çocuk ve ergenlerde davranış bozuklukları, öğrenme güçlükleri konularında uzmanlaşmış psikolog.',
    tags: ['Çocuk Psikolojisi', 'Ergen Terapisi', 'Öğrenme Güçlükleri'],
  },
])

const filteredTherapists = computed(() => {
  return therapists.value.filter((therapist) => {
    const matchesSearch =
      search.value === '' ||
      therapist.name.toLowerCase().includes(search.value.toLowerCase()) ||
      therapist.specialty.toLowerCase().includes(search.value.toLowerCase()) ||
      therapist.description.toLowerCase().includes(search.value.toLowerCase()) ||
      therapist.tags.some((tag) => tag.toLowerCase().includes(search.value.toLowerCase()))

    const matchesSpecialty =
      !selectedSpecialty.value || therapist.specialty === selectedSpecialty.value

    const matchesRating = !selectedRating.value || therapist.rating >= selectedRating.value

    return matchesSearch && matchesSpecialty && matchesRating
  })
})
</script>

<style lang="scss" scoped>
.therapists-page {
  background: linear-gradient(135deg, $soft-bg, white);
  min-height: 100vh;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding-top: 4rem;
  padding-bottom: 4rem;
}

.therapist-card {
  height: 100%;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  border-radius: $generic-border-radius;
  overflow: hidden;

  &:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 30px rgba($primary, 0.2);
  }

  .therapist-image {
    transition: all 0.3s ease;

    &:hover {
      transform: scale(1.05);
    }
  }

  .q-chip {
    transition: all 0.3s ease;

    &:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba($primary, 0.15);
    }
  }
}

.search-input,
.specialty-select,
.rating-select {
  .q-field__control {
    height: 44px;
  }
}

@media (max-width: 767px) {
  .container {
    padding-top: 2rem;
    padding-bottom: 2rem;
  }
}
</style>
