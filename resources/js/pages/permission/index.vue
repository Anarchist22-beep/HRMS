<template>
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">List Permission</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">List Permission</li>
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
                            <h5 class="mb-0">List Permission</h5>

                            <!-- Search -->
                            <input v-model="search" type="text" class="form-control w-25" placeholder="Search..." />
                        </div>

                        <!-- Body -->
                        <div class="card-body">
                            <EasyDataTable :headers="headers" :items="items" :server-options="serverOptions"
                                :server-items-length="serverItemsLength" @update:server-options="loadFromServer">

                                <!-- Actions -->
                                <template #item-actions="{ id }">
                                    <button class="btn btn-sm btn-primary me-2" @click="editPermission(id)">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <button class="btn btn-sm btn-danger" @click="confirmDelete(id)">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </template>

                            </EasyDataTable>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-start mt-3" v-if="pagination">

                                <button class="btn btn-sm btn-secondary me-2" :disabled="pagination.current_page === 1"
                                    @click="fetchPermissions(pagination.current_page - 1)">
                                    Previous
                                </button>

                                <span class="mx-2">
                                    Page {{ pagination.current_page }} / {{ pagination.last_page }}
                                </span>

                                <button class="btn btn-sm btn-secondary ms-2"
                                    :disabled="pagination.current_page === pagination.last_page"
                                    @click="fetchPermissions(pagination.current_page + 1)">
                                    Next
                                </button>

                            </div>
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
                        Are you sure you want to delete this permission?
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" @click="showModal = false">Cancel</button>
                        <button class="btn btn-danger" @click="deletePermission">Delete</button>
                    </div>

                </div>
            </div>
        </div>

    </div>
</template>
<script setup>
import { ref, onMounted } from "vue"
import EasyDataTable from "vue3-easy-data-table"
import axios from "axios"
import { useRouter } from "vue-router"
import { watch } from "vue"
import _ from "lodash"


const items = ref([])
const search = ref("") // keep if you plan backend search later
const showModal = ref(false)
const deleteId = ref(null)
const router = useRouter()

//  Server-side table control
const serverOptions = ref({
    page: 1,
    rowsPerPage: 10
})
//search

const debouncedSearch = _.debounce(() => {
    loadFromServer({
        page: 1,
        rowsPerPage: serverOptions.value.rowsPerPage
    })
}, 500)

//  Total items count (IMPORTANT for pagination UI)
const serverItemsLength = ref(0)

// Headers
const headers = [
    { text: "Name", value: "display_name" },
    { text: "Actions", value: "actions" }
]

//  Main API loader (CONNECTED to EasyTable)
const loadFromServer = async (options) => {
    try {
        serverOptions.value = options

        const res = await axios.get(`/permission-list`, {
            params: {
                page: options.page,
                per_page: options.rowsPerPage,
                search: search.value   //  ADD THIS
            }
        })

        const permissions = res.data.data.permissions

        items.value = permissions.data
        serverItemsLength.value = permissions.total

    } catch (error) {
        console.error("Error fetching permissions:", error)
    }
}
watch(search, debouncedSearch)

// Edit
const editPermission = (id) => {
    router.push(`/edit-permission/${id}`)
}

// Confirm delete
const confirmDelete = (id) => {
    deleteId.value = id
    showModal.value = true
}

// Delete API
const deletePermission = async () => {
    await axios.delete(`/permission/${deleteId.value}/delete`)
    showModal.value = false

    // reload current page after delete
    loadFromServer(serverOptions.value)
}

// Load initial data
onMounted(() => {
    loadFromServer(serverOptions.value)
})
</script>
