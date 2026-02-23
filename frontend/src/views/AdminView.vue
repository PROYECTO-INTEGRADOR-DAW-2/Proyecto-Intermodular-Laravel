<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import api from '@/services/api'
import RoleBadge from '@/components/RoleBadge.vue'

// ─── State ────────────────────────────────────────────────────────────────────
const activeTab = ref('products')
const isLoading = ref(false)
const toast = reactive({ show: false, message: '', type: 'success' })

// Products
const products = ref([])
const productSearch = ref('')
const showProductModal = ref(false)
const editingProduct = ref(null)
const productForm = reactive({
    sku: '', marca: '', categoria: '', nombre: '', precio: '',
    talla: '', color: '', stock: '', ajuste: '', sexo: '',
    descripcion: '', altura: '', deporte: '', oferta: false, img: '',
    secondary_images: []
})

// Orders
const orders = ref([])

// Users
const users = ref([])
const allRoles = ref([])
const selectedUser = ref(null)
const selectedRole = ref('')

// ─── Helpers ──────────────────────────────────────────────────────────────────
const showToast = (message, type = 'success') => {
    toast.message = message
    toast.type = type
    toast.show = true
    setTimeout(() => toast.show = false, 3500)
}

// ─── Products ─────────────────────────────────────────────────────────────────
const fileInput = ref(null)

const triggerExcelUpload = () => {
    if (fileInput.value) fileInput.value.click()
}

const handleExcelUpload = async (event) => {
    const file = event.target.files[0]
    if (!file) return

    const formData = new FormData()
    formData.append('file', file)

    isLoading.value = true
    try {
        const response = await api.post('/admin/products/import', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        })
        showToast(response.data.message || 'Importación exitosa', 'success')
        await fetchProducts()
    } catch (error) {
        showToast(error.response?.data?.message || 'Error al importar archivo', 'danger')
    } finally {
        event.target.value = ''
        isLoading.value = false
    }
}

const fetchProducts = async () => {
    isLoading.value = true
    try {
        const res = await api.get('/products', { params: { per_page: 100 } })
        products.value = res.data.data
    } catch (e) { showToast('Error cargando productos', 'danger') }
    finally { isLoading.value = false }
}

const filteredProducts = computed(() => {
    if (!productSearch.value) return products.value
    const q = productSearch.value.toLowerCase()
    return products.value.filter(p =>
        p.nombre?.toLowerCase().includes(q) ||
        p.marca?.toLowerCase().includes(q) ||
        p.sku?.toLowerCase().includes(q)
    )
})

const openCreateProduct = () => {
    editingProduct.value = null
    Object.assign(productForm, {
        sku: '', marca: '', categoria: '', nombre: '', precio: '',
        talla: '', color: '', stock: '', ajuste: '', sexo: '',
        descripcion: '', altura: '', deporte: '', oferta: false, img: '',
        secondary_images: []
    })
    showProductModal.value = true
}

const openEditProduct = async (product) => {
    try {
        const res = await api.get(`/products/${product.id}`)
        const fullProduct = res.data.data
        editingProduct.value = fullProduct
        Object.assign(productForm, { 
            ...fullProduct,
            secondary_images: fullProduct.images ? fullProduct.images.map(img => img.image_url) : []
        })
        showProductModal.value = true
    } catch (e) {
        showToast('Error cargando detalles del producto', 'danger')
    }
}

const saveProduct = async () => {
    try {
        const payload = { ...productForm }
        // Filter empty secondary images
        if (payload.secondary_images) {
            payload.secondary_images = payload.secondary_images.filter(img => img && img.trim() !== '')
        }

        if (editingProduct.value) {
            await api.put(`/admin/products/${editingProduct.value.id}`, payload)
            showToast('Producto actualizado correctamente')
        } else {
            await api.post('/admin/products', payload)
            showToast('Producto creado correctamente')
        }
        showProductModal.value = false
        await fetchProducts()
    } catch (e) {
        showToast(e.response?.data?.message || 'Error guardando producto', 'danger')
    }
}

const deleteProduct = async (product) => {
    if (!confirm(`¿Eliminar "${product.nombre}"?`)) return
    try {
        await api.delete(`/admin/products/${product.id}`)
        showToast('Producto eliminado')
        await fetchProducts()
    } catch (e) { showToast('Error eliminando producto', 'danger') }
}

