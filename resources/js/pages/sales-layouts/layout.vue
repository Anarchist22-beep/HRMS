<template>
  <div class="app-wrapper">
    <Header />
    <Sidebar />

    <main class="app-main">
      <router-view />  
    </main>

    <Footer />
  </div>
</template>

<script setup>
import Header from '../../components/sales/header.vue'
import Sidebar from '../../components/sales/sidebar.vue'
import Footer from '../../components/footer.vue'
import { onMounted, nextTick } from 'vue'  //  ADD THIS

onMounted(async () => {
  await nextTick()

  // Sidebar toggle fallback
  const btn = document.querySelector('[data-lte-toggle="sidebar"]')

  btn?.addEventListener('click', (e) => {
    e.preventDefault()
    document.body.classList.toggle('sidebar-collapse')
  })

  // Accordion fix
  document.querySelectorAll('.nav-item.has-treeview > a').forEach(el => {
    el.addEventListener('click', (e) => {
      e.preventDefault()
      const parent = el.parentElement
      parent.classList.toggle('menu-open')
    })
  })
})
</script>