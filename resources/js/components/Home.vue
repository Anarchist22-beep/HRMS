<template>
  <div class="message-model">
    <h1>Hi VUE</h1>
  </div>

  <!-- Show only if NOT logged in -->
  <router-link v-if="!isLoggedIn" to="/login">Login</router-link>

  <!-- Show only if logged in -->
 <router-link
    v-if="isLoggedIn && role !== 'Sales'"
    to="/dashboard"
  >
    Dashboard
  </router-link>

  <!-- sales -->
  <router-link
    v-if="isLoggedIn && role === 'Sales'"
    to="/sales/dashboard"
  >
    Dashboard
  </router-link>
</template>


<script>
export default {
  computed: {
    isLoggedIn() {
      return !!localStorage.getItem('token')
    },
     role() {
       return JSON.parse(localStorage.getItem('user'))?.role
     }
  }
}
</script>