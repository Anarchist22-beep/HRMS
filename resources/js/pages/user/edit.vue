<template>
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Edit User</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit User</li>
                    </ol>
                </div>
            </div>
            <!--end::Row-->
        </div>
        <!--end::Container-->
    </div>
    <div class="app-content">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row g-4">

                <!--begin::Col-->
                <div class="col-md-12">
                    <!--begin::Quick Example-->
                    <div class="card card-primary card-outline mb-4">
                        <!--begin::Header-->
                        <div class="card-header">
                            <div class="card-title">Edit User</div>
                        </div>
                        <!--end::Header-->
                        <!--begin::Form-->
                        <form @submit.prevent="updateUser">
                            <!--begin::Body-->
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" v-model.trim="name" name="name" />

                                </div>

                                <div class="mb-3">
                                    <label for="name" class="form-label">Email</label>
                                    <input type="email" class="form-control" v-model.trim="email" name="email"
                                        autocomplete="off" />

                                </div>


                                <div class="mb-3">
                                    <label for="name" class="form-label">Password</label>
                                    <input type="password" class="form-control" v-model.trim="password" name="password"
                                        autocomplete="new-password" />

                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Designation</label>
                                    <input type="text" class="form-control" v-model="designation" />
                                </div>

                                <!-- Salary -->
                                <div class="mb-3">
                                    <label class="form-label">Salary</label>
                                    <input type="number" class="form-control" v-model="salary" />
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Role</label>
                                    <select class="form-control" v-model="role">
                                        <option value="">Select Role</option>
                                        <option v-for="r in roles" :key="r.id" :value="r.name">
                                            {{ r.name }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Department Dropdown -->
                                <div class="mb-3">
                                    <label class="form-label">Department</label>
                                    <select class="form-control" v-model="depart_id">
                                        <option value="">Select Department</option>
                                        <option v-for="d in departments" :key="d.id" :value="d.id">
                                            {{ d.name }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Project Dropdown -->
                                <div class="mb-3">
                                    <label class="form-label">Project</label>
                                    <select class="form-control" v-model="project_id">
                                        <option value="">Select Project</option>
                                        <option v-for="p in projects" :key="p.id" :value="p.id">
                                            {{ p.name }}
                                        </option>
                                    </select>
                                </div>






                            </div>
                            <!--end::Body-->
                            <!--begin::Footer-->
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                            <!--end::Footer-->
                        </form>
                        <!--end::Form-->
                    </div>
                    <!--end::Quick Example-->


                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->
        </div>
        <!--end::Container-->
    </div>
</template>


<script>
import { useToast } from 'vue-toastification'

export default {
    data() {
        return {
            editId: this.$route.params.id,
            name: '',
            email: '',
            password: '',
            designation: '',
            salary: '',
            role: '',
            depart_id: '',
            project_id: '',

            roles: [],
            departments: [],
            projects: []
        }
    },

    mounted() {
        this.getEditData()
    },

    methods: {

        //  Fetch roles, departments, projects
        async getEditData() {
            const toast = useToast()

            try {
                const res = await this.$axios.get(`/user/${this.editId}/edit`)

                const data = res.data.data

                // dropdowns
                this.roles = data.roles
                this.departments = data.departments
                this.projects = data.projects

                // user data fill
                const user = data.user

                this.name = user.name
                this.email = user.email
                this.designation = user.designation
                this.salary = user.salary
                this.depart_id = user.depart_id
                this.project_id = user.project_id

                // IMPORTANT: role fix (Spatie)
                this.role = user.roles?.[0]?.name || ''

            } catch (err) {
                toast.error('Failed to load user data')
            }
        },

        //  Store user
        async updateUser() {
            const toast = useToast()

            try {
                const res = await this.$axios.put(`/user/${this.editId}/update`, {
                    name: this.name,
                    email: this.email,
                    password: this.password, // optional
                    designation: this.designation,
                    salary: this.salary,
                    role: this.role,
                    depart_id: this.depart_id,
                    project_id: this.project_id
                })

                toast.success(res.data.message)

                this.$router.push({ path: '/list-user' })

            } catch (err) {
                toast.error(err.response?.data?.message || 'Update failed')
            }
        }
    }
}
</script>
