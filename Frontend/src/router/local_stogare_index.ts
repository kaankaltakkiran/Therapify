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
    const token = localStorage.getItem('token')
    const user = localStorage.getItem('user')
      ? JSON.parse(localStorage.getItem('user') as string)
      : null

    // Check for protected route
    if (to.matched.some((record) => record.meta.requiresAuth)) {
      if (!token || !user) {
        next({
          path: '/login',
          query: { redirect: to.fullPath },
        })
        return
      }

      // Check for admin route
      if (to.matched.some((record) => record.meta.requiresAdmin)) {
        if (user.user_role !== 'admin') {
          next({ path: '/' })
          return
        }
      }

      // Check for therapist route
      if (to.matched.some((record) => record.meta.requiresTherapist)) {
        if (user.user_role !== 'therapist') {
          next({ path: '/' })
          return
        }
      }
    }

    // Check for guest route (login, register)
    if (to.matched.some((record) => record.meta.requiresGuest)) {
      if (token && user) {
        next({ path: '/' })
        return
      }
    }

    next()
  })

  return Router
})
