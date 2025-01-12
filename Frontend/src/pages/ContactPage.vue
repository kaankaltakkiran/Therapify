<template>
  <q-page class="contact-page">
    <div class="container q-pa-md">
      <!-- Page Header -->
      <div class="text-center q-mb-xl">
        <h1 class="text-h3 text-weight-bold q-mb-md">İletişim</h1>
        <p class="text-h6 text-grey-8 q-mb-xl">
          Sorularınız için bizimle iletişime geçebilirsiniz. Size en kısa sürede dönüş yapacağız.
        </p>
      </div>

      <div class="row q-col-gutter-xl">
        <!-- Contact Information -->
        <div class="col-12 col-md-4">
          <div class="contact-info q-pa-md">
            <h2 class="text-h5 q-mb-lg">İletişim Bilgileri</h2>

            <!-- Address -->
            <div class="contact-item q-mb-lg">
              <div class="row items-center q-mb-sm">
                <q-icon name="location_on" color="primary" size="sm" class="q-mr-sm" />
                <span class="text-subtitle1 text-weight-medium">Adres</span>
              </div>
              <p class="text-body1 text-grey-8">
                Terapi Sokak No: 123, Ruh Sağlığı Mahallesi, İstanbul
              </p>
            </div>

            <!-- Phone -->
            <div class="contact-item q-mb-lg">
              <div class="row items-center q-mb-sm">
                <q-icon name="phone" color="primary" size="sm" class="q-mr-sm" />
                <span class="text-subtitle1 text-weight-medium">Telefon</span>
              </div>
              <a href="tel:+905551234567" class="text-body1 text-grey-8">+90 (555) 123-4567</a>
            </div>

            <!-- Email -->
            <div class="contact-item q-mb-lg">
              <div class="row items-center q-mb-sm">
                <q-icon name="email" color="primary" size="sm" class="q-mr-sm" />
                <span class="text-subtitle1 text-weight-medium">E-posta</span>
              </div>
              <a href="mailto:destek@therapify.com" class="text-body1 text-grey-8 text-no-wrap"
                >destek@therapify.com</a
              >
            </div>

            <!-- Working Hours -->
            <!--     <div class="contact-item">
              <div class="row items-center q-mb-sm">
                <q-icon name="schedule" color="primary" size="sm" class="q-mr-sm" />
                <span class="text-subtitle1 text-weight-medium">Çalışma Saatleri</span>
              </div>
              <p class="text-body1 text-grey-8">Pazartesi - Cuma: 09:00 - 18:00</p>
              <p class="text-body1 text-grey-8">Cumartesi: 10:00 - 14:00</p>
            </div> -->
          </div>
        </div>

        <!-- Contact Form -->
        <div class="col-12 col-md-8">
          <q-card flat bordered class="contact-form-card q-pa-lg">
            <h2 class="text-h5 q-mb-lg">Bize Ulaşın</h2>
            <q-form @submit="onSubmit" class="q-gutter-md">
              <div class="row q-col-gutter-md">
                <div class="col-12 col-md-6">
                  <q-input
                    v-model="form.firstName"
                    label="Ad"
                    :rules="[
                      val => !!val || 'Ad alanı zorunludur',
                      val => val.length >= 2 || 'Ad en az 2 karakter olmalıdır'
                    ]"
                    outlined
                    lazy-rules
                    error-message="Ad alanı zorunludur"
                    hide-bottom-space
                    class="form-input"
                  />
                </div>
                <div class="col-12 col-md-6">
                  <q-input
                    v-model="form.lastName"
                    label="Soyad"
                    :rules="[
                      val => !!val || 'Soyad alanı zorunludur',
                      val => val.length >= 2 || 'Soyad en az 2 karakter olmalıdır'
                    ]"
                    outlined
                    lazy-rules
                    error-message="Soyad alanı zorunludur"
                    hide-bottom-space
                    class="form-input"
                  />
                </div>
              </div>

              <q-input
                v-model="form.email"
                label="E-posta"
                type="email"
                :rules="[
                  val => !!val || 'E-posta alanı zorunludur',
                  val => isValidEmail(val) || 'Geçerli bir e-posta adresi giriniz'
                ]"
                outlined
                lazy-rules
                error-message="Geçerli bir e-posta adresi giriniz"
                hide-bottom-space
                class="form-input"
              />

              <q-input
                v-model="form.message"
                label="Mesajınız"
                type="textarea"
                :rules="[
                  val => !!val || 'Mesaj alanı zorunludur',
                  val => val.length >= 10 || 'Mesaj en az 10 karakter olmalıdır'
                ]"
                outlined
                autogrow
                lazy-rules
                error-message="Mesaj alanı zorunludur"
                hide-bottom-space
                class="form-input"
              />

              <div class="row justify-end">
                <q-btn
                  type="submit"
                  color="primary"
                  label="Gönder"
                  :loading="submitting"
                  class="q-mt-md submit-button"
                  unelevated
                  size="lg"
                  padding="sm xl"
                />
              </div>
            </q-form>
          </q-card>
        </div>
      </div>

      <!-- Map -->
      <div class="map-container q-mt-xl">
        <iframe
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d192698.6296691816!2d28.871754966796865!3d41.005495599999994!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14caa7040068086b%3A0xe1ccfe98bc01b0d0!2zxLBzdGFuYnVs!5e0!3m2!1str!2str!4v1648226528092!5m2!1str!2str"
          width="100%"
          height="450"
          style="border: 0"
          :allowfullscreen="true"
          loading="lazy"
          referrerpolicy="no-referrer-when-downgrade"
        ></iframe>
      </div>
    </div>
  </q-page>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useQuasar } from 'quasar'
