<template>
  <div class="login-page bg-body-secondary">
    <div class="login-box">
      <div class="login-logo">
        <a href="#"><b>Softonic</b>Digital</a>
      </div>

      <p v-if="error" class="text-danger text-center">{{ error }}</p>

      <div class="card">
        <div class="card-body login-card-body">
          <p class="login-box-msg">Sign in to start your session</p>

          <form @submit.prevent="login">
            <div class="input-group mb-3">
              <input type="email" class="form-control" v-model="email" placeholder="Email" />
              <div class="input-group-text">
                <span class="bi bi-envelope"></span>
              </div>
            </div>

            <div class="input-group mb-3">
              <input type="password" class="form-control" v-model="password" placeholder="Password" />
              <div class="input-group-text">
                <span class="bi bi-lock-fill"></span>
              </div>
            </div>

            <div class="row">
              <div class="col-8">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" v-model="remember" />
                  <label class="form-check-label">Remember Me</label>
                </div>
              </div>

              <div class="col-4">
                <button type="submit" class="btn btn-primary w-100">
                  Sign In
                </button>
              </div>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { useToast } from 'vue-toastification'

export default {
  data() {
    return {
      email: '',
      password: '',
      remember: false,
      error: ''
    }
  },
  methods: {
    async login() {

      const toast = useToast()
      this.error = ''

      try {

        const response = await this.$axios.post('/login', {
          email: this.email,
          password: this.password
        })

        /*
        |--------------------------------------------------------------------------
        | Save Auth Data
        |--------------------------------------------------------------------------
        */
        localStorage.setItem('token', response.data.token)

        localStorage.setItem(
          'user',
          JSON.stringify(response.data.user)
        )

        localStorage.setItem(
          'permissions',
          JSON.stringify(response.data.permissions)
        )

        localStorage.setItem(
          'roles',
          JSON.stringify(response.data.roles)
        )

        // SAVE ROLE
        localStorage.setItem(
          'role',
          response.data.user.role
        )

        toast.success('Login successful')

        /*
        |--------------------------------------------------------------------------
        | Redirect Based On Role
        |--------------------------------------------------------------------------
        */
        const role = response.data.user.role

        if (role === 'Sales') {
          this.$router.push('/sales/dashboard')
        } else {
          this.$router.push('/dashboard')
        }

      } catch (err) {

        console.error(err)

        if (err.response && err.response.status === 401) {
          toast.error('Invalid email or password ❌')
        }
        else if (err.response && err.response.data.message) {
          toast.error(err.response.data.message)
        }
        else {
          toast.error('Something went wrong. Try again.')
        }
      }
    }
  }
}
</script>