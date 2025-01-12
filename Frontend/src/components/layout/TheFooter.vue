<template>
  <q-footer class="bg-white text-dark absolute-bottom q-pa-md footer">
    <div class="container q-pa-md">
      <div class="row q-col-gutter-md">
        <!-- Company Info -->
        <div class="col-12 col-md-4 q-pb-sm">
          <div class="text-subtitle1 text-primary q-mb-sm">Therapify</div>
          <p class="text-grey-8 text-body2 q-mb-sm">
            Yapay zeka destekli eşleştirme ile ruh sağlığı uzmanlarıyla sizi buluşturuyoruz.
          </p>
          <div class="row q-gutter-xs">
            <q-btn
              type="a"
              href="https://github.com/kaankaltakkiran/Therapify"
              target="_blank"
              flat
              round
              dense
              class="social-btn"
            >
              <q-icon name="fab fa-github" />
            </q-btn>
            <q-btn
              type="a"
              href="https://x.com/Kaankiran18"
              target="_blank"
              flat
              round
              dense
              class="social-btn"
            >
              <q-icon name="fab fa-twitter" />
            </q-btn>
            <q-btn
              type="a"
              href="https://www.instagram.com/kaan_k.kiran/?hl=tr"
              target="_blank"
              flat
              round
              dense
              class="social-btn"
            >
              <q-icon name="fab fa-instagram" />
            </q-btn>
            <q-btn
              type="a"
              href="https://www.linkedin.com/in/durdu-kaan-kaltakk%C4%B1ran-aba776223/"
              target="_blank"
              flat
              round
              dense
              class="social-btn"
            >
              <q-icon name="fab fa-linkedin-in" />
            </q-btn>
          </div>
        </div>

        <!-- Quick Links -->
        <div class="col-12 col-md-2">
          <div class="text-subtitle2 q-mb-sm">Hızlı Bağlantılar</div>
          <q-list dense padding class="q-pa-none">
         <!--    <q-item clickable v-ripple to="/about" class="q-pa-xs">
              <q-item-section class="text-body2">Hakkımızda</q-item-section>
            </q-item> -->
            <q-item clickable v-ripple to="/contact" class="q-pa-xs">
              <q-item-section class="text-body2">İletişim</q-item-section>
            </q-item>
            <q-item clickable v-ripple to="/register" class="q-pa-xs">
              <q-item-section class="text-body2">Kayıt Ol</q-item-section>
            </q-item>
            <q-item clickable v-ripple to="/login" class="q-pa-xs">
              <q-item-section class="text-body2">Giriş Yap</q-item-section>
            </q-item>
          </q-list>
        </div>

        <!-- Support -->
        <div class="col-12 col-md-2">
          <div class="text-subtitle2 q-mb-sm">Destek</div>
          <q-list dense padding class="q-pa-none">
            <q-item clickable v-ripple to="/contact" class="q-pa-xs">
              <q-item-section class="text-body2">Yardım Merkezi</q-item-section>
            </q-item>
            <q-item clickable v-ripple to="/privacy" class="q-pa-xs">
              <q-item-section class="text-body2">Gizlilik Politikası</q-item-section>
            </q-item>
            <q-item clickable v-ripple to="/faq" class="q-pa-xs">
              <q-item-section class="text-body2">SSS</q-item-section>
            </q-item>
          </q-list>
        </div>

        <!-- Contact Info -->
        <div class="col-12 col-md-4">
          <div class="text-subtitle2 q-mb-sm">İletişim</div>
          <q-list dense padding class="q-pa-none">
            <q-item class="q-pa-xs">
              <q-item-section avatar class="q-pr-xs">
                <q-icon name="email" size="xs" />
              </q-item-section>
              <q-item-section class="text-body2">destek@therapify.com</q-item-section>
            </q-item>
            <q-item class="q-pa-xs">
              <q-item-section avatar class="q-pr-xs">
                <q-icon name="phone" size="xs" />
              </q-item-section>
              <q-item-section class="text-body2">+90 (555) 123-4567</q-item-section>
            </q-item>
            <q-item class="q-pa-xs">
              <q-item-section avatar class="q-pr-xs">
                <q-icon name="location_on" size="xs" />
              </q-item-section>
              <q-item-section class="text-body2">
                Terapi Sokak No: 123, Ruh Sağlığı Mahallesi, İstanbul
              </q-item-section>
            </q-item>
          </q-list>
        </div>
      </div>

      <!-- Copyright -->
      <q-separator class="q-my-sm" />

      <div class="row justify-between items-center copyright-section q-py-sm">
        <div class="col-12 col-md-auto text-center text-md-left text-caption">
          © {{ new Date().getFullYear() }} Therapify. Tüm hakları saklıdır.
        </div>
        <div class="col-12 col-md-auto text-center text-md-right">
          <q-btn
            flat
            dense
            no-caps
            label="Gizlilik Politikası"
            to="/privacy"
            class="footer-link text-caption"
          />
          <q-btn
            flat
            dense
            no-caps
            label="Kullanım Koşulları"
            to="/terms"
            class="footer-link text-caption"
          />
        </div>
      </div>
    </div>
  </q-footer>
</template>

<script setup lang="ts">
// No script needed for now
</script>

<style lang="scss" scoped>
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

.footer {
  background: $soft-bg;
  border-top: 1px solid rgba($primary, 0.1);

  .social-btn {
    width: 36px;
    height: 36px;
    color: $grey-8;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background: $card-bg;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);

    :deep(.q-icon) {
      font-size: 1.2rem;
    }

    &:hover {
      background: linear-gradient(135deg, $primary, $secondary);
      color: white;
      transform: translateY(-2px) rotate(8deg);
      box-shadow: 0 4px 12px rgba($primary, 0.2);
    }
  }

  .footer-link {
    color: $grey-7;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    padding: 0.5rem;
    border-radius: 8px;
    font-weight: 500;

    &:hover {
      color: $primary;
      background: linear-gradient(135deg, rgba($primary, 0.1), rgba($secondary, 0.1));
      transform: translateY(-1px);
    }
  }

  .text-primary {
    background: linear-gradient(135deg, $primary, $secondary);
    background-clip: text;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-weight: 700;
  }
}

.copyright-section {
  @media (max-width: 767px) {
    flex-direction: column;
    text-align: center;

    .col-12 {
      margin-bottom: 1rem;
    }
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

  .q-footer {
    .q-pa-lg {
      padding: 1rem;
    }
  }
}
</style>
