import type { RouteRecordRaw } from 'vue-router'

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      {
        path: '',
        component: () => import('pages/IndexPage.vue'),
      },
      {
        /* requiresAuth: true: Kullanıcının giriş yapmış olması gerektiğini belirtir.
         requiresGuest: true: Giriş yapmamış kullanıcılar için uygun.
        requiresAdmin: true: Admin yetkisi gerektirir.
         requiresTherapist: true: Terapist rolüne sahip kullanıcılar için uygundur. */
        path: 'login',
        component: () => import('pages/auth/LoginPage.vue'),
        meta: { requiresGuest: true },
      },
      {
        path: 'contact',
        component: () => import('pages/ContactPage.vue'),
        meta: { requiresGuest: true },
      },
      {
        path: 'register',
        component: () => import('pages/auth/RegisterPage.vue'),
        meta: { requiresGuest: true },
      },
      {
        path: 'therapist-register',
        component: () => import('pages/auth/TherapistRegisterPage.vue'),
        meta: { requiresGuest: true },
      },
      {
        path: 'forgot-password',
        component: () => import('pages/auth/ForgotPasswordPage.vue'),
        meta: { requiresGuest: true },
      },
      {
        path: 'faq',
        component: () => import('pages/FaqPage.vue'),
        meta: { requiresGuest: true },
      },
      {
        path: 'privacy',
        component: () => import('pages/PrivacyPage.vue'),
      },
    ],
  },
  {
    // admin paneli için yönlendirme
    path: '/admin',
    component: () => import('layouts/AdminLayout.vue'),
    /* meta: { requiresAuth: true, requiresAdmin: true }, */
    children: [
      {
        path: '',
        redirect: { name: 'therapist-applications' },
      },
      {
        path: 'therapist-applications',
        name: 'therapist-applications',
        component: () => import('pages/admin/TherapistApplicationsPage.vue'),
      },
      {
        path: 'support-messages',
        name: 'support-messages',
        component: () => import('pages/admin/SupportMessagesPage.vue'),
      },
      {
        path: 'therapists',
        name: 'therapists',
        component: () => import('pages/admin/TherapistListPage.vue'),
      },
    ],
  },
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue'),
  },
]

export default routes
