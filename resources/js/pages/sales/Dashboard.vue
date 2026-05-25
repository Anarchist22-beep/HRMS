<template>
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Dashboard</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                    </ol>
                </div>
            </div>
            <!--end::Row-->
        </div>
        <!--end::Container-->
    </div>
    <div class="app-content">
        <div class="container-fluid">

            <div class="row">
                <div class="row mb-3">

                    <div class="col-md-3">
                        <select v-model="selectedYear" class="form-control">
                            <option v-for="y in yearsList" :key="y" :value="y">
                                {{ y }}
                            </option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <select v-model="selectedMonth" class="form-control">
                            <option value="">All Months</option>
                            <option v-for="m in monthsList" :key="m.id" :value="m.id">
                                {{ m.name }}
                            </option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <button class="btn btn-success w-100" @click="applyFilter">
                            Apply Filter
                        </button>
                    </div>

                </div>

                <!-- Total Leads Chart -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Total Leads Per Month</h5>
                        </div>

                        <div class="card-body">
                            <Line :data="totalLeadsChart" />
                        </div>
                    </div>
                </div>

            </div>


            <!-- CHARTS -->
            <div class="row">

                <!-- Earnings Chart -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Monthly Earnings</h5>
                        </div>

                        <div class="card-body">
                            <Bar :data="earningsChart" />
                        </div>
                    </div>
                </div>

                <!-- Qualified Leads Chart -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Qualified Leads</h5>
                        </div>

                        <div class="card-body">
                            <Line :data="leadsChart" />
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useToast } from 'vue-toastification'

// Chart.js
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    LineElement,
    PointElement,
    CategoryScale,
    LinearScale
} from 'chart.js'

import { Bar, Line } from 'vue-chartjs'

ChartJS.register(
    Title,
    Tooltip,
    Legend,
    BarElement,
    LineElement,
    PointElement,
    CategoryScale,
    LinearScale
)

const toast = useToast()

const stats = ref({
    yearly_leads: 0
})

const earningsChart = ref({
    labels: [],
    datasets: []
})

const totalLeadsChart = ref({
    labels: [],
    datasets: []
})

const leadsChart = ref({
    labels: [],
    datasets: []
})

const months = [
    'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
    'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
]
const selectedYear = ref(new Date().getFullYear())
const selectedMonth = ref('')
const monthsList = [
    { id: 1, name: 'January' },
    { id: 2, name: 'February' },
    { id: 3, name: 'March' },
    { id: 4, name: 'April' },
    { id: 5, name: 'May' },
    { id: 6, name: 'June' },
    { id: 7, name: 'July' },
    { id: 8, name: 'August' },
    { id: 9, name: 'September' },
    { id: 10, name: 'October' },
    { id: 11, name: 'November' },
    { id: 12, name: 'December' }
]
const yearsList = [2023, 2024, 2025, 2026, 2027, 2028, 2029, 2030, 2031, 2032, 2033]

const fetchDashboardStats = async () => {
    try {

        const response = await axios.get('/lead-analytics')

        if (response.data.status === 'success') {

            const data = response.data.data

            stats.value.yearly_leads = data.yearly_leads

            // Earnings chart
            earningsChart.value = {
                labels: months,
                datasets: [
                    {
                        label: 'Earnings',
                        data: data.monthly_earnings,
                        backgroundColor: '#4CAF50'
                    }
                ]
            }

            // Leads chart
            leadsChart.value = {
                labels: months,
                datasets: [
                    {
                        label: 'Qualified Leads',
                        data: data.qualified_leads,
                        borderColor: '#2196F3',
                        tension: 0.4
                    }
                ]
            }

            //total leads

            totalLeadsChart.value = {
                labels: months,
                datasets: [
                    {
                        label: 'Total Leads',
                        data: data.monthly_leads,
                        borderColor: '#FF9800',
                        backgroundColor: '#FF9800',
                        tension: 0.4
                    }
                ]
            }
        }

    } catch (error) {
        console.error(error)
        toast.error('Failed to fetch dashboard data')
    }
}
const applyFilter = async () => {
    const response = await axios.get('/lead-analytics', {
        params: {
            year: selectedYear.value,
            month: selectedMonth.value
        }
    })

    const data = response.data.data

    earningsChart.value = {
        labels: monthsList.map(m => m.name),
        datasets: [{
            label: 'Earnings',
            data: data.monthly_earnings,
            backgroundColor: '#4CAF50'
        }]
    }

    totalLeadsChart.value = {
        labels: monthsList.map(m => m.name),
        datasets: [{
            label: 'Total Leads',
            data: data.monthly_leads,
            borderColor: '#FF9800',
            backgroundColor: '#FF9800'
        }]
    }
}

onMounted(() => {
    fetchDashboardStats()
})
</script>
