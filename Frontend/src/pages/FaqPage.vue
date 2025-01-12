<template>
    <q-page class="q-pa-md">
      <div class="container q-py-lg">
        <h1 class="text-h4 text-primary q-mb-lg text-center">Sıkça Sorulan Sorular</h1>
  
        <div class="faq-content q-gutter-y-md">
          <q-expansion-item
            v-for="(faq, index) in faqs"
            :key="index"
            :label="faq.question"
            header-class="text-primary"
            class="faq-item"
            icon="help_outline"
            expand-icon-class="text-primary"
          >
            <q-card>
              <q-card-section>
                <p class="text-body1">{{ faq.answer }}</p>
              </q-card-section>
            </q-card>
          </q-expansion-item>
        </div>
  
        <div class="contact-section q-mt-xl q-pa-md text-center">
          <h2 class="text-h5 q-mb-md">Sorunuzu bulamadınız mı?</h2>
          <p class="q-mb-md">Bizimle iletişime geçebilirsiniz.</p>
          <q-btn
            color="primary"
            label="İletişime Geç"
            to="/contact"
            unelevated
            class="contact-btn"
          />
        </div>
      </div>
    </q-page>
  </template>
  
  <script setup lang="ts">
  const faqs = [
    {
      question: 'Therapify nedir?',
      answer:
        'Therapify, yapay zeka destekli eşleştirme sistemi ile ruh sağlığı uzmanlarıyla danışanları buluşturan online bir terapi platformudur.',
    },
    {
      question: 'Nasıl terapist bulabilirim?',
      answer:
        'Platformumuza kayıt olduktan sonra, size en uygun terapisti bulmak için yapay zeka destekli eşleştirme sistemimizi kullanabilirsiniz.',
    },
    {
      question: 'Online terapi seansları nasıl yapılır?',
      answer:
        'Seanslar, güvenli video görüşme platformumuz üzerinden gerçekleştirilir. İnternet bağlantısı olan herhangi bir cihazdan katılabilirsiniz.',
    },
    {
      question: 'Ödeme seçenekleri nelerdir?',
      answer:
        'Kredi kartı, banka kartı ve havale/EFT ile ödeme yapabilirsiniz. Ödemeler seans bazlı olarak gerçekleştirilir.',
    },
    {
      question: 'Terapistler nasıl seçiliyor?',
      answer:
        'Tüm terapistlerimiz lisanslı ve deneyimli profesyonellerdir. Kapsamlı bir değerlendirme sürecinden geçerek platformumuza katılırlar.',
    },
    {
      question: 'Seans iptali nasıl yapılır?',
      answer:
        'Seansınızı başlangıç saatinden en az 24 saat önce iptal edebilirsiniz. İptal işlemi için hesabınızdan veya uygulamamızdan randevularım bölümünü kullanabilirsiniz.',
    },
    {
      question: 'Bilgilerimin gizliliği nasıl sağlanıyor?',
      answer:
        'Tüm verileriniz KVKK kapsamında korunmaktadır. SSL şifreleme ve güvenli veri depolama sistemleri kullanıyoruz.',
    },
    {
      question: 'Acil durumlarda ne yapmalıyım?',
      answer:
        'Acil durumlarda lütfen 112 Acil Servis veya size en yakın ruh sağlığı acil servisine başvurun. Platform 7/24 acil destek hattımız mevcuttur.',
    },
  ]
  </script>
  
  <style lang="scss" scoped>
  .container {
    max-width: 800px;
    margin: 0 auto;
  }
  
  .faq-content {
    .faq-item {
      border-radius: 12px;
      background: white;
      overflow: hidden;
      border: 1px solid rgba($primary, 0.1);
      transition: all 0.3s ease;
  
      &:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
      }
  
      :deep(.q-expansion-item__container) {
        .q-item {
          padding: 1rem;
        }
      }
  
      :deep(.q-card) {
        box-shadow: none;
        background: rgba($primary, 0.03);
      }
    }
  }
  
  .contact-section {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
    border: 1px solid rgba($primary, 0.1);
  
    .contact-btn {
      padding: 0.5rem 2rem;
      border-radius: 12px;
      font-weight: 500;
      background: linear-gradient(135deg, $primary, $secondary);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  
      &:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba($primary, 0.3);
      }
    }
  }
  </style>
  