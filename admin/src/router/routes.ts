import { RouteConfig } from 'vue-router'

const routes: RouteConfig[] = [
  {
    path: '/',
    component: () => import('layouts/ExternalLayout.vue'),
    redirect: { name: 'login' },
    children: [
      {
        path: '/login',
        component: () => import('pages/Login'),
        name: 'login'
      }
    ]
  },
  {
    path: '/panel',
    component: () => import('layouts/InternalLayout.vue'),
    children: [
      {
        path: '/content',
        component: () => import('pages/ContentDisplay'),
        name: 'content'
      }
    ]
  }
]

// Always leave this as last one
if (process.env.MODE !== 'ssr') {
  routes.push({
    path: '*',
    component: () => import('pages/Error404.vue')
  })
}

export default routes
