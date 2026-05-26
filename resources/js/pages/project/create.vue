<template>
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Add Project</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Add Project</li>
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
                            <div class="card-title">Add Project</div>
                        </div>
                        <!--end::Header-->
                        <!--begin::Form-->
                        <form @submit.prevent="createProject">
                            <!--begin::Body-->
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" v-model.trim="name" name="name" />

                                </div>

                                <!-- Description -->
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control" v-model="description" rows="3"
                                        placeholder="Enter description"></textarea>
                                </div>


                                <!-- Document Upload -->
                                <div class="mb-3">
                                    <label class="form-label">Documents</label>

                                    <div class="border rounded p-4 text-center upload-box" @dragover.prevent
                                        @drop.prevent="handleDrop" @click="triggerFileInput" style="cursor: pointer;">
                                        <input type="file" ref="fileInput" class="d-none" @change="handleFileChange"
                                            accept=".pdf,.doc,.docx" multiple />

                                        <div v-if="documents.length === 0">
                                            <i class="bi bi-cloud-upload fs-1"></i>
                                            <p class="mb-0">Drag & drop files here or click to upload</p>
                                            <small class="text-muted">PDF, DOC, DOCX (Max 10MB per file)</small>
                                        </div>

                                        <!-- Preview -->
                                        <div v-else class="text-start" @click.stop>
                                            <div v-for="(file, index) in documents" :key="index"
                                                class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded bg-light">
                                                <div>
                                                    <i class="bi bi-file-earmark-text me-2 text-primary"></i>
                                                    <span class="fw-medium">{{ file.name }}</span>
                                                    <small class="text-muted ms-2">({{ (file.size / 1024 / 1024).toFixed(2) }} MB)</small>
                                                </div>

                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                    @click.stop="removeFile(index)">
                                                    <i class="bi bi-trash"></i> Remove
                                                </button>
                                            </div>
                                            <div class="text-center mt-3">
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                    @click.stop="triggerFileInput">
                                                    <i class="bi bi-plus-lg"></i> Add More Files
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>








                            </div>
                            <!--end::Body-->
                            <!--begin::Footer-->
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                            <!--end::Footer-->
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

export default {
    data() {
        return {
            name: '',
            description: '',
            documents: [], // Multiple documents array

        }
    },

    mounted() {
    },

    methods: {

        // ================= FILE HANDLING =================
        triggerFileInput() {
            this.$refs.fileInput.click()
        },

        handleFileChange(e) {
            const files = Array.from(e.target.files)
            this.addFiles(files)
        },

        handleDrop(e) {
            const files = Array.from(e.dataTransfer.files)
            this.addFiles(files)
        },

        addFiles(files) {
            const toast = useToast()
            files.forEach(file => {
                const ext = file.name.split('.').pop().toLowerCase()
                if (['pdf', 'doc', 'docx'].includes(ext)) {
                    if (file.size <= 10240 * 1024) {
                        this.documents.push(file)
                    } else {
                        toast.error(`File is too large: ${file.name}. Max 10MB allowed.`)
                    }
                } else {
                    toast.error(`Invalid file type: ${file.name}. Only PDF, DOC, and DOCX are allowed.`)
                }
            })
            // Reset the input value so the same files can be re-selected if removed
            if (this.$refs.fileInput) {
                this.$refs.fileInput.value = null
            }
        },

        removeFile(index) {
            this.documents.splice(index, 1)
        },

        // ================= FETCH DATA =================
        async getCreateData() {
            const toast = useToast()

            try {
                const res = await this.$axios.get('/project-store')

                this.roles = res.data.data.roles || []
                this.departments = res.data.data.departments || []
                this.projects = res.data.data.projects || []

            } catch (err) {
                toast.error('Failed to load form data')
            }
        },

        // ================= STORE PROJECT =================
        async createProject() {
            const toast = useToast()

            // validation
            if (!this.name || !this.description || this.documents.length === 0) {
                toast.error('Name, Description and at least one Document are required')
                return
            }

            try {

                const formData = new FormData()

                formData.append('name', this.name)
                formData.append('description', this.description)
                
                // Append each file to document[] array
                this.documents.forEach(file => {
                    formData.append('document[]', file)
                })

                const res = await this.$axios.post('/project-store', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })

                toast.success(res.data.message)

                // reset form
                this.name = ''
                this.description = ''
                this.documents = []

                if (this.$refs.fileInput) {
                    this.$refs.fileInput.value = null
                }

                this.$router.push({ path: '/list-project' })

            } catch (err) {
                toast.error(err.response?.data?.message || 'Failed to create project')
            }
        }
    }
}
</script>
