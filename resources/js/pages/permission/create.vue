<template>
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Add Permission</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Add Permission</li>
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
                            <div class="card-title">Add Permission</div>
                        </div>
                        <!--end::Header-->
                        <!--begin::Form-->
                        <form @submit.prevent="storePermission">
                            <!--begin::Body-->
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        v-model="name"
                                        placeholder="Enter permission name"
                                    />

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
            name: ''
        }
    },

    methods: {
        async storePermission() {
            const toast = useToast()

            if (!this.name) {
                toast.error('Permission name is required')
                return
            }

            try {
                const res = await this.$axios.post('/permission-store', {
                    name: this.name
                })

                toast.success(res.data.message)

                // clear form
                this.name = ''

                // redirect to list page
                this.$router.push({ path: '/list-permission' })

            } catch (err) {
                toast.error(
                    err.response?.data?.message || 'Failed to create permission'
                )
            }
        }
    }
}
</script>
