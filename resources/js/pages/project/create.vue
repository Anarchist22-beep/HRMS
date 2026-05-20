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
                                    <label class="form-label">Document</label>

                                    <div class="border rounded p-4 text-center upload-box" @dragover.prevent
                                        @drop.prevent="handleDrop" @click="triggerFileInput" style="cursor: pointer;">
                                        <input type="file" ref="fileInput" class="d-none" @change="handleFileChange"
                                            accept=".pdf,.doc,.docx" />

                                        <div v-if="!document">
                                            <i class="bi bi-cloud-upload fs-1"></i>
                                            <p class="mb-0">Drag & drop file here or click to upload</p>
                                            <small class="text-muted">PDF, DOC, DOCX (Max 10MB)</small>
                                        </div>

                                        <!-- Preview -->
                                        <div v-else class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="bi bi-file-earmark-text"></i>
                                                {{ document.name }}
                                            </div>

                                            <button type="button" class="btn btn-sm btn-danger"
                                                @click.stop="removeFile">
                                                Remove
                                            </button>
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
            document: null, //  REQUIRED

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
            const file = e.target.files[0]
            if (file) {
                this.document = file
            }
        },

        handleDrop(e) {
            const file = e.dataTransfer.files[0]
            if (file) {
                this.document = file
            }
        },

        removeFile() {
            this.document = null
            this.$refs.fileInput.value = null
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
            if (!this.name || !this.description || !this.document) {
                toast.error('Name, Description and Document are required')
                return
            }

            try {

                const formData = new FormData()

                formData.append('name', this.name)
                formData.append('description', this.description)
                formData.append('document', this.document)

                const res = await this.$axios.post('/project-store', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })

                toast.success(res.data.message)

                // reset form
                this.name = ''
                this.description = ''
                this.document = null

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
