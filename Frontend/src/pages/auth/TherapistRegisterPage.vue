<template>
  <q-page class="register-page">
    <div class="container q-pa-md">
      <div class="row justify-center">
        <div class="col-12 col-md-8">
          <q-card class="register-card q-pa-lg">
            <!-- Logo and Title -->
            <div class="text-center q-mb-xl">
              <router-link to="/" class="logo-link">
                <h4 class="text-h4 logo-text q-mb-md">Therapify</h4>
              </router-link>
              <h5 class="text-h5 text-weight-medium q-mb-sm">Terapist Olarak Katıl</h5>
              <p class="text-grey-8">
                Platformumuza katılarak danışanlarla buluşun ve pratiğinizi genişletin
              </p>
            </div>

            <!-- Registration Form -->
            <q-form @submit="onSubmit" class="q-gutter-md" ref="formRef" greedy>
              <!-- Stepper -->
              <q-stepper v-model="step" ref="stepper" color="primary" animated flat class="q-mb-lg">
                <!-- Personal Information Step -->
                <q-step :name="1" title="Kişisel Bilgiler" icon="person" :done="step > 1">
                  <div class="row q-col-gutter-md">
                    <div class="col-12 col-sm-6">
                      <q-input
                        v-model="form.firstName"
                        label="Ad *"
                        outlined
                        :rules="[(val: string) => !!val || 'Ad alanı zorunludur']"
                      />
                    </div>
                    <div class="col-12 col-sm-6">
                      <q-input
                        v-model="form.lastName"
                        label="Soyad *"
                        outlined
                        :rules="[(val: string) => !!val || 'Soyad alanı zorunludur']"
                      />
                    </div>
                  </div>

                  <q-input
                    v-model="form.email"
                    label="E-posta *"
                    type="email"
                    outlined
                    :rules="[
                      (val: string) => !!val || 'E-posta alanı zorunludur',
                      (val: string) => isValidEmail(val) || 'Geçerli bir e-posta adresi giriniz',
                    ]"
                  />

                  <q-input
                    v-model="form.password"
                    label="Şifre *"
                    type="password"
                    outlined
                    :rules="[
                      (val: string) => !!val || 'Şifre alanı zorunludur',
                      (val: string) => val.length >= 6 || 'Şifre en az 6 karakter olmalıdır',
                    ]"
                  />

                  <q-input
                    v-model="form.confirmPassword"
                    label="Şifre Tekrar *"
                    type="password"
                    outlined
                    :rules="[
                      (val: string) => !!val || 'Şifre tekrar alanı zorunludur',
                      (val: string) => val === form.password || 'Şifreler eşleşmiyor',
                    ]"
                  />

                  <q-input
                    v-model="form.phone"
                    label="Telefon *"
                    outlined
                    mask="(###) ### ## ##"
                    :rules="[(val: string) => !!val || 'Telefon alanı zorunludur']"
                  >
                    <template v-slot:prepend>
                      <div class="text-grey-7">+90</div>
                    </template>
                  </q-input>

                  <q-input
                    v-model="form.birthDate"
                    label="Doğum Tarihi *"
                    outlined
                    type="date"
                    mask="####/##/##"
                    :rules="[(val: string) => !!val || 'Doğum tarihi zorunludur']"
                  >
                  </q-input>

                  <q-input
                    v-model="form.address"
                    label="Adres *"
                    type="textarea"
                    outlined
                    autogrow
                    :rules="[(val: string) => !!val || 'Adres alanı zorunludur']"
                  />
                </q-step>

                <!-- Professional Information Step -->
                <q-step :name="2" title="Profesyonel Bilgiler" icon="work" :done="step > 2">
                  <q-select
                    v-model="form.title"
                    :options="titles"
                    label="Ünvan *"
                    outlined
                    emit-value
                    map-options
                    :rules="[(val: string) => !!val || 'Ünvan seçimi zorunludur']"
                  />

                  <q-input
                    v-model="form.licenseNumber"
                    label="Lisans Numarası *"
                    outlined
                    :rules="[(val: string) => !!val || 'Lisans numarası zorunludur']"
                  />

                  <q-input
                    v-model.number="form.experienceYears"
                    label="Deneyim (Yıl) *"
                    type="number"
                    outlined
                    :rules="[
                      (val: number) => !!val || 'Deneyim yılı zorunludur',
                      (val: number) => val >= 0 || 'Geçerli bir değer giriniz',
                    ]"
                  />

                  <q-select
                    v-model="form.specialties"
                    :options="specialties"
                    label="Uzmanlık Alanları *"
                    outlined
                    multiple
                    use-chips
                    :rules="[
                      (val: string[]) =>
                        (val && val.length > 0) || 'En az bir uzmanlık alanı seçiniz',
                    ]"
                  />

                  <q-input
                    v-model="form.education"
                    label="Eğitim Bilgileri *"
                    type="textarea"
                    outlined
                    autogrow
                    :rules="[(val: string) => !!val || 'Eğitim bilgileri zorunludur']"
                    hint="Eğitim geçmişinizi detaylı bir şekilde yazınız"
                  />

                  <q-input
                    v-model="form.aboutText"
                    label="Hakkında *"
                    type="textarea"
                    outlined
                    autogrow
                    :rules="[(val: string) => !!val || 'Hakkında alanı zorunludur']"
                    hint="Kendinizi ve çalışma yaklaşımınızı tanıtın"
                  />

                  <q-input
                    v-model.number="form.sessionFee"
                    label="Seans Ücreti (TL) *"
                    type="number"
                    outlined
                    :rules="[
                      (val: number) => !!val || 'Seans ücreti zorunludur',
                      (val: number) => val > 0 || 'Geçerli bir ücret giriniz',
                    ]"
                  />

                  <q-input
                    v-model.number="form.sessionDuration"
                    label="Seans Süresi (Dakika) *"
                    type="number"
                    outlined
                    :rules="[
                      (val: number) => !!val || 'Seans süresi zorunludur',
                      (val: number) => val > 0 || 'Geçerli bir süre giriniz',
                    ]"
                  />

                  <q-select
                    v-model="form.languagesSpoken"
                    :options="languages"
                    label="Konuşulan Diller *"
                    outlined
                    multiple
                    use-chips
                    :rules="[(val: string[]) => (val && val.length > 0) || 'En az bir dil seçiniz']"
                  />

                  <div class="row q-col-gutter-md">
                    <div class="col-12 col-sm-6">
                      <q-checkbox
                        v-model="form.videoSessionAvailable"
                        label="Online Görüşme Yapıyorum"
                      />
                    </div>
                    <div class="col-12 col-sm-6">
                      <q-checkbox
                        v-model="form.faceToFaceSessionAvailable"
                        label="Yüz Yüze Görüşme Yapıyorum"
                      />
                    </div>
                  </div>

                  <q-input
                    v-model="form.officeAddress"
                    label="Ofis Adresi"
                    type="textarea"
                    outlined
                    autogrow
                    :rules="[
                      (val: string) =>
                        !form.faceToFaceSessionAvailable ||
                        !!val ||
                        'Yüz yüze görüşme için ofis adresi zorunludur',
                    ]"
                    :disable="!form.faceToFaceSessionAvailable"
                  />
                </q-step>

                <!-- Documents Step -->
                <q-step :name="3" title="Belgeler" icon="attach_file" :done="step > 3">
                  <!-- Profile Image -->
                  <div class="row items-center q-col-gutter-md q-mb-md">
                    <div class="col-auto">
                      <q-avatar size="100px">
                        <img :src="imagePreview || 'https://cdn.quasar.dev/img/avatar.png'" />
                      </q-avatar>
                    </div>
                    <div class="col">
                      <q-file
                        v-model="form.userImg"
                        label="Profil Fotoğrafı *"
                        outlined
                        accept=".jpg,.jpeg,.png"
                        :rules="[
                          (val: File | null) => !!val || 'Profil fotoğrafı zorunludur',
                          (val: File | null) =>
                            !val || val.size <= 5242880 || 'Dosya boyutu 5MB\'dan küçük olmalıdır',
                          (val: File | null) =>
                            !val ||
                            validateFileType(val, ['image/jpeg', 'image/png']) ||
                            'Sadece JPG ve PNG formatları kabul edilir',
                        ]"
                        @update:model-value="onImageSelected"
                        @rejected="onFileRejected"
                      >
                        <template v-slot:prepend>
                          <q-icon name="photo_camera" />
                        </template>
                      </q-file>
                      <div class="text-grey-7 text-caption q-mt-sm">
                        JPG veya PNG formatında, maksimum 5MB
                      </div>
                    </div>
                  </div>

                  <!-- CV Upload -->
                  <q-file
                    v-model="form.cvFile"
                    label="Özgeçmiş (CV) *"
                    outlined
                    accept=".pdf,.doc,.docx"
                    :rules="[
                      (val: File | null) => !!val || 'CV yüklemesi zorunludur',
                      (val: File | null) =>
                        !val || val.size <= 10485760 || 'Dosya boyutu 10MB\'dan küçük olmalıdır',
                      (val: File | null) =>
                        !val ||
                        validateFileType(val, [
                          'application/pdf',
                          'application/msword',
                          'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        ]) ||
                        'Sadece PDF ve DOC formatları kabul edilir',
                    ]"
                    @rejected="onFileRejected"
                  >
                    <template v-slot:prepend>
                      <q-icon name="description" />
                    </template>
                  </q-file>

                  <!-- Diploma Upload -->
                  <q-file
                    v-model="form.diplomaFile"
                    label="Diploma *"
                    outlined
                    accept=".pdf,.jpg,.jpeg,.png"
                    :rules="[
                      (val: File | null) => !!val || 'Diploma yüklemesi zorunludur',
                      (val: File | null) =>
                        !val || val.size <= 10485760 || 'Dosya boyutu 10MB\'dan küçük olmalıdır',
                      (val: File | null) =>
                        !val ||
                        validateFileType(val, ['application/pdf', 'image/jpeg', 'image/png']) ||
                        'Sadece PDF, JPG ve PNG formatları kabul edilir',
                    ]"
                    @rejected="onFileRejected"
                  >
                    <template v-slot:prepend>
                      <q-icon name="school" />
                    </template>
                  </q-file>

                  <!-- License Upload -->
                  <q-file
                    v-model="form.licenseFile"
                    label="Lisans Belgesi *"
                    outlined
                    accept=".pdf,.jpg,.jpeg,.png"
                    :rules="[
                      (val: File | null) => !!val || 'Lisans belgesi yüklemesi zorunludur',
                      (val: File | null) =>
                        !val || val.size <= 10485760 || 'Dosya boyutu 10MB\'dan küçük olmalıdır',
                      (val: File | null) =>
                        !val ||
                        validateFileType(val, ['application/pdf', 'image/jpeg', 'image/png']) ||
                        'Sadece PDF, JPG ve PNG formatları kabul edilir',
                    ]"
                    @rejected="onFileRejected"
                  >
                    <template v-slot:prepend>
                      <q-icon name="badge" />
                    </template>
                  </q-file>
                </q-step>

                <!-- Terms Step -->
                <q-step :name="4" title="Onay" icon="done">
                  <q-checkbox
                    v-model="form.acceptTerms"
                    label="Kullanım koşullarını ve gizlilik politikasını kabul ediyorum *"
                    :rules="[(val: boolean) => !!val || 'Kullanım koşullarını kabul etmelisiniz']"
                  />

                  <q-checkbox
                    v-model="form.acceptProfessionalTerms"
                    label="Terapist sözleşmesini okudum ve kabul ediyorum *"
                    :rules="[(val: boolean) => !!val || 'Terapist sözleşmesini kabul etmelisiniz']"
                  />

                  <div class="text-grey-8 q-mt-md">
                    <p>
                      Başvurunuz gönderildikten sonra ekibimiz tarafından incelenecek ve en kısa
                      sürede size dönüş yapılacaktır.
                    </p>
                  </div>
                </q-step>
              </q-stepper>

              <!-- Navigation Buttons -->
              <div class="row justify-between q-mt-md">
                <q-btn v-if="step > 1" flat color="primary" label="Geri" @click="handlePrevious" />
                <q-space />
                <q-btn v-if="step < 4" color="primary" label="İleri" @click="handleNext" />
                <q-btn
                  v-else
                  type="submit"
                  color="primary"
                  label="Başvuruyu Gönder"
                  :loading="submitting"
                />
              </div>
            </q-form>
          </q-card>
        </div>
      </div>
    </div>
  </q-page>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useQuasar, QStepper, QForm } from 'quasar'
