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
  //token ve kullanıcı bilgilerini sessionStorage'dan al
  Router.beforeEach((to, from, next) => {
    const token = sessionStorage.getItem('token')
    const user = sessionStorage.getItem('user')
      ? JSON.parse(sessionStorage.getItem('user') as string)
      : null

    // token kontrolü
    if (to.matched.some((record) => record.meta.requiresAuth)) {
      if (!token || !user) {
        next({
          path: '/login',
          query: { redirect: to.fullPath },
        })
        return
      }

      // admin route
      if (to.matched.some((record) => record.meta.requiresAdmin)) {
        if (user.user_role !== 'admin') {
          console.log('Access denied: Admin role required')
          next({ path: '/' })
          return
        }
        next() // Allow access if user is admin
        return
      }

      // terapist route
      if (to.matched.some((record) => record.meta.requiresTherapist)) {
        if (user.user_role !== 'therapist') {
          next({ path: '/' })
          return
        }
      }
    }

    // login olduktan sonra login sayfasına yonlendir
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
