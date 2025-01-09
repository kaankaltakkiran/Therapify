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
        path: 'register',
        component: () => import('pages/auth/RegisterPage.vue'),
        meta: { requiresGuest: true },
      },
      {
        path: 'therapist-register',
        component: () => import('pages/auth/TherapistRegisterPage.vue'),
        meta: { requiresGuest: true },
      },
      /*    {
        path: 'profile',
        component: () => import('pages/ProfilePage.vue'),
        meta: { requiresAuth: true },
      }, */
      /*    {
        path: 'therapists',
        component: () => import('pages/TherapistsPage.vue'),
      }, */
      /*  {
        path: 'appointments',
        component: () => import('pages/AppointmentsPage.vue'),
        meta: { requiresAuth: true },
      }, */
      /*  {
        path: 'messages',
        component: () => import('pages/MessagesPage.vue'),
        meta: { requiresAuth: true },
      }, */
    ],
  },
  /*  {
    path: '/therapist',
    component: () => import('layouts/MainLayout.vue'),
    meta: { requiresAuth: true, requiresTherapist: true },
    children: [
      {
        path: 'dashboard',
        component: () => import('pages/therapist/DashboardPage.vue'),
      },
      {
        path: 'schedule',
        component: () => import('pages/therapist/SchedulePage.vue'),
      },
    ],
  }, */
  {
    path: '/admin',
    component: () => import('layouts/AdminLayout.vue'),
    meta: { requiresAuth: true, requiresAdmin: true },
    children: [
      {
        path: '',
        redirect: '/admin/therapist-applications',
      },
      {
        path: 'therapist-applications',
        component: () => import('pages/admin/TherapistApplicationsPage.vue'),
      },
      /*    {
        path: 'overview',
        component: () => import('pages/admin/OverviewPage.vue'),
      },
      {
        path: 'users',
        component: () => import('pages/admin/UsersPage.vue'),
      },
      {
        path: 'settings',
        component: () => import('pages/admin/SettingsPage.vue'),
      }, */
    ],
  },
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue'),
  },
]

export default routes
