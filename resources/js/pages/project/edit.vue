<template>
    <div class="app-content-header">
        <div class="container-fluid">
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
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="row g-4">
                <div class="col-md-12">

                    <!-- ═══════════════ UPDATE PROJECT CARD ═══════════════ -->
                    <div class="card card-primary card-outline mb-4">
                        <div class="card-header">
                            <div class="card-title">Update Project</div>
                        </div>

                        <form @submit.prevent="UpdateProject">
                            <div class="card-body">

                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" v-model.trim="name" name="name" />
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control" v-model="description" rows="3"
                                        placeholder="Enter description"></textarea>
                                </div>

                                <div class="mb-4">
                                    <div v-if="oldDocuments.length > 0" class="mb-3">
                                        <label class="form-label d-block fw-semibold text-secondary">Current
                                            Documents</label>
                                        <div v-for="(doc, idx) in oldDocuments" :key="doc.id"
                                            class="p-2 border rounded d-flex justify-content-between align-items-center mb-2 bg-light shadow-sm">
                                            <div>
                                                <i class="bi bi-file-earmark-pdf-fill me-2 text-danger fs-5"></i>
                                                <a :href="doc.document" target="_blank"
                                                    class="fw-medium text-decoration-none text-primary">
                                                    Document {{ idx + 1 }}
                                                </a>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                @click="removeOldDocument(doc.id, idx)">
                                                <i class="bi bi-trash"></i> Remove
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold text-secondary">Upload Additional
                                            Documents</label>
                                        <div class="border rounded p-4 text-center upload-box bg-white"
                                            @dragover.prevent @drop.prevent="handleDrop" @click="triggerFileInput"
                                            style="cursor: pointer;">
                                            <input type="file" ref="fileInput" class="d-none" @change="handleFileChange"
                                                accept=".pdf,.doc,.docx" multiple />

                                            <div v-if="documents.length === 0">
                                                <i class="bi bi-cloud-upload fs-1 text-primary"></i>
                                                <p class="mb-0 mt-2">Drag & drop files here or click to upload</p>
                                                <small class="text-muted">PDF, DOC, DOCX (Max 10MB per file)</small>
                                            </div>

                                            <div v-else class="text-start" @click.stop>
                                                <div v-for="(file, index) in documents" :key="index"
                                                    class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded bg-light">
                                                    <div>
                                                        <i class="bi bi-file-earmark-text me-2 text-success fs-5"></i>
                                                        <span class="fw-medium text-success">{{ file.name }}</span>
                                                        <small class="text-muted ms-2">
                                                            ({{ (file.size / 1024 / 1024).toFixed(2) }} MB)
                                                        </small>
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
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>

                    <!-- ═══════════════ PROJECT PAYMENTS CARD ═══════════════ -->
                    <div class="card card-primary card-outline mb-4">
                        <div class="card-header">
                            <div class="card-title">Project Payments</div>
                        </div>

                        <div class="card-body">
                            <div class="mb-3 d-flex justify-content-end">
                                <button type="button" class="btn btn-sm btn-success" @click="addRow">
                                    <i class="bi bi-plus-lg"></i> Add Row
                                </button>
                            </div>

                            <EasyDataTable :headers="columns" :items="payments" hide-footer border-cell alternating>
                                <!-- # Index -->
                                <template #item-index="row">
                                    {{ getRowIndex(row._id) }}
                                </template>

                                <!-- Amount -->
                                <template #item-amount="row">
                                    <input type="number" class="form-control form-control-sm"
                                        :value="getRow(row._id).amount"
                                        @input="getRow(row._id).amount = parseFloat($event.target.value) || ''"
                                        placeholder="Enter amount" min="0" step="0.01" style="min-width: 150px;" />
                                </template>

                                <!-- Amount Paid -->
                                <template #item-amount_paid="row">
                                    <div class="d-flex gap-2">
                                        <button type="button"
                                            :class="['btn btn-sm', getRow(row._id).amount_paid ? 'btn-success' : 'btn-outline-success']"
                                            @click="getRow(row._id).amount_paid = true">
                                            <i class="bi bi-check-circle-fill me-1"></i> Paid
                                        </button>
                                        <button type="button"
                                            :class="['btn btn-sm', !getRow(row._id).amount_paid ? 'btn-danger' : 'btn-outline-danger']"
                                            @click="getRow(row._id).amount_paid = false">
                                            <i class="bi bi-x-circle-fill me-1"></i> Unpaid
                                        </button>
                                    </div>
                                </template>

                                <!-- Amount Received -->
                                <template #item-amount_received="row">
                                    <div class="d-flex gap-2">
                                        <button type="button"
                                            :class="['btn btn-sm', getRow(row._id).amount_received ? 'btn-primary' : 'btn-outline-primary']"
                                            @click="getRow(row._id).amount_received = true">
                                            <i class="bi bi-check-circle-fill me-1"></i> Received
                                        </button>
                                        <button type="button"
                                            :class="['btn btn-sm', !getRow(row._id).amount_received ? 'btn-warning' : 'btn-outline-warning']"
                                            @click="getRow(row._id).amount_received = false">
                                            <i class="bi bi-x-circle-fill me-1"></i> Not Received
                                        </button>
                                    </div>
                                </template>

                                <!-- Actions -->
                                <template #item-actions="row">
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                        @click="deleteRow(row._id)">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </template>

                            </EasyDataTable>
                        </div>

                        <div class="card-footer">
                            <div class="row mb-3">

                                <!-- Total Amount -->
                                <div class="col-md-4">
                                    <div class="p-3 rounded border bg-light text-center">
                                        <small class="text-muted d-block mb-1">Total Amount</small>
                                        <span class="fw-semibold fs-5 text-dark">
                                            {{ liveTotalAmount.toFixed(2) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Total Paid -->
                                <div class="col-md-4">
                                    <div class="p-3 rounded border bg-light text-center">
                                        <small class="text-muted d-block mb-1">Total Paid</small>
                                        <span class="fw-semibold fs-5 text-success">
                                            {{ liveTotalAmountPaid.toFixed(2) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Total Received -->
                                <div class="col-md-4">
                                    <div class="p-3 rounded border bg-light text-center">
                                        <small class="text-muted d-block mb-1">Total Received</small>
                                        <span class="fw-semibold fs-5 text-primary">
                                            {{ liveTotalAmountReceived.toFixed(2) }}
                                        </span>
                                    </div>
                                </div>

                            </div>

                            <button type="button" class="btn btn-primary" @click="submitPayments">
                                Save Payments
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { useToast } from 'vue-toastification'
import EasyDataTable from 'vue3-easy-data-table'
import 'vue3-easy-data-table/dist/style.css'

export default {

    components: {
        EasyDataTable,
    },

    data() {
        return {
            // ── Project fields ──
            id: null,
            name: '',
            description: '',
            documents: [],
            oldDocuments: [],
            deletedDocumentIds: [],

            // ── Payment fields ──
            payments: [],
            rowCounter: 0,
            columns: [
                { text: '#',               value: 'index',           sortable: false },
                { text: 'Amount',          value: 'amount',          sortable: false },
                { text: 'Amount Paid',     value: 'amount_paid',     sortable: false },
                { text: 'Amount Received', value: 'amount_received', sortable: false },
                { text: 'Actions',         value: 'actions',         sortable: false },
            ],
        }
    },

    // ═══════════════ COMPUTED ═══════════════
    computed: {
        liveTotalAmount() {
            return this.payments.reduce((sum, p) => sum + (parseFloat(p.amount) || 0), 0)
        },
        liveTotalAmountPaid() {
            return this.payments
                .filter(p => p.amount_paid)
                .reduce((sum, p) => sum + (parseFloat(p.amount) || 0), 0)
        },
        liveTotalAmountReceived() {
            return this.payments
                .filter(p => p.amount_received)
                .reduce((sum, p) => sum + (parseFloat(p.amount) || 0), 0)
        },
    },

    mounted() {
        this.getProject()
    },

    methods: {

        // ═══════════════ HELPERS ═══════════════
        getRow(id) {
            return this.payments.find(p => p._id === id)
        },

        getRowIndex(id) {
            return this.payments.findIndex(p => p._id === id) + 1
        },

        // ═══════════════ LOAD PROJECT ═══════════════
        async getProject() {
            const toast = useToast()
            try {
                const id = this.$route.params.id
                const res = await this.$axios.get(`/project/${id}/edit`)
                const project = res.data.data.project

                this.id           = project.id
                this.name         = project.name
                this.description  = project.description
                this.oldDocuments = project.documents || []

                // ── Load existing payments into the table ──
                if (project.payments && project.payments.length > 0) {
                    project.payments.forEach(p => {
                        this.rowCounter++
                        this.payments.push({
                            _id:             this.rowCounter,
                            db_id:           p.id,
                            amount:          parseFloat(p.amount),
                            amount_paid:     p.amount_paid == 1 || p.amount_paid === true,
                            amount_received: p.amount_received == 1 || p.amount_received === true,
                        })
                    })
                }

            } catch (err) {
                toast.error('Failed to load project')
            }
        },

        // ═══════════════ FILE HANDLING ═══════════════
        triggerFileInput() {
            this.$refs.fileInput.click()
        },

        handleFileChange(e) {
            this.addFiles(Array.from(e.target.files))
        },

        handleDrop(e) {
            this.addFiles(Array.from(e.dataTransfer.files))
        },

        addFiles(files) {
            const toast = useToast()
            files.forEach(file => {
                const ext = file.name.split('.').pop().toLowerCase()
                if (!['pdf', 'doc', 'docx'].includes(ext)) {
                    toast.error(`Invalid file type: ${file.name}`)
                    return
                }
                if (file.size > 10240 * 1024) {
                    toast.error(`File too large: ${file.name}`)
                    return
                }
                this.documents.push(file)
            })
            if (this.$refs.fileInput) this.$refs.fileInput.value = null
        },

        removeFile(index) {
            this.documents.splice(index, 1)
        },

        removeOldDocument(id, index) {
            this.deletedDocumentIds.push(id)
            this.oldDocuments.splice(index, 1)
        },

        // ═══════════════ UPDATE PROJECT ═══════════════
        async UpdateProject() {
            const toast = useToast()

            if (!this.name || !this.description) {
                toast.error('Name and Description are required')
                return
            }

            if (this.oldDocuments.length === 0 && this.documents.length === 0) {
                toast.error('At least one document is required')
                return
            }

            try {
                const formData = new FormData()
                formData.append('name', this.name)
                formData.append('description', this.description)

                this.documents.forEach(file => formData.append('document[]', file))
                this.deletedDocumentIds.forEach(id => formData.append('deleted_document_ids[]', id))
                formData.append('_method', 'PUT')

                const res = await this.$axios.post(
                    `/project/${this.id}/update`,
                    formData,
                    { headers: { 'Content-Type': 'multipart/form-data' } }
                )

                toast.success(res.data.message)
                this.$router.push({ path: '/list-project' })

            } catch (err) {
                console.log(err.response)
                toast.error(err.response?.data?.message || 'Failed to update project')
            }
        },

        // ═══════════════ PAYMENT ROW MANAGEMENT ═══════════════
        addRow() {
            this.rowCounter++
            this.payments.push({
                _id:             this.rowCounter,
                db_id:           null,
                amount:          '',
                amount_paid:     false,
                amount_received: false,
            })
        },

        deleteRow(id) {
            const index = this.payments.findIndex(p => p._id === id)
            if (index !== -1) this.payments.splice(index, 1)
        },

        // ═══════════════ SUBMIT PAYMENTS ═══════════════
        async submitPayments() {
            const toast = useToast()

            if (!this.payments.length) {
                toast.error('Please add at least one payment row.')
                return
            }

            const hasInvalidAmount = this.payments.some(
                p => p.amount === '' || p.amount === null || isNaN(p.amount)
            )
            if (hasInvalidAmount) {
                toast.error('Please enter a valid amount for all rows.')
                return
            }

            try {
                const payload = {
                    project_id: this.id,
                    payments: this.payments.map(p => ({
                        id:              p.db_id || null,
                        amount:          p.amount,
                        amount_paid:     p.amount_paid,
                        amount_received: p.amount_received,
                    })),
                }

                const res = await this.$axios.put(`/project/${this.id}/payments`, payload)
                toast.success(res.data.message || 'Payments saved successfully.')

            } catch (err) {
                console.error(err)
                toast.error(err.response?.data?.message || 'Failed to save payments.')
            }
        },

    },
}
</script>