// ─── Orders ───────────────────────────────────────────────────────────────────
const fetchOrders = async () => {
    isLoading.value = true
    try {
        const res = await api.get('/admin/orders')
        orders.value = res.data.data
    } catch (e) { showToast('Error cargando pedidos', 'danger') }
    finally { isLoading.value = false }
}

const statusOptions = ['pending', 'processing', 'shipped', 'delivered', 'cancelled']
const statusLabels = {
    pending: 'Pendiente', processing: 'En proceso',
    shipped: 'Enviado', delivered: 'Entregado', cancelled: 'Cancelado'
}
const statusColors = {
    pending: 'warning', processing: 'info',
    shipped: 'primary', delivered: 'success', cancelled: 'danger'
}

const updateOrderStatus = async (order, status) => {
    try {
        await api.patch(`/admin/orders/${order.id}/status`, { status })
        order.status = status
        showToast('Estado del pedido actualizado')
    } catch (e) { showToast('Error actualizando estado', 'danger') }
}

// ─── Users / Roles ────────────────────────────────────────────────────────────
const fetchUsers = async () => {
    isLoading.value = true
    try {
        const [usersRes, rolesRes] = await Promise.all([
            api.get('/admin/users'),
            api.get('/admin/roles'),
        ])
        users.value = usersRes.data.data
        allRoles.value = rolesRes.data.data
    } catch (e) { showToast('Error cargando usuarios', 'danger') }
    finally { isLoading.value = false }
}

const assignRole = async () => {
    if (!selectedUser.value || !selectedRole.value) return
    try {
        await api.post(`/admin/users/${selectedUser.value.id}/roles`, { role: selectedRole.value })
        showToast(`Rol '${selectedRole.value}' asignado a ${selectedUser.value.name}`)
        selectedRole.value = ''
        await fetchUsers()
    } catch (e) { showToast('Error asignando rol', 'danger') }
}

const removeRole = async (user, roleName) => {
    if (!confirm(`¿Eliminar el rol '${roleName}' de ${user.name}?`)) return
    try {
        await api.delete(`/admin/users/${user.id}/roles/${roleName}`)
        showToast(`Rol '${roleName}' eliminado`)
        await fetchUsers()
    } catch (e) { showToast('Error eliminando rol', 'danger') }
}

// ─── Stats ────────────────────────────────────────────────────────────────────
const stats = computed(() => ({
    totalProducts: products.value.length,
    totalOrders: orders.value.length,
    totalUsers: users.value.length,
    revenue: orders.value.reduce((sum, o) => sum + parseFloat(o.total || 0), 0).toFixed(2),
}))

// ─── Tab switching ────────────────────────────────────────────────────────────
const switchTab = async (tab) => {
    activeTab.value = tab
    if (tab === 'products' && products.value.length === 0) await fetchProducts()
    if (tab === 'orders' && orders.value.length === 0) await fetchOrders()
    if (tab === 'users' && users.value.length === 0) await fetchUsers()
}

onMounted(async () => {
    await Promise.all([fetchProducts(), fetchOrders(), fetchUsers()])
})
</script>

