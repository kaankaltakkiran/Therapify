import { route } from 'quasar/wrappers'
import {
  createMemoryHistory,
  createRouter,
  createWebHashHistory,
  createWebHistory,
} from 'vue-router'
import routes from './routes'

/*
 * If not building with SSR mode, you can
 * directly export the Router instantiation;
 *
 * The function below can be async too; either use
 * async/await or return a Promise which resolves
 * with the Router instance.
 */

export default route(function () {
  const createHistory = process.env.SERVER
    ? createMemoryHistory
    : process.env.VUE_ROUTER_MODE === 'history'
      ? createWebHistory
      : createWebHashHistory

  const Router = createRouter({
    scrollBehavior: () => ({ left: 0, top: 0 }),
    routes,
    history: createHistory(process.env.VUE_ROUTER_BASE),
  })

  Router.beforeEach((to, from, next) => {
    const token = sessionStorage.getItem('token')
    const user = sessionStorage.getItem('user')
      ? JSON.parse(sessionStorage.getItem('user') as string)
      : null

    // Debug logs
    console.log('Route navigation:', {
      to: to.fullPath,
      requiresAuth: to.matched.some((record) => record.meta.requiresAuth),
      requiresAdmin: to.matched.some((record) => record.meta.requiresAdmin),
      token: !!token,
      userRole: user?.user_role,
    })

    // Check if route requires authentication
    if (to.matched.some((record) => record.meta.requiresAuth)) {
      if (!token || !user) {
        console.log('No token or user, redirecting to login')
        next({
          path: '/login',
          query: { redirect: to.fullPath },
        })
        return
      }

      // Check admin routes
      if (to.matched.some((record) => record.meta.requiresAdmin)) {
        if (user.user_role !== 'admin') {
          console.log('Access denied: Admin role required')
          next({ path: '/' })
          return
        }
      }

      // Check therapist routes
      if (to.matched.some((record) => record.meta.requiresTherapist)) {
        if (user.user_role !== 'therapist') {
          console.log('Access denied: Therapist role required')
          next({ path: '/' })
          return
        }
      }
    }

    // Handle guest-only routes (login, register, etc.)
    if (to.matched.some((record) => record.meta.requiresGuest)) {
      if (token && user) {
        console.log('Authenticated user accessing guest route, redirecting to home')
        next({ path: '/' })
        return
      }
    }

    // Allow navigation
    next()
  })

  return Router
})
