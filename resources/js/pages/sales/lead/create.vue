<template>
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Add Lead</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Add Lead</li>
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
                            <div class="card-title">Add Lead</div>
                        </div>
                        <!--end::Header-->
                        <!--begin::Form-->
                        <form @submit.prevent="storeLead">
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
                                    <label class="form-label">Phone No:</label>
                                    <input type="number" class="form-control" v-model="phone_no" />
                                </div>

                                <!-- Description -->
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control" v-model="description" rows="3"
                                        placeholder="Enter description"></textarea>
                                </div>

                                <!-- Location -->
                                <div class="mb-3">
                                    <label class="form-label">Location</label>
                                    <textarea class="form-control" v-model="location" rows="2"
                                        placeholder="Enter location"></textarea>
                                </div>
                                 <!-- Profile Link -->

                                <div class="mb-3">
                                    <label class="form-label">Profile Link</label>
                                    <textarea class="form-control" v-model="profile_link" rows="2"
                                        placeholder="Enter Profile Link"></textarea>
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
            phone_no: '',
            description: '',
            location: '',
            profile_link: '',

        }
    },

    methods: {



        //  Store lead
        async storeLead() {
            const toast = useToast()

            if (
                !this.name ||
                !this.email ||
                !this.phone_no ||
                !this.description ||
                !this.location ||
                !this.profile_link
            ) {
                toast.error('All fields are required')
                return
            }

            try {
                const res = await this.$axios.post('/lead-store', {
                    name: this.name,
                    email: this.email,
                    phone_no: this.phone_no,
                    description: this.description,
                    location: this.location,
                    profile_link: this.profile_link

                })

                toast.success(res.data.message)

                // reset form
                this.name = ''
                this.email = ''
                this.phone_no = ''
                this.description = ''
                this.location = ''
                this.profile_link = ''

                this.$router.push({ path: '/sales/list-lead' })

            } catch (err) {
                toast.error(err.response?.data?.message || 'Failed to create lead')
            }
        }
    }
}
</script>
