<template>
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Add Department</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Add Department</li>
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
                            <div class="card-title">Add Department</div>
                        </div>
                        <!--end::Header-->
                        <!--begin::Form-->
                        <form @submit.prevent="storeDepartment">
                            <!--begin::Body-->
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" v-model="name"
                                        placeholder="Enter department name" />

                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" v-model="description" rows="3"
                                        placeholder="Enter description"></textarea>
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
            description: ''

        }
    },

    methods: {
        async storeDepartment() {
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
                const res = await this.$axios.post('/department-store', {
                    name: this.name,
                    description: this.description
                })

                toast.success(res.data.message)

                // clear form
                this.name = '',
                    this.description = ''

                // redirect to list page
                this.$router.push({ path: '/list-department' })

            } catch (err) {
                toast.error(
                    err.response?.data?.message || 'Failed to create department'
                )
            }
        }
    }
}
</script>