import type { QRejectedEntry } from 'quasar'
import { useRouter } from 'vue-router'
import { useAuthStore } from 'src/stores/auth'

const $q = useQuasar()
const router = useRouter()
const stepper = ref<QStepper>()
const formRef = ref<InstanceType<typeof QForm> | null>(null)

interface StepValidation {
  [key: number]: boolean
}

// Add validation state for each step
const stepValidation = ref<StepValidation>({
  1: false,
  2: false,
  3: false,
  4: false,
})

// Function to validate current step
const validateStep = async (stepNumber: number) => {
  const isValid = await formRef.value?.validate()
  stepValidation.value[stepNumber] = !!isValid
  return isValid
}

// Function to handle next step
const handleNext = async () => {
  const isCurrentStepValid = await validateStep(step.value)

  if (!isCurrentStepValid) {
    $q.notify({
      type: 'negative',
      message: 'Lütfen tüm zorunlu alanları doldurunuz.',
      position: 'top',
    })
    return
  }

  stepper.value?.next()
}

// Function to handle previous step
const handlePrevious = () => {
  stepper.value?.previous()
}

interface TherapistForm {
  // Personal Information
  firstName: string
  lastName: string
  email: string
  password: string
  confirmPassword: string
  phone: string
  birthDate: string
  address: string
  userImg: File | null

