import { createRouter, createWebHistory } from 'vue-router'
import Home from './components/Home.vue'
import Login from './pages/auth/login.vue'
import Dashboard from './pages/Dashboard.vue'
import Layout from './pages/layouts/layout.vue'
import AddUser from './pages/user/create.vue'
import EditUser from './pages/user/edit.vue'
import ListUser from './pages/user/index.vue'
import ProfileInfo from './pages/profile/edit.vue'
import ListPermission from './pages/permission/index.vue'
import createPermission from './pages/permission/create.vue'
import editPermission from './pages/permission/edit.vue'
import ListRole from './pages/role/index.vue'
import createRole from './pages/role/create.vue'
import editRole from './pages/role/edit.vue'
import ListDepartment from './pages/department/index.vue'
import createDepartment from './pages/department/create.vue'
import editDepartment from './pages/department/edit.vue'
import ListShift from './pages/shift/index.vue'
import createShift from './pages/shift/create.vue'
import editShift from './pages/shift/edit.vue'
import assignShift from './pages/shift/assign_shift.vue'
import listShift from './pages/shift/list_assign_shift.vue'
import editAssignShift from './pages/shift/edit_assign_shift.vue'

//sales
import SaleLayout from './pages/sales-layouts/layout.vue'
import SalesDashboard from './pages/sales/Dashboard.vue'
import ListLead from './pages/sales/lead/index.vue'
import AddLead from './pages/sales/lead/create.vue'
import editLead from './pages/sales/lead/edit.vue'

const routes = [
  { path: '/', component: Home },
  {
    path: '/login',
    component: Login,
    meta: { guest: true } //  only for non-logged users
  },

  {
    path: '/',
    component: Layout,
    meta: { requiresAuth: true }, //  protected
    children: [
      { path: 'dashboard', component: Dashboard },
      { path: 'add-user', component: AddUser },
      { path: 'list-user', component: ListUser },
      { path: '/edit-user/:id', component: EditUser },
      { path: '/profile', component: ProfileInfo },
      { path: '/list-permission', component: ListPermission },
      { path: '/create-permission', component: createPermission },
      { path: '/edit-permission/:id', component: editPermission },
      //Roles
      { path: '/list-role', component: ListRole },
      { path: '/add-role', component: createRole },
      { path: '/edit-role/:id', component: editRole },
      //department
      { path: '/list-department', component: ListDepartment },
      { path: '/add-department', component: createDepartment },
      { path: '/edit-department/:id', component: editDepartment },
      //shift
      { path: '/list-shift', component: ListShift },
      { path: '/add-shift', component: createShift },
      { path: '/edit-shift/:id', component: editShift },
      { path: '/assign-shift', component: assignShift },
      { path: '/list-assign-shift', component: listShift },
      { path: '/edit-schedule/:id', component: editAssignShift },




    ]
  },

  /* Employee routes */
  {
    path: '/sales',
    component: SaleLayout,
    meta: {
      requiresAuth: true,
      role: 'Sales'
    },
    children: [
      { path: 'dashboard', component: SalesDashboard },
      { path: 'profile', component: ProfileInfo },
      { path: 'list-lead', component: ListLead },
      { path: 'add-lead', component: AddLead },
      { path: 'edit-lead/:id', component: editLead }



    ]
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})


router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')
  const role = localStorage.getItem('role')

  //  Not logged in → block protected routes
  if (to.meta.requiresAuth && !token) {
    return next('/login')
  }

  /*
  |--------------------------------------------------------------------------
  | Prevent logged users from visiting login page
  |--------------------------------------------------------------------------
  */
  if (to.meta.guest && token) {

    if (role === 'Sales') {
      return next('/sales/dashboard')
    }

    return next('/dashboard')
  }

  /*
  |--------------------------------------------------------------------------
  | Role Based Access
  |--------------------------------------------------------------------------
  */
  if (role === 'Admin') {
    return next()
  }

  // Restrict other roles
  if (to.meta.role && to.meta.role !== role) {

    if (role === 'Sales') {
      return next('/sales/dashboard')
    }

    return next('/dashboard')
  }

  next()
})

export default router