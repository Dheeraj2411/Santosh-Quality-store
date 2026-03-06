<template>
  <AdminLayout title="Edit Product" subtitle="Update the product details below">
    <div class="max-w-3xl bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
      <form @submit.prevent="submit" enctype="multipart/form-data" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="md:col-span-2">
            <label class="label">Product Name *</label>
            <input v-model="form.name" type="text" required class="input" placeholder="e.g. Basmati Rice (Premium)" />
          </div>
          <div>
            <label class="label">Category *</label>
            <select v-model="form.category_id" required class="input">
              <option value="">Select Category</option>
              <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
            </select>
          </div>
          <div>
            <label class="label">Unit *</label>
            <select v-model="form.unit" class="input">
              <option>kg</option><option>g</option><option>500g</option><option>litre</option><option>2L</option>
              <option>pack</option><option>piece</option><option>50g</option><option>10kg</option><option>5kg</option>
            </select>
          </div>
          <div>
            <label class="label">Price (₹) *</label>
            <input v-model="form.price" type="number" step="0.01" min="0" required class="input" placeholder="0.00" />
          </div>
          <div>
            <label class="label">MRP (₹)</label>
            <input v-model="form.mrp" type="number" step="0.01" min="0" class="input" placeholder="0.00" />
          </div>
          <div>
            <label class="label">Stock Quantity *</label>
            <input v-model="form.stock" type="number" min="0" required class="input" placeholder="0" />
          </div>
          <div>
            <label class="label">SKU</label>
            <input v-model="form.sku" type="text" class="input" placeholder="Optional unique code" />
          </div>
          <div>
            <label class="label">Barcode</label>
            <div class="flex gap-2">
              <input v-model="form.barcode" type="text" class="input flex-1" placeholder="Type or scan..." />
              <button type="button" @click="showScanner = true" class="bg-gray-100 hover:bg-gray-200 border border-gray-200 text-gray-700 px-4 rounded-xl flex items-center justify-center font-bold px-3 transition" title="Scan Barcode">
                📷 Scan
              </button>
            </div>
          </div>
          <div class="md:col-span-2">
            <label class="label">Description</label>
            <textarea v-model="form.description" rows="3" class="input resize-none"
              placeholder="Product description..."></textarea>
          </div>
          <div>
            <label class="label">Thumbnail Image</label>
            <div v-if="product.thumbnail_url" class="mb-3">
              <img :src="product.thumbnail_url" class="w-24 h-24 object-cover rounded-xl border border-gray-200 shadow-sm" />
            </div>
            <input @change="e => form.thumbnail = e.target.files[0]" type="file" accept="image/*"
              class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100" />
          </div>
          <div>
            <label class="label">Additional Images</label>
            <div v-if="product.images && product.images.length" class="flex gap-3 mb-3 flex-wrap">
              <div v-for="img in product.images" :key="img.id" class="relative group">
                <img :src="img.url" class="w-16 h-16 object-cover rounded-lg border border-gray-200" />
                <button type="button" @click.prevent="deleteImage(img.id)"
                  class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center opacity-0 group-hover:opacity-100 transition shadow cursor-pointer text-xs font-bold hover:bg-red-600">
                  ×
                </button>
              </div>
            </div>
            <input @change="e => form.new_images = Array.from(e.target.files)" type="file" accept="image/*" multiple
              class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100" />
          </div>
          <div class="flex items-center gap-6">
            <label class="flex items-center gap-2 cursor-pointer">
              <input v-model="form.is_featured" type="checkbox" :checked="form.is_featured" class="rounded text-orange-500 focus:ring-orange-500 w-4 h-4" />
              <span class="text-sm font-medium text-gray-700">Mark as Featured</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
              <input v-model="form.is_active" type="checkbox" :checked="form.is_active" class="rounded text-orange-500 focus:ring-orange-500 w-4 h-4" />
              <span class="text-sm font-medium text-gray-700">Active</span>
            </label>
          </div>
        </div>
        <div class="flex gap-4 pt-4 border-t border-gray-100">
          <button type="submit" :disabled="submitting"
            class="bg-orange-500 hover:bg-orange-600 text-white px-8 py-3 rounded-xl font-bold transition disabled:opacity-70">
            {{ submitting ? 'Updating...' : 'Update Product' }}
          </button>
          <Link href="/admin/products" class="border border-gray-200 text-gray-600 px-6 py-3 rounded-xl font-medium hover:bg-gray-50 transition text-sm flex items-center">
            Cancel
          </Link>
        </div>
      </form>
    </div>

    <!-- Barcode Scanner Modal -->
    <BarcodeScannerModal :show="showScanner" @close="showScanner = false" @scan="handleScan" />
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import BarcodeScannerModal from '@/Components/BarcodeScannerModal.vue'

const props = defineProps({ product: Object, categories: Array })
const submitting = ref(false)
const showScanner = ref(false)

const form = ref({
  name: props.product.name,
  category_id: props.product.category_id,
  description: props.product.description || '',
  price: props.product.price,
  mrp: props.product.mrp || '',
  unit: props.product.unit,
  stock: props.product.stock,
  sku: props.product.sku || '',
  barcode: props.product.barcode || '',
  is_featured: Boolean(props.product.is_featured),
  is_active: Boolean(props.product.is_active),
  thumbnail: null,
  new_images: [],
})

function handleScan(decodedText) {
  form.value.barcode = decodedText
  showScanner.value = false
}

function submit() {
  submitting.value = true
  const data = new FormData()
  
  // Laravel requires _method=PUT to handle multipart forms correctly as a PUT request
  data.append('_method', 'PUT')

  Object.entries(form.value).forEach(([key, val]) => {
    if (key === 'new_images') {
      val.forEach(img => data.append('images[]', img))
    } else if (val !== null && val !== undefined && key !== 'thumbnail') {
      data.append(key, val)
    } else if (key === 'thumbnail' && val !== null) {
      data.append('thumbnail', val)
    }
  })
  
  router.post(`/admin/products/${props.product.id}`, data, {
    onFinish: () => { submitting.value = false },
    forceFormData: true
  })
}

function deleteImage(id) {
  if (confirm('Are you sure you want to delete this image?')) {
    router.delete(`/admin/products/images/${id}`, {
      preserveScroll: true
    })
  }
}
</script>

<style scoped>
@reference "../../../../css/app.css";
.label { @apply block text-sm font-semibold text-gray-700 mb-1.5; }
.input { @apply w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 outline-none; }
</style>
