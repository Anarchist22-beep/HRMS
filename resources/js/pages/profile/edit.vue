<template>
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h3 class="mb-0">Update Profile</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end mb-0">
                        <li class="breadcrumb-item">
                            <a href="#">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Update Profile</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= CONTENT ================= -->
    <div class="app-content">
        <div class="container-fluid">

            <div class="row g-4">

                <!-- ================= PROFILE CARD ================= -->
                <div class="col-lg-12">
                    <div class="card card-primary card-outline h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Profile Information</h5>
                        </div>

                        <form id="change-name" @submit.prevent="updateName">


                            <div class="card-body">

                                <!-- Name -->
                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" v-model="name" required>
                                </div>

                            </div>

                            <div class="card-footer d-flex justify-content-between align-items-center">
                                <button class="btn btn-primary btn-sm">
                                    Save Changes
                                </button>


                            </div>
                        </form>
                    </div>
                </div>

                <!-- ================= Password Change ================= -->
                <div class="col-lg-12">
                    <div class="card card-info card-outline h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Change Password</h5>
                        </div>

                        <form @submit.prevent="updatePassword" id="change-password">


                            <div class="card-body">

                                <!-- Current Password -->
                                <div class="mb-3">
                                    <label for="old_password" class="form-label">
                                        Current Password
                                    </label>
                                    <input type="password" v-model="old_password" class="form-control">

                                </div>

                                <!-- New Password -->
                                <div class="mb-3">
                                    <label for="password" class="form-label">
                                        New Password
                                    </label>
                                    <input type="password" v-model="new_password" class="form-control">


                                </div>

                                <!-- Confirm Password -->
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">
                                        Confirm New Password
                                    </label>
                                    <input type="password" v-model="new_password_confirmation" class="form-control">

                                </div>

                            </div>

                            <div class="card-footer d-flex justify-content-between align-items-center">
                                <button type="submit" class="btn btn-warning btn-sm">
                                    Update Password
                                </button>


                            </div>
                        </form>
                    </div>
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
            name: '',
            old_password: '',
            new_password: '',
            new_password_confirmation: ''
        }
    },

    mounted() {
        this.loadUser()
    },

    methods: {
        loadUser() {
            const user = JSON.parse(localStorage.getItem('user'))

            if (user) {
                this.name = user.name //  fill input field
            }
        },

        async updateName() {
            const toast = useToast()

            try {
                const res = await this.$axios.post('/change-name', {
                    name: this.name
                })

                toast.success(res.data.message)

                //  update localStorage so header also updates
                const user = JSON.parse(localStorage.getItem('user'))
                user.name = this.name
                localStorage.setItem('user', JSON.stringify(user))

            } catch (err) {
                toast.error(err.response?.data?.message || 'Failed to update name')
            }
        },

        async updatePassword() {
            const toast = useToast()

            try {
                const res = await this.$axios.post('/change-password', {
                    old_password: this.old_password,
                    new_password: this.new_password,
                    new_password_confirmation: this.new_password_confirmation
                })

                toast.success(res.data.message)

                this.old_password = ''
                this.new_password = ''
                this.new_password_confirmation = ''

            } catch (err) {
                toast.error(err.response?.data?.message || 'Failed to update password')
            }
        }
    }
}
</script>