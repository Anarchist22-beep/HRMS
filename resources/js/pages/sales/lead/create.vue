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
                                    <label class="form-label">Country</label>

                                    <Multiselect v-model="country_id" :options="countries" label="countryName"
                                        valueProp="id" placeholder="Select Country" searchable />
                                </div>


                                <div class="mb-3">
                                    <label class="form-label">Phone No:</label>

                                    <vue-tel-input v-model="phone_no" mode="international"
                                        :preferred-countries="['PK', 'US', 'AE']" placeholder="Enter phone number" />
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
import Multiselect from '@vueform/multiselect'


export default {
    components: {
        Multiselect
    },

    data() {
        return {
            name: '',
            email: '',
            phone_no: '',
            description: '',
            location: '',
            profile_link: '',

            countries: [],
            country_id: ''
        }
    },

    async mounted() {
        await this.getCountries()
    },

    methods: {

        // GET COUNTRIES
        async getCountries() {
            try {
                const res = await this.$axios.get('/get-countries')

                // FIX: correct nested response
                this.countries = res.data.data.data

            } catch (error) {
                console.log(error)
            }
        },

        // STORE LEAD
        async storeLead() {
            const toast = useToast()

            if (
                !this.name ||
                !this.email ||
                !this.phone_no ||
                !this.description ||
                !this.location ||
                !this.profile_link ||
                !this.country_id
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
                    profile_link: this.profile_link,
                    country_id: this.country_id
                })

                toast.success(res.data.message)

                // reset form
                this.name = ''
                this.email = ''
                this.phone_no = ''
                this.description = ''
                this.location = ''
                this.profile_link = ''
                this.country_id = ''

                this.$router.push({ path: '/sales/list-lead' })

            } catch (err) {
                toast.error(err.response?.data?.message || 'Failed to create lead')
            }
        }
    }
}

</script>
