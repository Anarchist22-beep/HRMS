<template>
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">List Department</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">List Department</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">

                    <div class="card shadow-sm">

                        <!-- Header -->
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">List Department</h5>

                            <!-- Search -->
                            <input v-model="search" type="text" class="form-control w-25" placeholder="Search..." />
                        </div>

                        <!-- Body -->
                        <div class="card-body">
                            <EasyDataTable :headers="headers" :items="items" :server-options="serverOptions"
                                :server-items-length="serverItemsLength" @update:server-options="loadFromServer">
                                <template #item-status="{ status }">
                                    <span class="badge" :class="status ? 'bg-success' : 'bg-danger'">
                                        {{ status ? 'Active' : 'Inactive' }}
                                    </span>
                                </template>

                                <!-- Actions -->
                                <template #item-actions="{ id }">

                                    <button class="btn btn-sm btn-primary me-2" @click="editDepartment(id)">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>


                                    <button class="btn btn-sm btn-danger" @click="confirmDelete(id)">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </template>

                            </EasyDataTable>
                        </div>

                    </div>

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
                        <button class="btn btn-danger" @click="deleteDepartment">Delete</button>
                    </div>

                </div>
            </div>
        </div>

    </div>
</template>
<script setup>
import { ref, onMounted, computed } from "vue"
import EasyDataTable from "vue3-easy-data-table"
import axios from "axios"
import { useRouter } from "vue-router"
import { watch } from "vue"
import _ from "lodash"

const items = ref([])
const search = ref("")
const showModal = ref(false)
const deleteId = ref(null)
const router = useRouter() //  ADD THIS

const serverOptions = ref({
    page: 1,
    rowsPerPage: 10
})

const serverItemsLength = ref(0)


// Headers
const headers = [
    { text: "Name", value: "name" },
    { text: "Status", value: "status" },
    { text: "Actions", value: "actions" }
]

const debouncedSearch = _.debounce(() => {
    loadFromServer({
        page: 1,
        rowsPerPage: serverOptions.value.rowsPerPage
    })
}, 500)

// Fetch API

const loadFromServer = async (options) => {
    try {
        serverOptions.value = options

        const res = await axios.get(`/department-list`, {
            params: {
                page: options.page,
                per_page: options.rowsPerPage,
                search: search.value
            }
        })

        const departments = res.data.data.departments

        //  Correct mapping
        items.value = departments.data
        serverItemsLength.value = departments.total

    } catch (error) {
        console.error("Error fetching departments:", error)
    }
}
//search
watch(search, debouncedSearch)

// Edit
const editDepartment = (id) => {
    router.push(`/edit-department/${id}`)
}

// Confirm delete
const confirmDelete = (id) => {
    deleteId.value = id
    showModal.value = true
}

// Delete API
const deleteDepartment = async () => {
    await axios.delete(`/department/${deleteId.value}/delete`)
    showModal.value = false
    loadFromServer(serverOptions.value)
}

// Load data
onMounted(() => {
    loadFromServer(serverOptions.value)
})
</script>
