<template>
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Add User</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Add User</li>
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
                            <div class="card-title">Add User</div>
                        </div>
                        <!--end::Header-->
                        <!--begin::Form-->
                        <form @submit.prevent="storeUser">
                            <!--begin::Body-->
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" v-model.trim="name" name="name" />

                                </div>

                                <div class="mb-3">
                                    <label for="name" class="form-label">Email</label>
                                    <input type="email" class="form-control" v-model.trim="email" name="email" autocomplete="off" />

                                </div>


                                <div class="mb-3">
                                    <label for="name" class="form-label">Password</label>
                                    <input type="password" class="form-control" v-model.trim="password" name="password" autocomplete="new-password" />

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
                                <button type="submit" class="btn btn-primary">Save</button>
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
        this.getCreateData()
    },

    methods: {

        //  Fetch roles, departments, projects
        async getCreateData() {
            const toast = useToast()

            try {
                const res = await this.$axios.get('/user-create')

                this.roles = res.data.data.roles
                this.departments = res.data.data.departments
                this.projects = res.data.data.projects

            } catch (err) {
                toast.error('Failed to load form data')
            }
        },

        // 🔹 Store user
        async storeUser() {
            const toast = useToast()

            if (!this.name || !this.email || !this.password) {
                toast.error('Name, Email and Password are required')
                return
            }

            try {
                const res = await this.$axios.post('/user-store', {
                    name: this.name,
                    email: this.email,
                    password: this.password,
                    designation: this.designation,
                    salary: this.salary,
                    role: this.role,
                    depart_id: this.depart_id,
                    project_id: this.project_id
                })

                toast.success(res.data.message)

                // reset form
                this.name = ''
                this.email = ''
                this.password = ''
                this.designation = ''
                this.salary = ''
                this.role = ''
                this.depart_id = ''
                this.project_id = ''

                this.$router.push({ path: '/list-user' })

            } catch (err) {
                toast.error(err.response?.data?.message || 'Failed to create user')
            }
        }
    }
}
</script>
