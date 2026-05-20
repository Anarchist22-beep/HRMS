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
import ListProject from './pages/project/index.vue'
import createProject from './pages/project/create.vue'
import EditProject from './pages/project/edit.vue'

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
      { path: 'dashboard', component: Dashboard, meta:{title: 'Dashboard - HRMS'} },
      { path: 'add-user', component: AddUser, meta: {permission: 'list_user',title: 'Dashboard - User'} },
      { path: 'list-user', component: ListUser, meta: {permission: 'create_user',title: 'Dashboard - User'} },
      { path: '/edit-user/:id', component: EditUser, meta: {permission: 'edit_user',title: 'Dashboard - User'} },
      { path: '/profile', component: ProfileInfo, meta:{title: 'Dashboard - Profile'} },
      { path: '/list-permission', component: ListPermission, meta: {permission: 'list_permission',title: 'Dashboard - Roles & Permission'}  },
      { path: '/create-permission', component: createPermission, meta: {permission: 'create_permission',title: 'Dashboard - Roles & Permission'} },
      { path: '/edit-permission/:id', component: editPermission, meta: {permission: 'edit_permission',title: 'Dashboard - Roles & Permission'} },
      //Roles
      { path: '/list-role', component: ListRole, meta: {permission: 'list_role',title: 'Dashboard - Roles & Permission'}},
      { path: '/add-role', component: createRole, meta: {permission: 'create_role',title: 'Dashboard - Roles & Permission'} },
      { path: '/edit-role/:id', component: editRole, meta: {permission: 'edit_role',title: 'Dashboard - Roles & Permission'} },
      //department
      { path: '/list-department', component: ListDepartment, meta: {permission: 'list_department',title: 'Dashboard - Departments'} },
      { path: '/add-department', component: createDepartment, meta: {permission: 'create_department',title: 'Dashboard - Departments'} },
      { path: '/edit-department/:id', component: editDepartment, meta: {permission: 'edit_department',title: 'Dashboard - Departments'} },
      //shift
      { path: '/list-shift', component: ListShift, meta: {permission: 'list_shift',title: 'Dashboard - Shifts'} },
      { path: '/add-shift', component: createShift, meta: {permission: 'create_shift',title: 'Dashboard - Shifts'} },
      { path: '/edit-shift/:id', component: editShift, meta: {permission: 'edit_shift',title: 'Dashboard - Shifts'} },
      { path: '/assign-shift', component: assignShift, meta: {permission: 'create_assign_shift',title: 'Dashboard - Shifts'} },
      { path: '/list-assign-shift', component: listShift, meta: {permission: 'list_assign_shift',title: 'Dashboard - Shifts'} },
      { path: '/edit-schedule/:id', component: editAssignShift, meta: {permission: 'edit_assign_shift',title: 'Dashboard - Shifts'} },
      //project
      { path: '/list-project', component: ListProject, meta: {permission: 'list_project',title: 'Dashboard - Project'} },
      { path: '/add-project', component: createProject, meta: {permission: 'create_project',title: 'Dashboard - Project'} },
      { path: '/edit-project/:id', component: EditProject, meta: {permission: 'edit_project',title: 'Dashboard - Project'} },





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
      { path: 'dashboard', component: SalesDashboard, meta:{title: 'Dashboard - Sales'} },
      { path: 'profile', component: ProfileInfo, meta:{title: 'Dashboard - Sales'} },
      { path: 'list-lead', component: ListLead, meta:{title: 'Dashboard - Sales'} },
      { path: 'add-lead', component: AddLead, meta:{title: 'Dashboard - Sales'} },
      { path: 'edit-lead/:id', component: editLead, meta:{title: 'Dashboard - Sales'} }



    ]
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to, from, next) => {

  document.title = to.meta.title || 'HRMS'

  next()
})


router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')
  const role = localStorage.getItem('role')
  const permissions = JSON.parse(
    localStorage.getItem('permissions') || '[]'
  )

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

  /*
  |--------------------------------------------------------------------------
  | Permission Check
  |--------------------------------------------------------------------------
  */
  if (to.meta.permission) {

    const hasPermission = permissions.includes(
      to.meta.permission
    )

    if (!hasPermission) {
      return next('/dashboard')
    }
  }

  next()
})

export default router
