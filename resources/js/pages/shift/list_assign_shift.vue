<template>
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">List Assigned Shift</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">List Assigned Shift</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-md-12">

                    <!-- Card Start -->
                    <div class="card shadow-sm">

                        <!-- Card Header -->
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">List Assigned Shift</h5>

                            <!-- Search -->
                            <div style="width: 250px;">
                                <input type="text" class="form-control" placeholder="Search employee or shift..."
                                    v-model="search" />
                            </div>

                        </div>

                        <!-- Card Body -->
                        <div class="card-body">

                            <div class="table-responsive">
                                <EasyDataTable :headers="headers" :items="items" :server-options="serverOptions"
                                    :server-items-length="serverItemsLength" @update:server-options="loadFromServer">

                                    <!-- End Date -->
                                    <template #item-end_date="{ end_date }">
                                        <span>
                                            {{ end_date || 'Ongoing' }}
                                        </span>
                                    </template>

                                    <!-- Actions -->
                                    <template #item-actions="{ id }">

                                        <button class="btn btn-sm btn-primary me-2" @click="editSchedule(id)">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        <button class="btn btn-sm btn-danger" @click="confirmDelete(id)">
                                            <i class="bi bi-trash3-fill"></i>
                                        </button>

                                    </template>

                                </EasyDataTable>
                            </div>

                        </div>
                        <!-- End Card Body -->

                    </div>
                    <!-- End Card -->

                </div>
            </div>

        </div>
        <!-- Delete Modal -->
        <div v-if="showModal" class="modal fade show d-block" style="background: rgba(0,0,0,0.5)">
            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Delete</h5>
                        <button class="btn-close" @click="showModal = false"></button>
                    </div>

                    <div class="modal-body">
                        Are you sure you want to delete this department?
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" @click="showModal = false">Cancel</button>
                        <button class="btn btn-danger" @click="deleteSchedule">Delete</button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { ref, onMounted, watch } from "vue"
import EasyDataTable from "vue3-easy-data-table"
import axios from "axios"
import { useRouter } from "vue-router"
import _ from "lodash"

// ================= STATE =================
const items = ref([])
const search = ref("")
const router = useRouter()

const showModal = ref(false)
const deleteId = ref(null)

const serverOptions = ref({
    page: 1,
    rowsPerPage: 10
})

const serverItemsLength = ref(0)

// ================= HEADERS =================
const headers = [
    { text: "Employee Name", value: "user.name" },
    { text: "Shift Name", value: "shift.name" },
    { text: "Start Date", value: "start_date" },
    { text: "End Date", value: "end_date" },
    { text: "Actions", value: "actions" }
]

// ================= SEARCH =================
const debouncedSearch = _.debounce(() => {

    loadFromServer({
        page: 1,
        rowsPerPage: serverOptions.value.rowsPerPage
    })

}, 500)

watch(search, debouncedSearch)

// ================= LOAD DATA =================
const loadFromServer = async (options) => {

    try {

        serverOptions.value = options

        const res = await axios.get("/list-shift-schedule", {
            params: {
                page: options.page,
                per_page: options.rowsPerPage,
                search: search.value
            }
        })

        const schedules = res.data.data.schedules

        items.value = schedules.data || schedules

        serverItemsLength.value =
            schedules.total || schedules.length

    } catch (error) {

        console.error(
            "Error fetching schedules:",
            error
        )
    }
}

// ================= EDIT =================
const editSchedule = (id) => {
    router.push(`/edit-schedule/${id}`)
}

// ================= DELETE =================
const confirmDelete = (id) => {
    deleteId.value = id
    showModal.value = true
}

const deleteSchedule = async () => {

    try {

        await axios.delete(
            `/schedule/${deleteId.value}/delete`
        )

        showModal.value = false

        loadFromServer(serverOptions.value)

    } catch (error) {

        console.error(
            "Delete failed:",
            error
        )
    }
}

// ================= MOUNT =================
onMounted(() => {
    loadFromServer(serverOptions.value)
})
</script>
