import type { RouteRecordRaw } from 'vue-router'

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: '', component: () => import('pages/IndexPage.vue') },
      { path: 'contact', component: () => import('pages/ContactPage.vue') },
      { path: 'therapists', component: () => import('pages/TherapistsPage.vue') },
      { path: 'login', component: () => import('pages/auth/LoginPage.vue') },
      { path: 'register', component: () => import('pages/auth/RegisterPage.vue') },
      {
        path: 'therapist-register',
        component: () => import('pages/auth/TherapistRegisterPage.vue'),
      },
    ],
  },
  {
    path: '/admin',
    component: () => import('layouts/AdminLayout.vue'),
    children: [
      /*   { path: '', component: () => import('pages/admin/DashboardPage.vue') },
      { path: 'users', component: () => import('pages/admin/UsersPage.vue') },
      { path: 'settings', component: () => import('pages/admin/SettingsPage.vue') }, */
      {
        path: 'therapist-applications',
        component: () => import('pages/admin/TherapistApplicationsPage.vue'),
      },
    ],
    meta: {
      requiresAuth: true,
      requiresAdmin: true,
    },
  },
  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue'),
  },
]

export default routes
