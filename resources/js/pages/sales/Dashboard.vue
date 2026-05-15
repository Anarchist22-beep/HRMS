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
        <!--begin::Container-->
        <div class="container-fluid">
            <!-- Info boxes -->
            <div class="row">
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon text-bg-primary shadow-sm">
                            <i class="bi bi-person-fill"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">User</span>
                            <span class="info-box-number">
                                {{ stats.users }}
                                <small></small>
                            </span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>

                
                <!-- /.col -->

                <!-- /.col -->
            </div>
            <!-- /.row -->

        </div>
        <!--end::Container-->
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useToast } from 'vue-toastification'

const toast = useToast()

// Dashboard stats
const stats = ref({
    users: 0,
    shift: 0,
    department: 0,
    project: 0
})

// Fetch dashboard data
const fetchDashboardStats = async () => {
    try {

        const response = await axios.get('/admin/dashboard')

        if (response.data.status === 'success') {
            stats.value = response.data.data
        }

    } catch (error) {

        console.error(error)

        toast.error('Failed to fetch dashboard data')
    }
}

onMounted(() => {
    fetchDashboardStats()
})
</script>
