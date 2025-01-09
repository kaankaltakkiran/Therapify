<template>
  <q-page class="q-pa-md">
    <div class="row items-center q-mb-lg">
      <div class="text-h5 q-mr-md">Terapist Başvuruları</div>
      <q-space />
      <q-select
        v-model="filter"
        :options="statusOptions"
        label="Durum Filtresi"
        outlined
        dense
        options-dense
        emit-value
        map-options
        clearable
        style="width: 200px"
        class="q-mr-sm"
      />
      <q-input
        v-model="search"
        placeholder="Ara..."
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

    <!-- Applications Table -->
    <q-table
      :rows="filteredApplications"
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
                <img :src="props.row.user.user_img" />
              </q-avatar>
              <div class="q-ml-sm">
                {{ props.row.user.first_name }} {{ props.row.user.last_name }}
                <div class="text-caption">{{ props.row.user.email }}</div>
              </div>
            </div>
          </q-td>
          <q-td key="education" :props="props">{{ props.row.education }}</q-td>
          <q-td key="license_number" :props="props">{{ props.row.license_number }}</q-td>
          <q-td key="experience_years" :props="props">{{ props.row.experience_years }} yıl</q-td>
          <q-td key="application_status" :props="props">
            <q-chip
              :color="getStatusColor(props.row.application_status)"
              text-color="white"
              size="sm"
              square
            >
              {{ getStatusLabel(props.row.application_status) }}
            </q-chip>
          </q-td>
          <q-td key="created_at" :props="props">
            {{ new Date(props.row.created_at).toLocaleDateString('tr-TR') }}
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
              <q-btn
                v-if="props.row.application_status === 'pending'"
                flat
                round
                color="positive"
                icon="check_circle"
                size="sm"
                @click="confirmApprove(props.row)"
              >
                <q-tooltip>Onayla</q-tooltip>
              </q-btn>
              <q-btn
                v-if="props.row.application_status === 'pending'"
                flat
                round
                color="negative"
                icon="cancel"
                size="sm"
                @click="openRejectDialog(props.row)"
              >
                <q-tooltip>Reddet</q-tooltip>
              </q-btn>
            </div>
          </q-td>
        </q-tr>
      </template>
    </q-table>

    <!-- View Application Dialog -->
    <q-dialog v-model="viewDialog" maximized persistent>
      <q-card class="column">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-h6">Başvuru Detayları</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>

        <q-card-section class="q-pa-lg scroll" v-if="selectedApplication">
          <div class="row q-col-gutter-lg">
            <!-- User Information -->
            <div class="col-12 col-md-4">
              <q-card flat bordered>
                <q-card-section>
                  <div class="text-h6 q-mb-md">Kişisel Bilgiler</div>
                  <div class="row items-center q-mb-md">
                    <q-avatar size="72px">
                      <img :src="selectedApplication.user.user_img" />
                    </q-avatar>
                    <div class="q-ml-md">
                      <div class="text-subtitle1">
                        {{ selectedApplication.user.first_name }}
                        {{ selectedApplication.user.last_name }}
                      </div>
                      <div class="text-caption">{{ selectedApplication.user.email }}</div>
                      <div class="text-caption">{{ selectedApplication.user.phone_number }}</div>
                    </div>
                  </div>
                  <div class="text-caption q-mb-sm">Adres</div>
                  <div class="text-body2">{{ selectedApplication.user.address }}</div>
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
                      <div class="text-caption q-mb-sm">Eğitim</div>
                      <div class="text-body2 q-mb-md">{{ selectedApplication.education }}</div>

                      <div class="text-caption q-mb-sm">Lisans Numarası</div>
                      <div class="text-body2 q-mb-md">{{ selectedApplication.license_number }}</div>

                      <div class="text-caption q-mb-sm">Deneyim</div>
                      <div class="text-body2 q-mb-md">
                        {{ selectedApplication.experience_years }} yıl
                      </div>
                    </div>

                    <div class="col-12 col-md-6">
                      <div class="text-caption q-mb-sm">Belgeler</div>
                      <div class="q-gutter-y-sm">
                        <q-btn
                          outline
                          color="primary"
                          icon="description"
                          :label="'CV'"
                          class="full-width"
                          :href="selectedApplication.cv_file"
                          target="_blank"
                        />
                        <q-btn
                          outline
                          color="primary"
                          icon="school"
                          label="Diploma"
                          class="full-width"
                          :href="selectedApplication.diploma_file"
                          target="_blank"
                        />
                        <q-btn
                          outline
                          color="primary"
                          icon="badge"
                          label="Lisans Belgesi"
                          class="full-width"
                          :href="selectedApplication.license_file"
                          target="_blank"
                        />
                      </div>
                    </div>
                  </div>
                </q-card-section>
              </q-card>

              <!-- Application Status -->
              <q-card flat bordered class="q-mt-md">
                <q-card-section>
                  <div class="text-h6 q-mb-md">Başvuru Durumu</div>
                  <div class="row items-center q-mb-md">
                    <q-chip
                      :color="getStatusColor(selectedApplication.application_status)"
                      text-color="white"
                    >
                      {{ getStatusLabel(selectedApplication.application_status) }}
                    </q-chip>
                    <div class="q-ml-md text-caption">
                      Son güncelleme:
                      {{ new Date(selectedApplication.updated_at).toLocaleString('tr-TR') }}
                    </div>
                  </div>

                  <template v-if="selectedApplication.admin_notes">
                    <div class="text-caption q-mb-sm">Admin Notları</div>
                    <div class="text-body2">{{ selectedApplication.admin_notes }}</div>
                  </template>

                  <!-- Action Buttons -->
                  <div
                    v-if="selectedApplication.application_status === 'pending'"
                    class="row q-gutter-sm q-mt-lg"
                  >
                    <q-btn
                      color="positive"
                      icon="check_circle"
                      label="Onayla"
                      @click="confirmApprove(selectedApplication)"
                    />
                    <q-btn
                      color="negative"
                      icon="cancel"
                      label="Reddet"
                      @click="openRejectDialog(selectedApplication)"
                    />
                  </div>
                </q-card-section>
              </q-card>
            </div>
          </div>
        </q-card-section>
      </q-card>
    </q-dialog>

    <!-- Reject Dialog -->
    <q-dialog v-model="rejectDialog" persistent>
      <q-card style="min-width: 350px">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-h6">Başvuruyu Reddet</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>

        <q-card-section class="q-pt-lg">
          <q-input
            v-model="rejectNotes"
            label="Red Nedeni *"
            type="textarea"
            autogrow
            :rules="[(val) => !!val || 'Red nedeni zorunludur']"
          />
        </q-card-section>

        <q-card-actions align="right" class="text-primary">
          <q-btn flat label="İptal" v-close-popup />
          <q-btn flat label="Reddet" color="negative" @click="handleReject" :loading="submitting" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useQuasar } from 'quasar'
