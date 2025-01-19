<template>
  <q-page class="q-pa-md">
    <div class="row items-center q-mb-lg">
      <div class="text-h5 q-mr-md">{{ $t('Onaylı Terapistler') }}</div>
      <q-space />
      <q-input
        v-model="search"
        :placeholder="$t('Ara...')"
        outlined
        dense
        clearable
        class="q-mr-sm"
        style="width: 200px"
      >
        <template v-slot:append>
          <q-icon name="search" />
        </template>
      </q-input>
    </div>

    <!-- Therapists Table -->
    <q-table
      :rows="filteredTherapists"
      :columns="columns"
      row-key="id"
      :loading="loading"
      :pagination="initialPagination"
      flat
      bordered
    >
      <!-- Custom Header Slots -->
      <template v-slot:header="props">
        <q-tr :props="props">
          <q-th v-for="col in props.cols" :key="col.name" :props="props">
            {{ col.label }}
          </q-th>
        </q-tr>
      </template>

      <!-- Custom Body Slots -->
      <template v-slot:body="props">
        <q-tr :props="props">
          <q-td key="user" :props="props">
            <div class="row items-center">
              <q-avatar size="32px">
                <img :src="getFileUrl(props.row.user_img)" />
              </q-avatar>
              <div class="q-ml-sm">
                {{ props.row.first_name }} {{ props.row.last_name }}
                <div class="text-caption">{{ props.row.email }}</div>
              </div>
            </div>
          </q-td>
          <q-td key="title" :props="props">{{ props.row.title }}</q-td>
          <!--      <q-td key="specialties" :props="props">
            <div class="row q-gutter-x-xs">
              <q-chip
                v-for="specialty in props.row.specialties"
                :key="specialty.id"
                color="primary"
                text-color="white"
                size="sm"
              >
                {{ specialty.name }}
              </q-chip>
            </div>
          </q-td> -->
          <q-td key="session_fee" :props="props">{{ props.row.session_fee }} ₺</q-td>
          <q-td key="session_duration" :props="props">{{ props.row.session_duration }} dakika</q-td>
          <q-td key="languages" :props="props">
            <div class="row q-gutter-x-xs">
              <q-chip
                v-for="language in props.row.languages_spoken"
                :key="language"
                size="sm"
                color="primary"
                text-color="white"
              >
                {{ language }}
              </q-chip>
            </div>
          </q-td>
          <q-td key="actions" :props="props">
            <div class="row items-center q-gutter-x-sm justify-end">
              <q-btn
                flat
                round
                color="primary"
                icon="visibility"
                size="sm"
                @click="openViewDialog(props.row)"
              >
                <q-tooltip>Detayları Görüntüle</q-tooltip>
              </q-btn>
            </div>
          </q-td>
        </q-tr>
      </template>
    </q-table>

    <!-- View Therapist Dialog -->
    <q-dialog v-model="viewDialog" maximized persistent>
      <q-card class="column">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-h6">Terapist Detayları</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>

        <q-card-section class="q-pa-lg scroll" v-if="selectedTherapist">
          <div class="row q-col-gutter-lg">
            <!-- User Information -->
            <div class="col-12 col-md-4">
              <q-card flat bordered>
                <q-card-section>
                  <div class="text-h6 q-mb-md">Kişisel Bilgiler</div>
                  <div class="row items-center q-mb-md">
                    <q-avatar size="72px">
                      <img :src="getFileUrl(selectedTherapist.user_img)" />
                    </q-avatar>
                    <div class="q-ml-md">
                      <div class="text-subtitle1">
                        {{ selectedTherapist.first_name }}
                        {{ selectedTherapist.last_name }}
                      </div>
                      <div class="text-caption">{{ selectedTherapist.email }}</div>
                      <div class="text-caption">{{ selectedTherapist.phone_number }}</div>
                    </div>
                  </div>
                  <div class="text-caption q-mb-sm">Adres</div>
                  <div class="text-body2">{{ selectedTherapist.address }}</div>
                </q-card-section>
              </q-card>
            </div>

            <!-- Professional Information -->
            <div class="col-12 col-md-8">
              <q-card flat bordered>
                <q-card-section>
                  <div class="text-h6 q-mb-md">Profesyonel Bilgiler</div>
                  <div class="row q-col-gutter-md">
                    <div class="col-12 col-md-6">
                      <div class="text-caption q-mb-sm">Ünvan</div>
                      <div class="text-body2 q-mb-md">{{ selectedTherapist.title }}</div>

                      <div class="text-caption q-mb-sm">Seans Ücreti</div>
                      <div class="text-body2 q-mb-md">{{ selectedTherapist.session_fee }} ₺</div>

                      <div class="text-caption q-mb-sm">Seans Süresi</div>
                      <div class="text-body2 q-mb-md">
                        {{ selectedTherapist.session_duration }} dakika
                      </div>

                      <div class="text-caption q-mb-sm">Konuşulan Diller</div>
                      <div class="text-body2 q-mb-md">
                        <div class="row q-gutter-x-xs">
                          <q-chip
                            v-for="language in selectedTherapist.languages_spoken"
                            :key="language"
                            color="primary"
                            text-color="white"
                          >
                            {{ language }}
                          </q-chip>
                        </div>
                      </div>

                      <div class="text-caption q-mb-sm">Uzmanlık Alanları</div>
                      <div class="row q-gutter-x-xs q-mb-md">
                        <q-chip
                          v-for="specialty in selectedTherapist.specialties"
                          :key="specialty.id"
                          color="primary"
                          text-color="white"
                          size="sm"
                        >
                          {{ specialty.name }}
                        </q-chip>
                      </div>
                    </div>

                    <div class="col-12 col-md-6">
                      <div class="text-caption q-mb-sm">Seans Türleri</div>
                      <div class="q-gutter-y-sm">
                        <q-chip
                          :color="selectedTherapist.video_session_available ? 'positive' : 'grey'"
                          text-color="white"
                          icon="videocam"
                        >
                          Online Görüşme
                        </q-chip>
                        <q-chip
                          :color="
                            selectedTherapist.face_to_face_session_available ? 'positive' : 'grey'
                          "
                          text-color="white"
                          icon="person"
                        >
                          Yüz Yüze Görüşme
                        </q-chip>
                      </div>

                      <div v-if="selectedTherapist.office_address" class="q-mt-md">
                        <div class="text-caption q-mb-sm">Ofis Adresi</div>
                        <div class="text-body2">{{ selectedTherapist.office_address }}</div>
                      </div>
                    </div>
                  </div>
                </q-card-section>
              </q-card>

              <!-- About Section -->
              <q-card flat bordered class="q-mt-md">
                <q-card-section>
                  <div class="text-h6 q-mb-md">Hakkında</div>
                  <div class="text-body2">{{ selectedTherapist.about_text }}</div>
                </q-card-section>
              </q-card>
            </div>
          </div>
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useQuasar } from 'quasar'

