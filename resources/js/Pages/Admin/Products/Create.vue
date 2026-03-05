<template>
  <AdminLayout title="Add New Product" subtitle="Fill in the product details below">
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
          <div class="md:col-span-2">
            <label class="label">Description</label>
            <textarea v-model="form.description" rows="3" class="input resize-none"
              placeholder="Product description..."></textarea>
          </div>
          <div>
            <label class="label">Thumbnail Image</label>
            <input @change="e => form.thumbnail = e.target.files[0]" type="file" accept="image/*"
              class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100" />
          </div>
          <div>
            <label class="label">Additional Images</label>
            <input @change="e => form.images = Array.from(e.target.files)" type="file" accept="image/*" multiple
              class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100" />
          </div>
          <div class="flex items-center gap-6">
            <label class="flex items-center gap-2 cursor-pointer">
              <input v-model="form.is_featured" type="checkbox" class="rounded text-orange-500 focus:ring-orange-500 w-4 h-4" />
              <span class="text-sm font-medium text-gray-700">Mark as Featured</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
              <input v-model="form.is_active" type="checkbox" class="rounded text-orange-500 focus:ring-orange-500 w-4 h-4" />
              <span class="text-sm font-medium text-gray-700">Active</span>
            </label>
          </div>
        </div>
        <div class="flex gap-4 pt-2">
          <button type="submit" :disabled="submitting"
            class="bg-orange-500 hover:bg-orange-600 text-white px-8 py-3 rounded-xl font-bold transition disabled:opacity-70">
            {{ submitting ? 'Creating...' : 'Create Product' }}
          </button>
          <Link href="/admin/products" class="border border-gray-200 text-gray-600 px-6 py-3 rounded-xl font-medium hover:bg-gray-50 transition text-sm">
            Cancel
          </Link>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({ categories: Array })
const submitting = ref(false)

const form = ref({
  name: '', category_id: '', description: '', price: '', mrp: '',
  unit: 'kg', stock: 0, sku: '', is_featured: false, is_active: true,
  thumbnail: null, images: [],
})

function submit() {
  submitting.value = true
  const data = new FormData()
  Object.entries(form.value).forEach(([key, val]) => {
    if (key === 'images') {
      val.forEach(img => data.append('images[]', img))
    } else if (val !== null && val !== undefined) {
      data.append(key, val)
    }
  })
  router.post('/admin/products', data, {
    onFinish: () => { submitting.value = false }
  })
}
</script>

<style scoped>
@reference "../../../../css/app.css";
.label { @apply block text-sm font-semibold text-gray-700 mb-1.5; }
.input { @apply w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 outline-none; }
</style>