import { api } from 'src/boot/axios'

const $q = useQuasar()

interface ContactForm {
  firstName: string
  lastName: string
  email: string
  message: string
}

const form = ref<ContactForm>({
  firstName: '',
  lastName: '',
  email: '',
  message: '',
})

const submitting = ref(false)

const isValidEmail = (email: string) => {
  const emailPattern =
    /^(?=[a-zA-Z0-9@._%+-]{6,254}$)[a-zA-Z0-9._%+-]{1,64}@(?:[a-zA-Z0-9-]{1,63}\.){1,8}[a-zA-Z]{2,63}$/
  return emailPattern.test(email)
}

const onSubmit = async () => {
  submitting.value = true
  try {
    const { data } = await api.post('/admin.php', {
      method: 'submit-contact',
      firstName: form.value.firstName,
      lastName: form.value.lastName,
      email: form.value.email,
      message: form.value.message
    })

    if (data.success) {
      $q.notify({
        type: 'positive',
        message: data.message,
        position: 'top',
      })
      // Reset form
      form.value = {
        firstName: '',
        lastName: '',
        email: '',
        message: '',
      }
    } else {
      throw new Error(data.message)
    }
  } catch (error: unknown) {
    $q.notify({
      type: 'negative',
      message: error instanceof Error ? error.message : 'Bir hata oluştu. Lütfen daha sonra tekrar deneyiniz.',
      position: 'top',
    })
  } finally {
    submitting.value = false
  }
}
</script>

<style lang="scss" scoped>
.contact-page {
  background: linear-gradient(135deg, $soft-bg, white);
  min-height: 100vh;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding-top: 4rem;
  padding-bottom: 4rem;
}

.contact-info {
  background: white;
  border-radius: $generic-border-radius;
  box-shadow: $card-shadow;
  height: 100%;

  .contact-item {
    padding: 1rem;
    border-radius: $generic-border-radius;
    transition: all 0.3s ease;

    &:hover {
      background: linear-gradient(135deg, rgba($primary, 0.05), rgba($secondary, 0.05));
      transform: translateX(4px);
    }

    .q-icon {
      font-size: 1.5rem;
    }
  }
}

.contact-form-card {
  background: white;
  border-radius: $generic-border-radius;
  box-shadow: $card-shadow;

  .q-input {
    .q-field__control {
      height: 56px;
    }

    &.q-field--textarea .q-field__control {
      height: auto;
      min-height: 56px;
    }
  }
}

.map-container {
  border-radius: $generic-border-radius;
  overflow: hidden;
  box-shadow: $card-shadow;
}

@media (max-width: 767px) {
  .container {
    padding-top: 2rem;
    padding-bottom: 2rem;
  }

  .contact-info {
    margin-bottom: 2rem;
  }
}

.form-input {
  .q-field__control {
    height: 56px;
    transition: all 0.3s ease;
  }

  &.q-field--textarea .q-field__control {
    height: auto;
    min-height: 120px;
  }

  &.q-field--error {
    .q-field__control {
      border-color: $negative;
    }
  }

  &.q-field--focused {
    .q-field__control {
      border-color: $primary;
      box-shadow: 0 0 0 1px $primary;
    }
  }
}

.submit-button {
  font-weight: 600;
  letter-spacing: 0.5px;
  transition: all 0.3s ease;

  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba($primary, 0.2);
  }
}
</style>
