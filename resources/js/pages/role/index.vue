<template>
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">List Role</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">List Role</li>
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
                            <h5 class="mb-0">List Role</h5>

                            <!-- Search -->
                            <input v-model="search" type="text" class="form-control w-25" placeholder="Search..." />
                        </div>

                        <!-- Body -->
                        <div class="card-body">
                            <EasyDataTable :headers="headers" :items="items" :server-options="serverOptions"
                                :server-items-length="serverItemsLength" @update:server-options="loadFromServer">

                                <!-- Actions -->
                                <template #item-actions="{ id }">
                                    <button class="btn btn-sm btn-primary me-2" @click="editRole(id)">
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
                        Are you sure you want to delete this permission?
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" @click="showModal = false">Cancel</button>
                        <button class="btn btn-danger" @click="deleteRole">Delete</button>
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

const router = useRouter()

const serverOptions = ref({
    page: 1,
    rowsPerPage: 10
})

const serverItemsLength = ref(0)

// Headers
const headers = [
    { text: "Name", value: "name" },
    { text: "Actions", value: "actions" }
]

//search

const debouncedSearch = _.debounce(() => {
    loadFromServer({
        page: 1,
        rowsPerPage: serverOptions.value.rowsPerPage
    })
}, 500)

//  Fetch Roles


const loadFromServer = async (options) => {
    try {
        serverOptions.value = options

        const res = await axios.get(`/role-list`, {
            params: {
                page: options.page,
                per_page: options.rowsPerPage,
                search: search.value   //  ADD THIS
            }
        })

        const roles = res.data.data.roles

        items.value = roles.data
        serverItemsLength.value = roles.total

    } catch (error) {
        console.error("Error fetching roles:", error)
    }
}

watch(search, debouncedSearch)




//  Edit Role
const editRole = (id) => {
    router.push(`/edit-role/${id}`)
}

//  Confirm delete
const confirmDelete = (id) => {
    deleteId.value = id
    showModal.value = true
}

//  Delete Role
const deleteRole = async () => {
    try {
        await axios.delete(`/role/${deleteId.value}/delete`)
        showModal.value = false
         loadFromServer(serverOptions.value)
    } catch (err) {
        console.log(err)
    }
}

// Load data
onMounted(() => {
    loadFromServer(serverOptions.value)
})
</script>
