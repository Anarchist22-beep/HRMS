<template>
  <!--begin::App Wrapper-->

  <!--begin::Header-->
  <nav class="app-header navbar navbar-expand bg-body">
    <!--begin::Container-->
    <div class="container-fluid">
      <!--begin::Start Navbar Links-->
      <router-link to="/" class="navbar-brand ms-2 me-3">HRMS</router-link>
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
            <i class="bi bi-list"></i>
          </a>
        </li>
        <li class="nav-item d-none d-md-block"><a href="#" class="nav-link">Home</a></li>
        <li class="nav-item d-none d-md-block"><a href="#" class="nav-link">Contact</a></li>
      </ul>
      <!--end::Start Navbar Links-->
      <!--begin::End Navbar Links-->
      <ul class="navbar-nav ms-auto">
        <!--begin::Navbar Search-->
        <li class="nav-item">
          <a class="nav-link" data-widget="navbar-search" href="#" role="button">
            <i class="bi bi-search"></i>
          </a>
        </li>
        <!--end::Navbar Search-->
        <!--begin::Messages Dropdown Menu-->
        <!--end::Messages Dropdown Menu-->
        <!--begin::Notifications Dropdown Menu-->
        <!--end::Notifications Dropdown Menu-->
        <!--begin::Fullscreen Toggle-->

        <!--end::Fullscreen Toggle-->
        <!--begin::User Menu Dropdown-->
        <li class="nav-item dropdown user-menu">
          <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
            <img src="./assets/img/user2-160x160.jpg" class="user-image rounded-circle shadow" alt="User Image" />
            <span class="d-none d-md-inline">{{ user?.name ?? 'Guest' }}</span>
          </a>
          <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
            <!--begin::User Image-->
            <li class="user-header text-bg-primary">
              <img src="./assets/img/user2-160x160.jpg" class="rounded-circle shadow" alt="User Image" />
              <p>
                 {{ user?.name ?? 'Guest' }}
                <!-- <small>Member since Nov. 2023</small> -->
              </p>
            </li>
            <!--end::User Image-->
            <!--begin::Menu Body-->
            <li class="user-body">
              <!--begin::Row-->
              <!--end::Row-->
            </li>
            <!--end::Menu Body-->
            <!--begin::Menu Footer-->
            <li class="user-footer">
              <router-link to="/profile" class="btn btn-default btn-flat">Profile</router-link>
              <a href="#" @click.prevent="logout" class="btn btn-default btn-flat float-end">
                Sign out
              </a>
            </li>
            <!--end::Menu Footer-->
          </ul>
        </li>
        <!--end::User Menu Dropdown-->
      </ul>
      <!--end::End Navbar Links-->
    </div>
    <!--end::Container-->
  </nav>
  <!--end::Header-->

</template>
<script>
import { useToast } from 'vue-toastification'

export default {
  name: 'Header',

  data() {
    return {
      user: null
    }
  },

  mounted() {
    this.loadUser()
  },

  methods: {
    loadUser() {
      const user = localStorage.getItem('user')
      this.user = user ? JSON.parse(user) : null
    },

    async logout() {
      const toast = useToast()

      try {
        await this.$axios.post('/logout')
        toast.success('Logged out successfully')
      } catch (err) {
        console.error(err)
        toast.error('Logout failed, but session cleared')
      }

      localStorage.removeItem('token')
      localStorage.removeItem('user')

      this.$router.push('/login')
    }
  }
}
</script>