import { api } from 'src/boot/axios'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

//Terapist bilgilerini tutan interface
interface Therapist {
  id: number
  user_id: number
  first_name: string
  last_name: string
  email: string
  phone_number: string
  address: string
  user_img: string
  title: string
  about_text: string
  session_fee: number
  session_duration: number
  languages_spoken: string
  video_session_available: boolean
  face_to_face_session_available: boolean
  office_address: string | null
  specialties: Array<{ id: number; name: string }>
}

const $q = useQuasar()
const loading = ref(false)
const therapists = ref<Therapist[]>([])
const search = ref('')
const viewDialog = ref(false)
const selectedTherapist = ref<Therapist | null>(null)

const columns = computed(() => [
  {
    name: 'user',
    required: true,
    label: t('Terapist'),
    align: 'left' as const,
    field: (row: Therapist) => row.first_name + ' ' + row.last_name,
    sortable: true,
  },
  {
    name: 'title',
    align: 'left' as const,
    label: t('Ünvan'),
    field: 'title',
    sortable: true,
  },
  {
    name: 'session_fee',
    align: 'left' as const,
    label: t('Seans Ücreti (TL)'),
    field: 'session_fee',
    sortable: true,
  },
  {
    name: 'session_duration',
    align: 'left' as const,
    label: t('Seans Süresi (Dakika)'),
    field: 'session_duration',
    sortable: true,
  },
  {
    name: 'languages',
    align: 'left' as const,
    label: t('Konuşulan Diller'),
    field: 'languages_spoken',
    sortable: true,
  },
  {
    name: 'actions',
    align: 'right' as const,
    label: t('İşlemler'),
    field: 'id',
  },
])

const initialPagination = {
  sortBy: 'first_name',
  descending: false,
  page: 1,
  rowsPerPage: 10,
}

//Terapistleri filtreleme
const filteredTherapists = computed(() => {
  if (!search.value) return therapists.value

  const searchLower = search.value.toLowerCase()
  return therapists.value.filter(
    (therapist) =>
      therapist.first_name.toLowerCase().includes(searchLower) ||
      therapist.last_name.toLowerCase().includes(searchLower) ||
      therapist.email.toLowerCase().includes(searchLower) ||
      therapist.title.toLowerCase().includes(searchLower),
  )
})

//Terapistleri getirme
const fetchTherapists = async () => {
  try {
    loading.value = true
    const response = await api.post('/therapist.php', {
      method: 'get-approved-therapists',
    })
    therapists.value = response.data.therapists
  } catch (error) {
    console.error('Error fetching therapists:', error)
    $q.notify({
      type: 'negative',
      message: 'Terapist listesi alınırken bir hata oluştu.',
    })
  } finally {
    loading.value = false
  }
}

const openViewDialog = (therapist: Therapist) => {
  selectedTherapist.value = therapist
  viewDialog.value = true
}

//Kullanıcının yüklediği dosyaların url'sini getirme
const getFileUrl = (path: string) => {
  if (!path) return ''

  // If it's already a full URL, return as is
  if (path.startsWith('http')) {
    return path
  }

  // For development environment
  if (process.env.DEV) {
    return `http://localhost/${path}`
  }

  // For production environment
  return `https://therapify.kaankaltakkiran.com/api/${path}`
}

onMounted(() => {
  fetchTherapists()
})
</script>
