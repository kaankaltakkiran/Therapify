<template>
  <q-page class="q-pa-md">
    <div class="text-h4 q-mb-md">Destek Talepleri</div>

    <!-- Messages Table -->
    <q-table
      :rows="messages"
      :columns="columns"
      row-key="id"
      :loading="loading"
      :pagination="initialPagination"
      class="messages-table"
    >
      <!-- Status Column -->
      <template v-slot:body-cell-status="props">
        <q-td :props="props">
          <q-chip
            :color="getStatusColor(props.value)"
            text-color="white"
            size="sm"
          >
            {{ getStatusLabel(props.value) }}
          </q-chip>
        </q-td>
      </template>

      <!-- Actions Column -->
      <template v-slot:body-cell-actions="props">
        <q-td :props="props" class="q-gutter-sm">
          <q-btn
            flat
            round
            color="primary"
            icon="visibility"
            size="sm"
            @click="openMessageDialog(props.row)"
          >
            <q-tooltip>Mesajı Görüntüle</q-tooltip>
          </q-btn>
          <q-btn
            flat
            round
            :color="props.row.status === 'new' ? 'positive' : 'grey'"
            :icon="props.row.status === 'new' ? 'done' : 'undo'"
            size="sm"
            @click="updateMessageStatus(props.row.id, props.row.status === 'new' ? 'read' : 'new')"
          >
            <q-tooltip>{{ props.row.status === 'new' ? 'Okundu Olarak İşaretle' : 'Okunmadı Olarak İşaretle' }}</q-tooltip>
          </q-btn>
        </q-td>
      </template>
    </q-table>

    <!-- Message Dialog -->
    <q-dialog v-model="messageDialog" persistent>
      <q-card style="min-width: 350px">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-h6">Destek Mesajı</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>

        <q-card-section v-if="selectedMessage">
          <div class="q-mb-sm">
            <div class="text-subtitle2">Gönderen</div>
            <div>{{ selectedMessage.first_name }} {{ selectedMessage.last_name }}</div>
          </div>
          <div class="q-mb-sm">
            <div class="text-subtitle2">E-posta</div>
            <div>{{ selectedMessage.email }}</div>
          </div>
          <div class="q-mb-sm">
            <div class="text-subtitle2">Tarih</div>
            <div>{{ formatDate(selectedMessage.created_at) }}</div>
          </div>
          <div>
            <div class="text-subtitle2">Mesaj</div>
            <div style="white-space: pre-wrap">{{ selectedMessage.message }}</div>
          </div>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Kapat" color="primary" v-close-popup />
          <q-btn
            v-if="selectedMessage?.status === 'new'"
            unelevated
            label="Okundu Olarak İşaretle"
            color="primary"
            @click="markAsRead"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { api } from 'src/boot/axios'
import { useQuasar } from 'quasar'
import { date } from 'quasar'

const $q = useQuasar()

interface Message {
  id: number
  first_name: string
  last_name: string
  email: string
  message: string
  status: 'new' | 'read'
  created_at: string
  updated_at: string
}

const loading = ref(false)
const messages = ref<Message[]>([])
const messageDialog = ref(false)
const selectedMessage = ref<Message | null>(null)

const initialPagination = {
  sortBy: 'created_at',
  descending: true,
  rowsPerPage: 10
}

const columns = [
  {
    name: 'first_name',
    required: true,
    label: 'Ad',
    align: 'left' as const,
    field: 'first_name',
    sortable: true
  },
  {
    name: 'last_name',
    required: true,
    label: 'Soyad',
    align: 'left' as const,
    field: 'last_name',
    sortable: true
  },
  {
    name: 'email',
    required: true,
    label: 'E-posta',
    align: 'left' as const,
    field: 'email',
    sortable: true
  },
  {
    name: 'status',
    required: true,
    label: 'Durum',
    align: 'left' as const,
    field: 'status',
    sortable: true
  },
  {
    name: 'created_at',
    required: true,
    label: 'Tarih',
    align: 'left' as const,
    field: 'created_at',
    format: (val: string) => formatDate(val),
    sortable: true
  },
  {
    name: 'actions',
    required: true,
    label: 'İşlemler',
    align: 'center' as const,
    field: 'actions'
  }
]

const fetchMessages = async () => {
  loading.value = true
  try {
    const response = await api.post('/admin.php', {
      method: 'get-contact-messages'
    })

    if (response.data.success) {
      messages.value = response.data.messages
    }
  } catch (error) {
    console.error('Error fetching messages:', error)
    $q.notify({
      type: 'negative',
      message: 'Mesajlar yüklenirken bir hata oluştu',
      position: 'top'
    })
  } finally {
    loading.value = false
  }
}

const updateMessageStatus = async (messageId: number, status: string) => {
  try {
    const response = await api.post('/admin.php', {
      method: 'update-contact-status',
      messageId,
      status
    })

    if (response.data.success) {
      await fetchMessages()
      // Emit event to update unread count
      const event = new CustomEvent('update-unread-count')
      window.dispatchEvent(event)
      
      $q.notify({
        type: 'positive',
        message: 'Mesaj durumu güncellendi',
        position: 'top'
      })
    }
  } catch (error) {
    console.error('Error updating message status:', error)
    $q.notify({
      type: 'negative',
      message: 'Mesaj durumu güncellenirken bir hata oluştu',
      position: 'top'
    })
  }
}

const openMessageDialog = (message: Message) => {
  selectedMessage.value = message
  messageDialog.value = true
}

const markAsRead = async () => {
  if (selectedMessage.value) {
    await updateMessageStatus(selectedMessage.value.id, 'read')
    messageDialog.value = false
  }
}

const getStatusColor = (status: string) => {
  switch (status) {
    case 'new':
      return 'primary'
    case 'read':
      return 'positive'
    default:
      return 'grey'
  }
}

const getStatusLabel = (status: string) => {
  switch (status) {
    case 'new':
      return 'Yeni'
    case 'read':
      return 'Okundu'
    default:
      return status
  }
}

const formatDate = (dateStr: string) => {
  return date.formatDate(dateStr, 'DD.MM.YYYY HH:mm')
}

onMounted(() => {
  fetchMessages()
})
</script>

<style lang="scss" scoped>
.messages-table {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.12);
}

.message-dialog {
  .message-content {
    padding: 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.12);
    max-width: 500px;
    width: 90%;
    margin: 0 auto;
  }
}
</style> 