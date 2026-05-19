<template>
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Update Lead</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Update Lead</li>
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
                            <div class="card-title">Update Lead</div>
                        </div>
                        <!--end::Header-->
                        <!--begin::Form-->
                        <form @submit.prevent="updateLead">
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
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                            <!--end::Footer-->
                        </form>
                        <!--end::Form-->
                    </div>
                    <!--end::Quick Example-->

                    <!-- update status -->
                    <div class="card card-primary card-outline mb-4" v-if="hasPermission('update_lead_status')">
                        <div class="card-header">
                            <div class="card-title">Update Lead Status</div>
                        </div>

                        <form @submit.prevent="updateStatus">
                            <div class="card-body">

                                <!-- Amount -->
                                <div class="mb-3">
                                    <label class="form-label">Amount</label>
                                    <input type="number" class="form-control" v-model="amount" />
                                </div>

                                <!-- Qualified Dropdown -->
                                <div class="mb-3">
                                    <label class="form-label">Status</label>

                                    <select class="form-control" v-model="qualified">
                                        <option :value="true">Qualified</option>
                                        <option :value="false">Not Qualified</option>
                                    </select>

                                </div>

                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-success">
                                    Update Status
                                </button>
                            </div>
                        </form>
                    </div>


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
            id: null,
            name: '',
            email: '',
            phone_no: '',
            description: '',
            location: '',
            profile_link: '',
            amount: '',
            qualified: false
        }
    },

    computed: {
        permissions() {
            return JSON.parse(localStorage.getItem('permissions') || '[]')
        }
    },

    methods: {

        /*
        |--------------------------------------------------------------------------
        | CHECK PERMISSION
        |--------------------------------------------------------------------------
        */
        hasPermission(permission) {
            return this.permissions.includes(permission)
        },

        /*
        |--------------------------------------------------------------------------
        | LOAD LEAD DATA
        |--------------------------------------------------------------------------
        */
        async getLead() {
            const toast = useToast()

            try {
                const id = this.$route.params.id
                this.id = id

                const res = await this.$axios.get(`/lead/${id}/edit`)

                const lead = res.data.data.data

                this.name = lead.name
                this.email = lead.email
                this.phone_no = lead.phone_no
                this.description = lead.description
                this.location = lead.location
                this.profile_link = lead.profile_link
                this.amount = lead.amount
                this.qualified = !!lead.qualified

            } catch (err) {
                toast.error('Failed to load lead')
            }
        },

        /*
        |--------------------------------------------------------------------------
        | UPDATE LEAD BASIC INFO
        |--------------------------------------------------------------------------
        */
        async updateLead() {
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
                const res = await this.$axios.put(`/lead/${this.id}/update`, {
                    name: this.name,
                    email: this.email,
                    phone_no: this.phone_no,
                    description: this.description,
                    location: this.location,
                    profile_link: this.profile_link,

                })

                toast.success(res.data.message)

                this.$router.push('/sales/list-lead')

            } catch (err) {
                toast.error(err.response?.data?.message || 'Update failed')
            }
        },

        /*
        |--------------------------------------------------------------------------
        | UPDATE LEAD STATUS
        |--------------------------------------------------------------------------
        */
        async updateStatus() {
            const toast = useToast()

            if (!this.hasPermission('update_lead_status')) {
                toast.error('You do not have permission')
                return
            }

            try {
                const res = await this.$axios.put(`/lead/${this.id}/status`, {
                    amount: this.amount,
                    qualified: this.qualified
                })

                toast.success(res.data.message)

            } catch (err) {
                toast.error(err.response?.data?.message || 'Status update failed')
            }
        }
    },

    mounted() {
        this.getLead()
    }
}
</script>
