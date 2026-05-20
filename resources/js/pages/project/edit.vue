<template>
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Update Project</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Update Project</li>
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
                            <div class="card-title">Update Project</div>
                        </div>
                        <!--end::Header-->
                        <!--begin::Form-->
                        <form @submit.prevent="UpdateProject">
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

                                    <!-- Document Upload -->
                                    <div class="mb-3">
                                        <label class="form-label">Document</label>

                                        <!-- OLD FILE -->
                                        <div v-if="oldDocument && !removeOldDocument"
                                            class="mb-2 p-2 border rounded d-flex justify-content-between align-items-center">
                                            <a :href="oldDocument" target="_blank">
                                                📄 View Current Document
                                            </a>

                                            <button type="button" class="btn btn-sm btn-danger"
                                                @click="removeOldDocument = true">
                                                Remove
                                            </button>
                                        </div>

                                        <!-- UPLOAD BOX -->
                                        <div v-if="removeOldDocument || !oldDocument"
                                            class="border rounded p-4 text-center upload-box" @dragover.prevent
                                            @drop.prevent="handleDrop" @click="triggerFileInput"
                                            style="cursor: pointer;">
                                            <input type="file" ref="fileInput" class="d-none" @change="handleFileChange"
                                                accept=".pdf,.doc,.docx" />

                                            <div v-if="!document">
                                                <i class="bi bi-cloud-upload fs-1"></i>
                                                <p class="mb-0">Drag & drop or click to upload</p>
                                                <small class="text-muted">PDF, DOC, DOCX (Max 10MB)</small>
                                            </div>

                                            <!-- NEW FILE PREVIEW -->
                                            <div v-else class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    📄 {{ document.name }}
                                                </div>

                                                <button type="button" class="btn btn-sm btn-danger"
                                                    @click.stop="removeFile">
                                                    Remove
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>








                            </div>
                            <!--end::Body-->
                            <!--begin::Footer-->
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Update</button>
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
            id: null,
            name: '',
            description: '',

            document: null,        // new file
            oldDocument: null,     // existing file URL
            removeOldDocument: false
        }
    },

    mounted() {
        this.getProject()
    },

    methods: {

        // ================= LOAD EDIT DATA =================
        async getProject() {
            const toast = useToast()

            try {
                const id = this.$route.params.id

                const res = await this.$axios.get(`/project/${id}/edit`)

                const project = res.data.data.project

                this.id = project.id
                this.name = project.name
                this.description = project.description
                this.oldDocument = project.document

            } catch (err) {
                toast.error('Failed to load project')
            }
        },

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

        // ================= UPDATE PROJECT =================
        async UpdateProject() {
            const toast = useToast()

            if (!this.name || !this.description) {
                toast.error('Name and Description are required')
                return
            }

            if (!this.oldDocument && !this.document) {
                toast.error('Document is required')
                return
            }

            try {

                const formData = new FormData()

                formData.append('name', this.name)
                formData.append('description', this.description)

                if (this.document) {
                    formData.append('document', this.document)
                }

                if (this.removeOldDocument) {
                    formData.append('remove_document', true)
                }

                // 🔥 IMPORTANT FIX
                formData.append('_method', 'PUT')

                const res = await this.$axios.post(
                    `/project/${this.id}/update`,
                    formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }
                )

                toast.success(res.data.message)

                this.$router.push({ path: '/list-project' })

            } catch (err) {
                console.log(err.response) // 👈 IMPORTANT DEBUG
                toast.error(err.response?.data?.message || 'Failed to update project')
            }
        }
    }
}
</script>
