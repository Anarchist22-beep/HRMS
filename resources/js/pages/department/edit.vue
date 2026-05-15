<template>
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Edit Department</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit Department</li>
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
                            <div class="card-title">Edit Department</div>
                        </div>
                        <!--end::Header-->
                        <!--begin::Form-->
                        <form @submit.prevent="updateDepartment">
                            <div class="card-body">

                                <div class="mb-3">
                                    <label class="form-label">Name</label>

                                    <input type="text" class="form-control" v-model="name" />
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" v-model="description" rows="3"
                                        placeholder="Enter description"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Status</label>

                                    <select class="form-control" v-model="status">
                                        <option :value="1">Active</option>
                                        <option :value="0">Inactive</option>
                                    </select>
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
            description: '',
            id: null
        }
    },

    async mounted() {
        const toast = useToast()

        this.id = this.$route.params.id

        try {
            const res = await this.$axios.get(`/department/${this.id}/edit`)

            // IMPORTANT: adjust based on your API response
            this.name = res.data.data.data.name
            this.description = res.data.data.data.description
            this.status = res.data.data.data.status

        } catch (err) {
            toast.error('Failed to load department')
        }
    },

    methods: {
        async updateDepartment() {
            const toast = useToast()

            if (!this.name) {
                toast.error('Department name is required')
                return
            }

            if (!this.description) {
                toast.error('Department description is required')
                return
            }

            try {
                const res = await this.$axios.put(`/department/${this.id}/update`, {
                    name: this.name,
                    description: this.description,
                    status: this.status
                })

                toast.success(res.data.message)

                // redirect back to list
                this.$router.push({ path: '/list-department' })

            } catch (err) {
                toast.error(
                    err.response?.data?.message || 'Failed to update department'
                )
            }
        }
    }
}
</script>
