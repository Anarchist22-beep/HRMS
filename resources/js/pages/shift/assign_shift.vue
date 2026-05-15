<template>
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Assign Shift</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Assign Shift</li>
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
                            <div class="card-title">Assign Shift</div>
                        </div>
                        <!--end::Header-->
                        <!--begin::Form-->
                        <form @submit.prevent="storeSchedule">

                            <!-- Card Body -->
                            <div class="card-body">

                                <!-- Department Name -->
                                <div class="mb-3">
                                    <label class="form-label">
                                        Department
                                    </label>

                                    <select class="form-select" v-model="department_id" @change="filterEmployees">
                                        <option value="">
                                            Select Department
                                        </option>

                                        <option v-for="department in departments" :key="department.id"
                                            :value="department.id">
                                            {{ department.name }}
                                        </option>
                                    </select>
                                </div>

                                <!-- employee -->
                                <div class="mb-3">
                                    <label class="form-label">
                                        Employee
                                    </label>

                                    <select class="form-select" v-model="user_id">
                                        <option value="">
                                            Select Employee
                                        </option>

                                        <option v-for="employee in filteredEmployees" :key="employee.id"
                                            :value="employee.id">
                                            {{ employee.name }}
                                        </option>
                                    </select>
                                </div>


                                <!-- Shift -->
                                <div class="mb-3">
                                    <label class="form-label">
                                        Shift
                                    </label>

                                    <select class="form-select" v-model="shift_id">
                                        <option value="">
                                            Select Shift
                                        </option>

                                        <option v-for="shift in shifts" :key="shift.id" :value="shift.id">
                                            {{ shift.name }}/{{ shift.start_time }}-{{ shift.end_time }}
                                        </option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">
                                        Start Date
                                    </label>

                                    <VueDatePicker v-model="start_date" :enable-time-picker="false"
                                        placeholder="Select Start Date" />
                                </div>

                                <!-- End Date -->
                                <div class="mb-3">
                                    <label class="form-label">
                                        End Date
                                    </label>

                                    <VueDatePicker v-model="end_date" :enable-time-picker="false"
                                        placeholder="Select End Date" />
                                </div>

                                <!-- Days -->
                                <div class="mb-3">
                                    <label class="form-label d-block mb-2">
                                        Working Days
                                    </label>

                                    <div class="d-flex flex-wrap gap-2">

                                        <button v-for="day in days" :key="day.key" type="button" class="btn" :class="selectedDays[day.key]
                                            ? 'btn-success'
                                            : 'btn-outline-secondary'" @click="toggleDay(day.key)">
                                            {{ day.label }}
                                        </button>

                                    </div>
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
import axios from 'axios'
import { useToast } from 'vue-toastification'

import { VueDatePicker } from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'

export default {

    components: {
        VueDatePicker
    },

    data() {
        return {

            departments: [],
            employees: [],
            filteredEmployees: [],
            shifts: [],

            department_id: '',
            user_id: '',
            shift_id: '',

            start_date: null,
            end_date: null,

            days: [
                { key: 'monday', label: 'Monday' },
                { key: 'tuesday', label: 'Tuesday' },
                { key: 'wednesday', label: 'Wednesday' },
                { key: 'thursday', label: 'Thursday' },
                { key: 'friday', label: 'Friday' },
                { key: 'saturday', label: 'Saturday' },
                { key: 'sunday', label: 'Sunday' }
            ],

            selectedDays: {
                monday: false,
                tuesday: false,
                wednesday: false,
                thursday: false,
                friday: false,
                saturday: false,
                sunday: false
            }
        }
    },

    mounted() {

        this.fetchDepartments()
        this.fetchEmployees()
        this.fetchShifts()
    },

    methods: {

        // Fetch Departments
        async fetchDepartments() {

            try {

                const res = await axios.get(
                    '/department-list',
                    {
                        params: {
                            all: true
                        }
                    }
                )

                this.departments =
                    res.data.data.departments

            } catch (error) {

                console.error(
                    'Failed to fetch departments',
                    error
                )
            }
        },

        // Fetch Employees
        async fetchEmployees() {

            try {

                const res = await axios.get(
                    '/fetch-employees'
                )

                this.employees =
                    res.data.data.employee

                this.filteredEmployees =
                    this.employees

            } catch (error) {

                console.error(
                    'Failed to fetch employees',
                    error
                )
            }
        },

        // Fetch Shifts
        async fetchShifts() {

            try {

                const res = await axios.get(
                    '/shift-list',
                    {
                        params: {
                            all: true
                        }
                    }
                )

                this.shifts =
                    res.data.data.shifts

            } catch (error) {

                console.error(
                    'Failed to fetch shifts',
                    error
                )
            }
        },

        // Filter Employees By Department
        filterEmployees() {

            // Reset employee selection
            this.user_id = ''

            if (!this.department_id) {

                this.filteredEmployees =
                    this.employees

                return
            }

            this.filteredEmployees =
                this.employees.filter(employee =>
                    employee.depart_id ==
                    this.department_id
                )
        },

        // Format Date For Laravel
        formatDate(date) {

            if (!date) return null

            const d = new Date(date)

            const year =
                d.getFullYear()

            const month =
                String(
                    d.getMonth() + 1
                ).padStart(2, '0')

            const day =
                String(
                    d.getDate()
                ).padStart(2, '0')

            return `${year}-${month}-${day}`
        },

        // Toggle Days
        toggleDay(day) {

            this.selectedDays[day] =
                !this.selectedDays[day]
        },

        // Store Schedule
        async storeSchedule() {

            const toast = useToast()

            try {

                const payload = {

                    department_id:
                        this.department_id,

                    user_id:
                        this.user_id,

                    shift_id:
                        this.shift_id,

                    start_date:
                        this.formatDate(
                            this.start_date
                        ),

                    end_date:
                        this.formatDate(
                            this.end_date
                        ),

                    ...this.selectedDays
                }

                const res = await axios.post(
                    '/shift-schedule-store',
                    payload
                )

                toast.success(
                    res.data.message
                )

                // Reset Form
                this.department_id = ''
                this.user_id = ''
                this.shift_id = ''

                this.start_date = null
                this.end_date = null

                this.filteredEmployees =
                    this.employees

                this.selectedDays = {
                    monday: false,
                    tuesday: false,
                    wednesday: false,
                    thursday: false,
                    friday: false,
                    saturday: false,
                    sunday: false
                }

                this.$router.push(
                    '/list-assign-shift'
                )

            } catch (error) {

                toast.error(
                    error.response?.data?.message ||
                    'Failed to create schedule'
                )
            }
        }
    }
}
</script>
