<template>
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Edit Role</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit Role</li>
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
                            <div class="card-title">Edit Role</div>
                        </div>
                        <!--end::Header-->
                        <!--begin::Form-->
                        <form @submit.prevent="updateRole">

                            <div class="card-body">

                                <!-- Role Name -->
                                <div class="mb-3">
                                    <label class="form-label">Name</label>

                                    <input type="text" class="form-control" v-model="name"
                                        placeholder="Enter role name" />
                                </div>

                                <!-- Permissions Pills -->
                                <div class="mb-3">
                                    <label class="form-label">Permissions</label>

                                    <div class="d-flex flex-wrap gap-2 mt-2">

                                        <span v-for="perm in permissions" :key="perm.id"
                                            @click="togglePermission(perm.id)" class="badge permission-pill"
                                            :class="selectedPermissions.includes(perm.id) ? 'bg-success' : 'bg-light text-dark'">
                                            {{ perm.display_name }}
                                        </span>

                                    </div>
                                </div>

                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    Update
                                </button>
                            </div>

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
            permissions: [],
            selectedPermissions: [],
            roleId: null,
            isEdit: false
        }
    },

    async mounted() {
        this.roleId = this.$route.params.id

        //  Detect edit mode
        if (this.roleId) {
            this.isEdit = true
            await this.fetchRole()
        }

        await this.fetchPermissions()
    },

    methods: {

        //  Fetch all permissions (same as create)
        async fetchPermissions() {
            try {
                const res = await this.$axios.get('/permission-list', {
                    params: { all: true } //  IMPORTANT
                })

                const perms = res.data.data.permissions
                this.permissions = perms.data ? perms.data : perms

            } catch (err) {
                console.log(err)
            }
        },

        //  Fetch role (EDIT)
        async fetchRole() {
            const toast = useToast()

            try {
                const res = await this.$axios.get(`/role/${this.roleId}/edit`)

                const role = res.data.data.data

                //  Prefill name
                this.name = role.name

                //  Prefill selected permissions
                this.selectedPermissions = role.permissions.map(p => p.id)

            } catch (err) {
                toast.error('Failed to load role')
            }
        },

        //  Toggle pills
        togglePermission(id) {
            if (this.selectedPermissions.includes(id)) {
                this.selectedPermissions = this.selectedPermissions.filter(p => p !== id)
            } else {
                this.selectedPermissions.push(id)
            }
        },

        //  Submit (Create OR Update)
        async updateRole() {
            const toast = useToast()

            if (!this.name) {
                toast.error('Role name is required')
                return
            }

            if (this.selectedPermissions.length === 0) {
                toast.error('Select at least one permission')
                return
            }

            try {

                const res = await this.$axios.put(`/role/${this.roleId}/update`, {
                    name: this.name,
                    permissions: this.selectedPermissions
                })

                toast.success(res.data.message)

                this.$router.push('/list-role')

            } catch (err) {
                toast.error(err.response?.data?.message || 'Failed to update role')
            }
        }
    }
}
</script>
<style>
.permission-pill {
    cursor: pointer;
    padding: 8px 12px;
    border-radius: 50px;
    transition: 0.2s;
    border: 1px solid #ddd;
}

.permission-pill:hover {
    transform: scale(1.05);
}
</style>
