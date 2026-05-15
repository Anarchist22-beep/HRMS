<script setup>
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const openMenu = ref(null)

const toggleMenu = (menu) => {
  openMenu.value = openMenu.value === menu ? null : menu
}

//  Load from localStorage
const permissions = JSON.parse(localStorage.getItem('permissions')) || []
//  helper function
const hasPermission = (perm) => {
  return permissions.includes(perm)
}

const isUsersRoute = computed(() => {
  return route.path.startsWith('/add-user')
})

const isSettingsRoute = computed(() => {
  return route.path.startsWith('/list-permission')
  return route.path.startsWith('/create-permission')
  return route.path.startsWith('/list-role')
  return route.path.startsWith('/add-role')


})

const isDepartmentsRoute = computed(() => {
  return route.path.startsWith('/list-department')
  return route.path.startsWith('/add-department')

})

const isShiftRoute = computed(() => {
  return route.path.startsWith('/list-shift')
  return route.path.startsWith('/add-shift')
  return route.path.startswith('/assign-shift')

})

//permissions
const canViewUserMenu = computed(() => {
  return hasPermission('create_user') || hasPermission('list_user')
})

const canViewSettingsMenu = computed(() => {
  return hasPermission('create_permission') ||
    hasPermission('list_permission') ||
    hasPermission('create_role') ||
    hasPermission('list_role')
})

const canViewDepartmentMenu = computed(() => {
  return hasPermission('create_department') || hasPermission('list_department')
})

const canViewShiftMenu = computed(() => {
  return hasPermission('create_shift') || hasPermission('list_shift')

})


</script>

<template>
  <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">

    <div class="sidebar-wrapper">
      <nav class="mt-2">

        <ul class="nav sidebar-menu flex-column">

          <!-- Dashboard -->
          <li class="nav-item">
            <router-link to="/dashboard" class="nav-link" active-class="active">
              <i class="nav-icon bi bi-speedometer"></i>
              <p>Dashboard</p>
            </router-link>
          </li>

          <!-- Users (Accordion) -->
          <li :class="['nav-item', { 'menu-open': openMenu === 'users' || isUsersRoute }]" v-if="canViewUserMenu">

            <a href="#" class="nav-link" @click.prevent="toggleMenu('users')">
              <i class="nav-icon bi bi-person-circle"></i>
              <p>
                User
                <i class="nav-arrow bi bi-chevron-right"></i>
              </p>
            </a>

            <ul class="nav nav-treeview" v-show="openMenu === 'users' || isUsersRoute">
              <li class="nav-item" v-if="hasPermission('create_user')">
                <router-link to="/add-user" class="nav-link" active-class="active">
                  <i class="nav-icon bi bi-circle"></i>
                  <p>Add User</p>
                </router-link>
              </li>

              <li class="nav-item" v-if="hasPermission('list_user')">
                <router-link to="/list-user" class="nav-link" active-class="active">
                  <i class="nav-icon bi bi-circle"></i>
                  <p>List User</p>
                </router-link>
              </li>
            </ul>

          </li>

          <!-- settings -->
          <li :class="['nav-item', { 'menu-open': openMenu === 'settings' || isSettingsRoute }]"
            v-if="canViewSettingsMenu">

            <a href="#" class="nav-link" @click.prevent="toggleMenu('settings')">
              <i class="nav-icon bi bi-gear-fill"></i>
              <p>
                Settings
                <i class="nav-arrow bi bi-chevron-right"></i>
              </p>
            </a>

            <ul class="nav nav-treeview" v-show="openMenu === 'settings' || isSettingsRoute">
              <li class="nav-item" v-if="hasPermission('create_permission')">
                <router-link to="/create-permission" class="nav-link" active-class="active">
                  <i class="nav-icon bi bi-circle"></i>
                  <p>Add Permission</p>
                </router-link>
              </li>

              <li class="nav-item" v-if="hasPermission('list_permission')">
                <router-link to="/list-permission" class="nav-link" active-class="active">
                  <i class="nav-icon bi bi-circle"></i>
                  <p>List Permission</p>
                </router-link>
              </li>

              <li class="nav-item" v-if="hasPermission('list_role')">
                <router-link to="/list-role" class="nav-link" active-class="active">
                  <i class="nav-icon bi bi-circle"></i>
                  <p>List Role</p>
                </router-link>
              </li>

              <li class="nav-item" v-if="hasPermission('create_role')">
                <router-link to="/add-role" class="nav-link" active-class="active">
                  <i class="nav-icon bi bi-circle"></i>
                  <p>Add Role</p>
                </router-link>
              </li>
            </ul>

          </li>
          <!-- Department -->

          <li :class="['nav-item', { 'menu-open': openMenu === 'departments' || isDepartmentsRoute }]"
            v-if="canViewDepartmentMenu">

            <a href="#" class="nav-link" @click.prevent="toggleMenu('departments')">
              <i class="nav-icon bi bi-building"></i>
              <p>
                Department
                <i class="nav-arrow bi bi-chevron-right"></i>
              </p>
            </a>

            <ul class="nav nav-treeview" v-show="openMenu === 'departments' || isDepartmentsRoute">
              <li class="nav-item" v-if="hasPermission('create_department')">
                <router-link to="/add-department" class="nav-link" active-class="active">
                  <i class="nav-icon bi bi-circle"></i>
                  <p>Add Department</p>
                </router-link>
              </li>

              <li class="nav-item" v-if="hasPermission('list_department')">
                <router-link to="/list-department" class="nav-link" active-class="active">
                  <i class="nav-icon bi bi-circle"></i>
                  <p>List Department</p>
                </router-link>
              </li>


            </ul>

          </li>

          <!-- shift route -->
          <li :class="['nav-item', { 'menu-open': openMenu === 'shifts' || isShiftRoute }]" v-if="canViewShiftMenu">

            <a href="#" class="nav-link" @click.prevent="toggleMenu('shifts')">
              <i class="nav-icon bi bi-calendar"></i>
              <p>
                Shift
                <i class="nav-arrow bi bi-chevron-right"></i>
              </p>
            </a>

            <ul class="nav nav-treeview" v-show="openMenu === 'shifts' || isShiftRoute">
              <li class="nav-item" v-if="hasPermission('create_shift')">
                <router-link to="/add-shift" class="nav-link" active-class="active">
                  <i class="nav-icon bi bi-circle"></i>
                  <p>Add Shift</p>
                </router-link>
              </li>

              <li class="nav-item" v-if="hasPermission('list_shift')">
                <router-link to="/list-shift" class="nav-link" active-class="active">
                  <i class="nav-icon bi bi-circle"></i>
                  <p>List Shift</p>
                </router-link>
              </li>


              <li class="nav-item" v-if="hasPermission('assign_shift')">
                <router-link to="/assign-shift" class="nav-link" active-class="active">
                  <i class="nav-icon bi bi-circle"></i>
                  <p>Assign Shift</p>
                </router-link>
              </li>

              <li class="nav-item" v-if="hasPermission('assign_shift')">
                <router-link to="/list-assign-shift" class="nav-link" active-class="active">
                  <i class="nav-icon bi bi-circle"></i>
                  <p>List Assign Shift</p>
                </router-link>
              </li>


            </ul>

          </li>


        </ul>
      </nav>
    </div>
  </aside>
</template>
