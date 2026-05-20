<template>
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">List Project</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">List Project</li>
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
                            <h5 class="mb-0">Projects List</h5>
                            <input v-model="search" type="text" class="form-control w-25"
                                    placeholder="Search projects by name..." />

                        </div>

                        <!-- Card Body -->
                        <div class="card-body">
                            
                                
                            

                            <div class="table-responsive">
                                <EasyDataTable :headers="headers" :items="items" :server-options="serverOptions"
                                    :server-items-length="serverItemsLength" @update:server-options="loadFromServer">
                                    <template #item-status="{ id, status }">
                                        <div class="d-flex align-items-center gap-2">

                                            <!-- Switch -->
                                            <div class="form-check form-switch m-0">
                                                <input class="form-check-input" type="checkbox" :checked="status"
                                                    @change="toggleStatus(id, $event.target.checked)" />
                                            </div>

                                            <!-- Badge -->
                                            <span class="badge" :class="status ? 'bg-success' : 'bg-danger'">
                                                {{ status ? 'Active' : 'Inactive' }}
                                            </span>

                                        </div>
                                    </template>

                                    <!-- Actions -->
                                    <template #item-actions="{ id }">

                                        <button class="btn btn-sm btn-primary me-2" @click="editProject(id)">
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
                        Are you sure you want to delete this project?
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" @click="showModal = false">Cancel</button>
                        <button class="btn btn-danger" @click="deleteProject">Delete</button>
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
    { text: "Name", value: "name" },
    { text: "Description", value: "description" },
    { text: "Status", value: "status" },
    { text: "Actions", value: "actions" }
]

// ================= SEARCH (DEBOUNCE) =================
const debouncedSearch = _.debounce(() => {
    loadFromServer({
        page: 1,
        rowsPerPage: serverOptions.value.rowsPerPage
    })
}, 500)

watch(search, debouncedSearch)

// ================= LOAD USERS =================
const loadFromServer = async (options) => {
    try {
        serverOptions.value = options

        const res = await axios.get(`/projects-list`, {
            params: {
                page: options.page,
                per_page: options.rowsPerPage,
                search: search.value
            }
        })

        const project = res.data.data.projects

        //  Map response properly
        items.value = project.data.map(item => ({
            id: item.id,
            name: item.name,
            description: item.description,
            status: item.status,
        }))

        serverItemsLength.value = project.total

    } catch (error) {
        console.error("Error fetching projects:", error)
    }
}

// ================= EDIT =================
const editProject = (id) => {
    router.push(`/edit-project/${id}`)
}

// Confirm delete
const confirmDelete = (id) => {
    deleteId.value = id
    showModal.value = true
}

const deleteProject = async () => {
    await axios.delete(`/project/${deleteId.value}/delete`)
    showModal.value = false
    loadFromServer(serverOptions.value)
}

const toggleStatus = async (id, newStatus) => {
    try {
        await axios.put(`/project/${id}/status`, {
            status: newStatus
        })

        // update UI instantly (no reload needed)
        const project = items.value.find(u => u.id === id)
        if (project) {
            project.status = newStatus
        }

    } catch (error) {
        console.error("Status update failed:", error)
    }
}

// ================= LOAD ON MOUNT =================
onMounted(() => {
    loadFromServer(serverOptions.value)
})
</script>