  // Professional Information
  title: string
  licenseNumber: string
  experienceYears: number
  education: string
  specialties: string[]
  aboutText: string
  sessionFee: number
  sessionDuration: number
  languagesSpoken: string[]
  officeAddress: string
  videoSessionAvailable: boolean
  faceToFaceSessionAvailable: boolean

  // Documents
  cvFile: File | null
  diplomaFile: File | null
  licenseFile: File | null

  // Terms
  acceptTerms: boolean
  acceptProfessionalTerms: boolean
}

const step = ref(1)
const submitting = ref(false)
const imagePreview = ref('')

const titles = ['Dr.', 'Uzm. Psk.', 'Psk.', 'Uzm. Psk. Dan.', 'Psk. Dan.', 'Prof. Dr.', 'Doç. Dr.']

const specialties = [
  'Klinik Psikoloji',
  'Aile Terapisi',
  'Çift Terapisi',
  'Çocuk ve Ergen',
  'Depresyon',
  'Anksiyete',
  'Travma Sonrası Stres Bozukluğu',
  'Obsesif Kompulsif Bozukluk',
  'Yeme Bozuklukları',
  'Bağımlılık',
  'Cinsel Terapi',
  'Oyun Terapisi',
]

const languages = ['Türkçe', 'English', 'Deutsch', 'Français', 'Español', 'العربية', 'Русский']

