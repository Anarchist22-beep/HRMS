<template>
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Add Shift</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Add Shift</li>
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
                            <div class="card-title">Add Shift</div>
                        </div>
                        <!--end::Header-->
                        <!--begin::Form-->
                        <form @submit.prevent="storeShift">

                            <!-- Card Body -->
                            <div class="card-body">

                                <!-- Shift Name -->
                                <div class="mb-3">
                                    <label class="form-label">Name</label>

                                    <input type="text" class="form-control" v-model.trim="name" />
                                </div>

                                <!-- Start Time -->
                                <div class="mb-3">
                                    <label class="form-label">Start Time</label>

                                    <TimePicker v-model="start_time" time-picker :is-24="false" :format="'hh:mm aa'"
                                        placeholder="Select Start Time" />
                                </div>

                                <!-- End Time -->
                                <div class="mb-3">
                                    <label class="form-label">End Time</label>

                                    <TimePicker v-model="end_time" time-picker :is-24="false" :format="'hh:mm aa'"
                                        placeholder="Select End Time" />
                                </div>

                                <!-- Hidden Hours -->
                                <input type="hidden" v-model="hours" />

                                <!-- Show Hours -->
                                <div class="mb-3">
                                    <label class="form-label">
                                        Working Hours
                                    </label>

                                    <input type="text" class="form-control" :value="hours" readonly />
                                </div>

                                <!-- Break Minutes -->
                                <div class="mb-3">
                                    <label class="form-label">
                                        Break Minutes
                                    </label>

                                    <input type="number" class="form-control" v-model="breaK_minute" />
                                </div>

                                <!-- Grace Minutes -->
                                <div class="mb-3">
                                    <label class="form-label">
                                        Grace Minutes
                                    </label>

                                    <input type="number" class="form-control" v-model="grace_minutes" />
                                </div>

                            </div>

                            <!-- Footer -->
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    Save
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

import { VueDatePicker } from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'

export default {

    components: {
        TimePicker: VueDatePicker
    },

    data() {
        return {

            name: '',

            start_time: null,
            end_time: null,

            hours: 0,

            breaK_minute: 60,

            grace_minutes: 15
        }
    },

    watch: {

        start_time() {
            this.calculateHours()
        },

        end_time() {
            this.calculateHours()
        },

        breaK_minute() {
            this.calculateHours()
        }
    },

    methods: {

        // Calculate Hours Automatically
        calculateHours() {

            if (!this.start_time || !this.end_time) {
                return
            }

            let start =
                this.start_time.hours +
                (this.start_time.minutes / 60)

            let end =
                this.end_time.hours +
                (this.end_time.minutes / 60)

            let diff = end - start

            // Night Shift Support
            if (diff < 0) {
                diff += 24
            }

            // Minus Break Time
            diff -= this.breaK_minute / 60

            // Prevent Negative
            if (diff < 0) {
                diff = 0
            }

            this.hours = parseFloat(diff.toFixed(2))
        },

        // Format Time
        formatTime(time) {

            if (!time) return null

            return `${String(time.hours).padStart(2, '0')}:${String(time.minutes).padStart(2, '0')}`
        },

        // Store Shift
        async storeShift() {

            const toast = useToast()

            try {

                const payload = {

                    name: this.name,

                    start_time: this.formatTime(this.start_time),

                    end_time: this.formatTime(this.end_time),

                    hours: this.hours,

                    breaK_minute: this.breaK_minute,

                    grace_minutes: this.grace_minutes
                }

                const res = await this.$axios.post(
                    '/shift-store',
                    payload
                )

                toast.success(res.data.message)

                // Reset Form
                this.name = ''
                this.start_time = null
                this.end_time = null
                this.hours = 0
                this.breaK_minute = 60
                this.grace_minutes = 15

                this.$router.push('/list-shift')

            } catch (err) {

                toast.error(
                    err.response?.data?.message ||
                    'Failed to create shift'
                )
            }
        }
    }
}
</script>

