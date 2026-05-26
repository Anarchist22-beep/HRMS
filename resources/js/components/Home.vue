<template>
  <div class="landing-wrap">

    <div class="landing-card">

      <div class="logo-area">
        <img src="./assets/img/softonicdigital.jfif" alt="Softonic Digital" class="logo-img" />
      </div>

      <div class="tagline">Where Ideas Meet Innovation</div>

      <div class="divider"></div>

      <p class="welcome-text">
        Welcome to your workspace. Please log in to continue.
      </p>

      <div class="btn-group">

        <router-link v-if="!isLoggedIn" to="/login" class="sd-btn sd-btn-primary">
          <i class="bi bi-box-arrow-in-right me-2"></i> Login
        </router-link>

        <router-link
          v-if="isLoggedIn && role !== 'Sales'"
          to="/dashboard"
          class="sd-btn sd-btn-primary"
        >
          <i class="bi bi-speedometer2 me-2"></i> Go to Dashboard
        </router-link>

        <router-link
          v-if="isLoggedIn && role === 'Sales'"
          to="/sales/dashboard"
          class="sd-btn sd-btn-primary"
        >
          <i class="bi bi-speedometer2 me-2"></i> Go to Sales Dashboard
        </router-link>

      </div>

    </div>

  </div>
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

<style scoped>
.landing-wrap {
  min-height: 100vh;
  background-color: #0d1117;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem 1rem;
}

.landing-card {
  background-color: #161b22;
  border: 1px solid #1e3a2f;
  border-radius: 16px;
  padding: 3rem 2.5rem;
  width: 100%;
  max-width: 460px;
  text-align: center;
}

.logo-area {
  margin-bottom: 1.25rem;
}

.logo-img {
  max-width: 280px;
  width: 100%;
  height: auto;
  object-fit: contain;
}

.tagline {
  font-size: 13px;
  letter-spacing: 0.08em;
  color: #3fb882;
  text-transform: uppercase;
  font-weight: 500;
  margin-bottom: 1.5rem;
}

.divider {
  width: 48px;
  height: 2px;
  background-color: #1e6e47;
  margin: 0 auto 1.5rem;
  border-radius: 2px;
}

.welcome-text {
  color: #8b949e;
  font-size: 14px;
  line-height: 1.6;
  margin-bottom: 2rem;
}

.btn-group {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.sd-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  text-decoration: none;
  transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
  cursor: pointer;
  border: 1px solid transparent;
}

.sd-btn-primary {
  background-color: #1a7a4a;
  color: #e6f4ee;
  border-color: #1e6e47;
}

.sd-btn-primary:hover {
  background-color: #22a063;
  color: #ffffff;
  border-color: #22a063;
  text-decoration: none;
}

.sd-btn-primary:active {
  background-color: #155e38;
}

@media (max-width: 480px) {
  .landing-card {
    padding: 2rem 1.25rem;
  }

  .logo-img {
    max-width: 200px;
  }
}
</style>