const form = ref<TherapistForm>({
  firstName: '',
  lastName: '',
  email: '',
  password: '',
  confirmPassword: '',
  phone: '',
  birthDate: '',
  address: '',
  userImg: null,
  title: '',
  licenseNumber: '',
  experienceYears: 0,
  education: '',
  specialties: [],
  aboutText: '',
  sessionFee: 0,
  sessionDuration: 50,
  languagesSpoken: ['Türkçe'],
  officeAddress: '',
  videoSessionAvailable: true,
  faceToFaceSessionAvailable: true,
  cvFile: null,
  diplomaFile: null,
  licenseFile: null,
  acceptTerms: false,
  acceptProfessionalTerms: false,
})

const isValidEmail = (email: string) => {
  const emailPattern =
    /^(?=[a-zA-Z0-9@._%+-]{6,254}$)[a-zA-Z0-9._%+-]{1,64}@(?:[a-zA-Z0-9-]{1,63}\.){1,8}[a-zA-Z]{2,63}$/
  return emailPattern.test(email)
}

const onImageSelected = (file: File | null) => {
  if (file) {
    const reader = new FileReader()
    reader.onload = (e: ProgressEvent<FileReader>) => {
      imagePreview.value = e.target?.result as string
    }
    reader.readAsDataURL(file)
  } else {
    imagePreview.value = ''
  }
}

// File validation function
const validateFileType = (file: File, allowedTypes: string[]): boolean => {
  return allowedTypes.includes(file.type)
}

