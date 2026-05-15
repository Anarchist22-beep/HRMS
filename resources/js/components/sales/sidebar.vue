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



const isLeadRoute = computed(() => {
  return route.path.startsWith('/sales/list-lead')
  return route.path.startsWith('/sales/add-lead')
  

})

//permissions
const canViewLeadMenu = computed(() => {
  return hasPermission('create_lead') || hasPermission('list_lead')
})




</script>

<template>
  <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">

    <div class="sidebar-wrapper">
      <nav class="mt-2">

        <ul class="nav sidebar-menu flex-column">

          <!-- Dashboard -->
          <li class="nav-item">
            <router-link to="/sales/dashboard" class="nav-link" active-class="active">
              <i class="nav-icon bi bi-speedometer"></i>
              <p>Dashboard</p>
            </router-link>
          </li>

          <!-- Leads (Accordion) -->
          <li :class="['nav-item', { 'menu-open': openMenu === 'leads' || isLeadRoute }]" v-if="canViewLeadMenu">

            <a href="#" class="nav-link" @click.prevent="toggleMenu('leads')">
              <i class="nav-icon bi bi-receipt"></i>
              <p>
                Leads
                <i class="nav-arrow bi bi-chevron-right"></i>
              </p>
            </a>

            <ul class="nav nav-treeview" v-show="openMenu === 'leads' || isLeadRoute">
              <li class="nav-item" v-if="hasPermission('create_lead')">
                <router-link to="/sales/add-lead" class="nav-link" active-class="active">
                  <i class="nav-icon bi bi-circle"></i>
                  <p>Add Lead</p>
                </router-link>
              </li>

              <li class="nav-item" v-if="hasPermission('list_lead')">
                <router-link to="/sales/list-lead" class="nav-link" active-class="active">
                  <i class="nav-icon bi bi-circle"></i>
                  <p>List Lead</p>
                </router-link>
              </li>
            </ul>

          </li>


        </ul>
      </nav>
    </div>
  </aside>
</template>