import { api } from 'src/boot/axios'

const $q = useQuasar()

interface User {
  id: number
  first_name: string
  last_name: string
  email: string
  address: string
  phone_number: string
  user_img: string
}

interface TherapistApplication {
  id: number
  user_id: number
  user: User
  education: string
  license_number: string
  experience_years: number
  cv_file: string
  diploma_file: string
  license_file: string
  application_status: 'pending' | 'approved' | 'rejected'
  admin_notes: string | null
  created_at: string
  updated_at: string
}

// State
const loading = ref(false)
const search = ref('')
const filter = ref<string | null>(null)
const viewDialog = ref(false)
const rejectDialog = ref(false)
const selectedApplication = ref<TherapistApplication | null>(null)
const rejectNotes = ref('')
const submitting = ref(false)
const applications = ref<TherapistApplication[]>([])

// Table configuration
const initialPagination = {
  sortBy: 'created_at',
  descending: true,
  page: 1,
  rowsPerPage: 10,
}

const columns = [
  {
    name: 'user',
    required: true,
    label: 'Başvuran',
    align: 'left' as const,
    field: (row: TherapistApplication) => row.user,
    sortable: true,
  },
  {
    name: 'education',
    align: 'left' as const,
    label: 'Eğitim',
    field: 'education',
    sortable: true,
  },
  {
    name: 'license_number',
    align: 'left' as const,
    label: 'Lisans No',
    field: 'license_number',
    sortable: true,
  },
  {
    name: 'experience_years',
    align: 'left' as const,
    label: 'Deneyim',
    field: 'experience_years',
    sortable: true,
  },
  {
    name: 'application_status',
    align: 'left' as const,
    label: 'Durum',
    field: 'application_status',
    sortable: true,
  },
  {
    name: 'created_at',
    align: 'left' as const,
    label: 'Başvuru Tarihi',
    field: 'created_at',
    sortable: true,
  },
  {
    name: 'actions',
    align: 'right' as const,
    label: 'İşlemler',
    field: 'id',
    sortable: false,
  },
]