// File rejection handler
const onFileRejected = (rejectedEntries: QRejectedEntry[]) => {
  rejectedEntries.forEach((entry) => {
    $q.notify({
      type: 'negative',
      message: `${entry.file.name}: ${entry.failedPropValidation ? 'Geçersiz dosya formatı veya boyutu' : entry.failedPropValidation}`,
      position: 'top',
    })
  })
}

const onSubmit = async () => {
  const isCurrentStepValid = await validateStep(step.value)

  if (!isCurrentStepValid) {
    $q.notify({
      type: 'negative',
      message: 'Lütfen tüm zorunlu alanları doldurunuz.',
      position: 'top',
    })
    return
  }

  // Check if all steps are valid
  const allStepsValid = Object.values(stepValidation.value).every(Boolean)

  if (!allStepsValid) {
    $q.notify({
      type: 'negative',
      message: 'Lütfen tüm adımları tamamlayınız.',
      position: 'top',
    })
    return
  }

  submitting.value = true
  try {
    const authStore = useAuthStore()
    const formData = new FormData()

    // Add method identifier
    formData.append('method', 'therapist-register')

    // Personal Information
    formData.append('first_name', form.value.firstName)
    formData.append('last_name', form.value.lastName)
    formData.append('email', form.value.email)
    formData.append('password', form.value.password)
    formData.append('phone_number', form.value.phone)
    formData.append('birth_of_date', form.value.birthDate)
    formData.append('address', form.value.address)

    // File uploads with validation
    if (form.value.userImg && validateFileType(form.value.userImg, ['image/jpeg', 'image/png'])) {
      formData.append('user_img', form.value.userImg)
    }

    // Professional Information
    formData.append('title', form.value.title)
    formData.append('license_number', form.value.licenseNumber)
    formData.append('experience_years', form.value.experienceYears.toString())
    formData.append('education', form.value.education)
    formData.append('specialties', JSON.stringify(form.value.specialties))
    formData.append('about_text', form.value.aboutText)
    formData.append('session_fee', form.value.sessionFee.toString())
    formData.append('session_duration', form.value.sessionDuration.toString())
    formData.append('languages_spoken', JSON.stringify(form.value.languagesSpoken))
    formData.append('office_address', form.value.officeAddress)
    formData.append('video_session_available', form.value.videoSessionAvailable ? '1' : '0')
    formData.append(
      'face_to_face_session_available',
      form.value.faceToFaceSessionAvailable ? '1' : '0',
    )

    // Documents with validation
    if (
      form.value.cvFile &&
      validateFileType(form.value.cvFile, [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
      ])
    ) {
      formData.append('cv_file', form.value.cvFile)
    }

    if (
      form.value.diplomaFile &&
      validateFileType(form.value.diplomaFile, ['application/pdf', 'image/jpeg', 'image/png'])
    ) {
      formData.append('diploma_file', form.value.diplomaFile)
    }

    if (
      form.value.licenseFile &&
      validateFileType(form.value.licenseFile, ['application/pdf', 'image/jpeg', 'image/png'])
    ) {
      formData.append('license_file', form.value.licenseFile)
    }

    const success = await authStore.therapistRegister(formData)
    if (success) {
      router.push('/')
    }
  } catch (error: unknown) {
    console.error('Application error:', error)
    $q.notify({
      type: 'negative',
      message: 'Başvuru sırasında bir hata oluştu. Lütfen tekrar deneyiniz.',
      position: 'top',
    })
  } finally {
    submitting.value = false
  }
}
</script>

<style lang="scss" scoped>
.register-page {
  background: linear-gradient(135deg, $soft-bg, white);
  min-height: 100vh;
  padding: 2rem 0;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
}

.register-card {
  border-radius: $generic-border-radius;
  box-shadow: $card-shadow;
}

.logo-link {
  text-decoration: none;
  color: inherit;

  .logo-text {
    background: linear-gradient(135deg, $primary, $secondary);
    background-clip: text;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-weight: 700;
    margin: 0;
  }
}

:deep(.q-stepper) {
  box-shadow: none;
  background: transparent;

  .q-stepper__tab {
    &--active,
    &--done {
      color: $primary;
    }
  }
}

@media (max-width: 599px) {
  .register-page {
    padding: 1rem 0;
  }

  .register-card {
    padding: 1.5rem !important;
  }
}
</style>