<template>
    <div class="admin-panel">
        <!-- Header -->
        <div class="admin-header">
            <div class="container-fluid py-4 px-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="admin-icon">
                            <i class="bi bi-shield-lock-fill"></i>
                        </div>
                        <div>
                            <h1 class="mb-0 fw-bold text-white fs-3">Panel de Administración</h1>
                            <p class="mb-0 text-white-50 small">J&A Sports — Control total</p>
                        </div>
                    </div>
                    <router-link to="/" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>Volver a la tienda
                    </router-link>
                </div>
            </div>
        </div>

        <div class="container-fluid px-4 py-4">
            <!-- Stats Cards -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="stat-card stat-blue">
                        <div class="stat-icon"><i class="bi bi-box-seam"></i></div>
                        <div class="stat-value">{{ stats.totalProducts }}</div>
                        <div class="stat-label">Productos</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card stat-green">
                        <div class="stat-icon"><i class="bi bi-bag-check"></i></div>
                        <div class="stat-value">{{ stats.totalOrders }}</div>
                        <div class="stat-label">Pedidos</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card stat-purple">
                        <div class="stat-icon"><i class="bi bi-people"></i></div>
                        <div class="stat-value">{{ stats.totalUsers }}</div>
                        <div class="stat-label">Usuarios</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card stat-orange">
                        <div class="stat-icon"><i class="bi bi-currency-euro"></i></div>
                        <div class="stat-value">{{ stats.revenue }}€</div>
                        <div class="stat-label">Ingresos totales</div>
                    </div>
                </div>
            </div>

            <!-- Toast -->
            <div v-if="toast.show" :class="`alert alert-${toast.type} alert-dismissible d-flex align-items-center`" role="alert">
                <i :class="`bi bi-${toast.type === 'success' ? 'check-circle' : 'exclamation-triangle'}-fill me-2`"></i>
                {{ toast.message }}
                <button type="button" class="btn-close ms-auto" @click="toast.show = false"></button>
            </div>

            <!-- Tabs -->
            <div class="admin-tabs mb-4">
                <button @click="switchTab('products')" :class="['tab-btn', { active: activeTab === 'products' }]">
                    <i class="bi bi-box-seam me-2"></i>Productos
                </button>
                <button @click="switchTab('orders')" :class="['tab-btn', { active: activeTab === 'orders' }]">
                    <i class="bi bi-bag me-2"></i>Pedidos
                </button>
                <button @click="switchTab('users')" :class="['tab-btn', { active: activeTab === 'users' }]">
                    <i class="bi bi-people me-2"></i>Usuarios & Roles
                </button>
            </div>

            <!-- Loading -->
            <div v-if="isLoading" class="text-center py-5">
                <div class="spinner-border text-danger" role="status"></div>
            </div>

            <!-- ═══════════════════════════════════════════════════════════════ -->
            <!-- TAB: PRODUCTS -->
            <!-- ═══════════════════════════════════════════════════════════════ -->
            <div v-if="!isLoading && activeTab === 'products'">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="input-group" style="max-width: 350px;">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input v-model="productSearch" type="text" class="form-control" placeholder="Buscar producto...">
                    </div>
                    <div class="d-flex gap-2">
                        <input type="file" ref="fileInput" @change="handleExcelUpload" accept=".csv, .xlsx, .xls" class="d-none">
                        <button @click="triggerExcelUpload" class="btn btn-success shadow-sm">
                            <i class="bi bi-file-earmark-excel me-1"></i>Subir Excel
                        </button>
                        <button @click="openCreateProduct" class="btn btn-danger shadow-sm">
                            <i class="bi bi-plus-lg me-1"></i>Nuevo Producto
                        </button>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th class="ps-3">IMG</th>
                                    <th>SKU</th>
                                    <th>Nombre</th>
                                    <th>Marca</th>
                                    <th>Categoría</th>
                                    <th>Precio</th>
                                    <th>Stock</th>
                                    <th>Oferta</th>
                                    <th class="text-center pe-3">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="product in filteredProducts" :key="product.id">
                                    <td class="ps-3">
                                        <img :src="product.img || product.image_url || '/img/placeholder.png'"
                                            :alt="product.nombre" width="48" height="48"
                                            class="rounded object-fit-cover" style="object-fit:cover;">
                                    </td>
                                    <td><code class="text-muted small">{{ product.sku }}</code></td>
                                    <td class="fw-bold" style="max-width:180px;">{{ product.nombre }}</td>
                                    <td>{{ product.marca }}</td>
                                    <td><span class="badge bg-secondary">{{ product.categoria }}</span></td>
                                    <td class="fw-bold text-success">{{ product.precio }}€</td>
                                    <td>
                                        <span :class="['badge', product.stock > 10 ? 'bg-success' : product.stock > 0 ? 'bg-warning text-dark' : 'bg-danger']">
                                            {{ product.stock }}
                                        </span>
                                    </td>
                                    <td>
                                        <i :class="['bi', product.oferta ? 'bi-tag-fill text-danger' : 'bi-tag text-muted']"></i>
                                    </td>
                                    <td class="text-center pe-3">
                                        <button @click="openEditProduct(product)" class="btn btn-outline-primary btn-sm me-1">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button @click="deleteProduct(product)" class="btn btn-outline-danger btn-sm">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="filteredProducts.length === 0">
                                    <td colspan="9" class="text-center text-muted py-4">No se encontraron productos</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════════════════════ -->
            <!-- TAB: ORDERS -->
            <!-- ═══════════════════════════════════════════════════════════════ -->
            <div v-if="!isLoading && activeTab === 'orders'">
                <div class="card border-0 shadow-sm">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th class="ps-3">#</th>
                                    <th>Cliente</th>
                                    <th>Email</th>
                                    <th>Ciudad</th>
                                    <th>Artículos</th>
                                    <th>Total</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th class="pe-3">Cambiar estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="order in orders" :key="order.id">
                                    <td class="ps-3 fw-bold">#{{ order.id }}</td>
                                    <td>{{ order.user }}</td>
                                    <td class="text-muted small">{{ order.email }}</td>
                                    <td>{{ order.city }}</td>
                                    <td><span class="badge bg-secondary">{{ order.items_count }}</span></td>
                                    <td class="fw-bold text-success">{{ parseFloat(order.total).toFixed(2) }}€</td>
                                    <td class="text-muted small">{{ new Date(order.created_at).toLocaleDateString('es-ES') }}</td>
                                    <td>
                                        <span :class="`badge bg-${statusColors[order.status]}`">
                                            {{ statusLabels[order.status] || order.status }}
                                        </span>
                                    </td>
                                    <td class="pe-3">
                                        <select class="form-select form-select-sm" style="min-width:130px;"
                                            :value="order.status" @change="updateOrderStatus(order, $event.target.value)">
                                            <option v-for="s in statusOptions" :key="s" :value="s">
                                                {{ statusLabels[s] }}
                                            </option>
                                        </select>
                                    </td>
                                </tr>
                                <tr v-if="orders.length === 0">
                                    <td colspan="9" class="text-center text-muted py-4">No hay pedidos</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════════════════════ -->
            <!-- TAB: USERS & ROLES -->
            <!-- ═══════════════════════════════════════════════════════════════ -->
            <div v-if="!isLoading && activeTab === 'users'">
                <!-- Assign role form -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-dark text-white fw-bold">
                        <i class="bi bi-person-plus-fill me-2"></i>Asignar Rol
                    </div>
                    <div class="card-body">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-5">
                                <label class="form-label fw-bold">Usuario</label>
                                <select v-model="selectedUser" class="form-select">
                                    <option :value="null" disabled>Selecciona un usuario...</option>
                                    <option v-for="u in users" :key="u.id" :value="u">{{ u.name }} ({{ u.email }})</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Rol</label>
                                <select v-model="selectedRole" class="form-select">
                                    <option value="" disabled>Selecciona un rol...</option>
                                    <option v-for="r in allRoles" :key="r.id" :value="r.name">{{ r.name }} — {{ r.description }}</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button @click="assignRole" class="btn btn-danger w-100" :disabled="!selectedUser || !selectedRole">
                                    <i class="bi bi-plus-circle me-1"></i>Asignar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Users table -->
                <div class="card border-0 shadow-sm">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th class="ps-3">ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Rol directo</th>
                                    <th class="pe-3">Roles asignados</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="user in users" :key="user.id">
                                    <td class="ps-3 text-muted">#{{ user.id }}</td>
                                    <td class="fw-bold">{{ user.name }}</td>
                                    <td class="text-muted">{{ user.email }}</td>
                                    <td><RoleBadge :role="user.role || 'user'" /></td>
                                    <td class="pe-3">
                                        <span v-if="user.roles.length === 0" class="text-muted fst-italic small">Sin roles</span>
                                        <span v-for="role in user.roles" :key="role" class="me-1 d-inline-flex align-items-center gap-1">
                                            <RoleBadge :role="role" />
                                            <button @click="removeRole(user, role)" class="btn btn-link btn-sm text-danger p-0" title="Eliminar">
                                                <i class="bi bi-x-circle-fill"></i>
                                            </button>
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <!-- PRODUCT MODAL -->
        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <div v-if="showProductModal" class="modal-backdrop-custom" @click.self="showProductModal = false">
            <div class="modal-dialog-custom">
                <div class="card border-0 shadow-lg">
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold">
                            <i class="bi bi-box-seam me-2"></i>
                            {{ editingProduct ? 'Editar Producto' : 'Nuevo Producto' }}
                        </span>
                        <button @click="showProductModal = false" class="btn-close btn-close-white"></button>
                    </div>
                    <div class="card-body" style="max-height:70vh; overflow-y:auto;">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">SKU</label>
                                <input v-model="productForm.sku" type="text" class="form-control" placeholder="SKU-001">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nombre *</label>
                                <input v-model="productForm.nombre" type="text" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Marca</label>
                                <input v-model="productForm.marca" type="text" class="form-control" placeholder="Nike, Adidas...">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Categoría</label>
                                <input v-model="productForm.categoria" type="text" class="form-control" placeholder="Zapatillas, Camisetas...">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Precio (€) *</label>
                                <input v-model="productForm.precio" type="number" step="0.01" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Stock</label>
                                <input v-model="productForm.stock" type="number" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Sexo</label>
                                <select v-model="productForm.sexo" class="form-select">
                                    <option value="">Seleccionar...</option>
                                    <option>Hombre</option>
                                    <option>Mujer</option>
                                    <option>Unisex</option>
                                    <option>Niños</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Talla</label>
                                <input v-model="productForm.talla" type="text" class="form-control" placeholder="M, L, 42...">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Color</label>
                                <input v-model="productForm.color" type="text" class="form-control" placeholder="Rojo, Azul...">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Deporte</label>
                                <input v-model="productForm.deporte" type="text" class="form-control" placeholder="Fútbol, Running...">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Ajuste</label>
                                <input v-model="productForm.ajuste" type="text" class="form-control" placeholder="Regular, Slim...">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Altura</label>
                                <input v-model="productForm.altura" type="text" class="form-control" placeholder="Alta, Media...">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">URL Imagen</label>
                                <input v-model="productForm.img" type="text" class="form-control" placeholder="https://...">
                            </div>

                            <!-- Secondary Images -->
                            <div class="col-12">
                                <label class="form-label fw-bold">Imágenes Secundarias</label>
                                <div v-for="(img, index) in productForm.secondary_images" :key="index" class="d-flex mb-2 gap-2">
                                    <input v-model="productForm.secondary_images[index]" type="text" class="form-control" placeholder="URL o nombre de archivo (ej: foto.png)">
                                    <button @click="productForm.secondary_images.splice(index, 1)" class="btn btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                <button @click="productForm.secondary_images.push('')" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-plus text-lg"></i> Añadir imagen
                                </button>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Descripción</label>
                                <textarea v-model="productForm.descripcion" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input v-model="productForm.oferta" class="form-check-input" type="checkbox" id="ofertaCheck">
                                    <label class="form-check-label fw-bold" for="ofertaCheck">
                                        <i class="bi bi-tag-fill text-danger me-1"></i>En oferta
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end gap-2">
                        <button @click="showProductModal = false" class="btn btn-outline-secondary">Cancelar</button>
                        <button @click="saveProduct" class="btn btn-danger">
                            <i class="bi bi-check-lg me-1"></i>
                            {{ editingProduct ? 'Guardar cambios' : 'Crear producto' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.admin-panel {
    min-height: 100vh;
    background: #f4f6f9;
}

.admin-header {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    border-bottom: 3px solid #dc3545;
}

.admin-icon {
    width: 50px;
    height: 50px;
    background: rgba(220, 53, 69, 0.2);
    border: 2px solid #dc3545;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
    color: #dc3545;
}

/* Stats */
.stat-card {
    border-radius: 16px;
    padding: 1.25rem;
    color: white;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
.stat-card::before {
    content: '';
    position: absolute;
    top: -20px; right: -20px;
    width: 80px; height: 80px;
    border-radius: 50%;
    background: rgba(255,255,255,0.1);
}
.stat-blue   { background: linear-gradient(135deg, #0d6efd, #0a58ca); }
.stat-green  { background: linear-gradient(135deg, #198754, #146c43); }
.stat-purple { background: linear-gradient(135deg, #6f42c1, #59359a); }
.stat-orange { background: linear-gradient(135deg, #fd7e14, #e96b02); }
.stat-icon   { font-size: 1.5rem; margin-bottom: 0.5rem; opacity: 0.9; }
.stat-value  { font-size: 1.8rem; font-weight: 800; line-height: 1; }
.stat-label  { font-size: 0.8rem; opacity: 0.85; margin-top: 0.25rem; }

/* Tabs */
.admin-tabs {
    display: flex;
    gap: 0.5rem;
    border-bottom: 2px solid #dee2e6;
    padding-bottom: 0;
}
.tab-btn {
    padding: 0.6rem 1.4rem;
    border: none;
    background: none;
    color: #6c757d;
    font-weight: 600;
    border-bottom: 3px solid transparent;
    margin-bottom: -2px;
    cursor: pointer;
    transition: all 0.2s;
    border-radius: 6px 6px 0 0;
}
.tab-btn:hover { color: #dc3545; background: rgba(220,53,69,0.05); }
.tab-btn.active { color: #dc3545; border-bottom-color: #dc3545; background: rgba(220,53,69,0.05); }

/* Modal */
.modal-backdrop-custom {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.6);
    z-index: 1050;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}
.modal-dialog-custom {
    width: 100%;
    max-width: 700px;
    max-height: 90vh;
}
</style>