// Status options
const statusOptions = [
  { label: 'Bekliyor', value: 'pending' },
  { label: 'Onaylandı', value: 'approved' },
  { label: 'Reddedildi', value: 'rejected' },
]

// Methods
const getStatusColor = (status: TherapistApplication['application_status']) => {
  switch (status) {
    case 'approved':
      return 'positive'
    case 'rejected':
      return 'negative'
    default:
      return 'warning'
  }
}

const getStatusLabel = (status: TherapistApplication['application_status']) => {
  switch (status) {
    case 'approved':
      return 'Onaylandı'
    case 'rejected':
      return 'Reddedildi'
    default:
      return 'Bekliyor'
  }
}

const openViewDialog = (application: TherapistApplication) => {
  selectedApplication.value = application
  viewDialog.value = true
}

const openRejectDialog = (application: TherapistApplication) => {
  selectedApplication.value = application
  rejectNotes.value = ''
  rejectDialog.value = true
}

const fetchApplications = async () => {
  loading.value = true
  try {
    const response = await api.post('/admin.php', {
      method: 'get-therapist-applications',
    })
    console.log(response.data)

    if (response.data.success) {
      applications.value = response.data.applications
    } else {
      throw new Error(response.data.error || 'Failed to fetch applications')
    }
  } catch (error) {
    console.error('Fetch error:', error)
    $q.notify({
      type: 'negative',
      message: error instanceof Error ? error.message : 'Failed to fetch applications',
      position: 'top',
    })
  } finally {
    loading.value = false
  }
}

const updateApplicationStatus = async (
  application: TherapistApplication,
  status: 'approved' | 'rejected',
  notes: string = '',
) => {
  try {
    const response = await api.post('/admin.php', {
      method: 'update-application-status',
      application_id: application.id,
      status,
      admin_notes: notes,
    })

    if (response.data.success) {
      // Update local state
      const index = applications.value.findIndex((app) => app.id === application.id)
      if (index !== -1) {
        applications.value[index] = {
          ...application,
          application_status: status,
          admin_notes: notes,
          updated_at: new Date().toISOString(),
        }
      }

      $q.notify({
        type: 'positive',
        message: `Başvuru ${status === 'approved' ? 'onaylandı' : 'reddedildi'}`,
        position: 'top',
      })

      rejectDialog.value = false
      viewDialog.value = false
    } else {
      throw new Error(response.data.error || 'Failed to update application status')
    }
  } catch (error) {
    console.error('Update error:', error)
    $q.notify({
      type: 'negative',
      message: error instanceof Error ? error.message : 'Failed to update application status',
      position: 'top',
    })
  }
}

const confirmApprove = (application: TherapistApplication) => {
  $q.dialog({
    title: 'Onay',
    message: 'Bu başvuruyu onaylamak istediğinizden emin misiniz?',
    cancel: true,
    persistent: true,
  }).onOk(async () => {
    await updateApplicationStatus(application, 'approved')
  })
}

const handleReject = async () => {
  if (!rejectNotes.value) {
    $q.notify({
      type: 'warning',
      message: 'Lütfen red nedenini giriniz',
      position: 'top',
    })
    return
  }

  submitting.value = true
  try {
    if (selectedApplication.value) {
      await updateApplicationStatus(selectedApplication.value, 'rejected', rejectNotes.value)
    }
  } finally {
    submitting.value = false
  }
}

// Computed
const filteredApplications = computed(() => {
  let filtered = [...applications.value]

  // Apply status filter
  if (filter.value) {
    filtered = filtered.filter((app) => app.application_status === filter.value)
  }

  // Apply search filter
  if (search.value) {
    const searchLower = search.value.toLowerCase()
    filtered = filtered.filter(
      (app) =>
        app.user.first_name.toLowerCase().includes(searchLower) ||
        app.user.last_name.toLowerCase().includes(searchLower) ||
        app.user.email.toLowerCase().includes(searchLower) ||
        app.license_number.toLowerCase().includes(searchLower) ||
        app.education.toLowerCase().includes(searchLower),
    )
  }

  return filtered
})

// Fetch applications on mount
onMounted(() => {
  fetchApplications()
})
</script>

<style lang="scss" scoped>
.q-table {
  background: white;
  border-radius: $generic-border-radius;
}

.document-preview {
  width: 100%;
  height: 500px;
  border: none;
  border-radius: $generic-border-radius;
}
</style>
