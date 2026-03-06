<template>
  <AdminLayout title="Products" subtitle="Manage your product catalog">
    <!-- Actions Bar -->
    <div class="flex flex-col sm:flex-row gap-4 justify-between mb-6">
      <div class="flex gap-3 flex-wrap">
        <input v-model="searchQuery" @input="doSearch" type="text" placeholder="🔍 Search products..."
          class="border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 outline-none w-64 transition-colors bg-white border-gray-200 text-gray-900" />
      </div>
      <Link href="/admin/products/create"
        class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition flex items-center gap-2 self-start">
        + Add Product
      </Link>
    </div>

    <!-- Table -->
    <div class="rounded-2xl shadow-sm border overflow-hidden bg-white border-gray-100">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="border-b bg-gray-50 border-gray-100">
            <tr>
              <th class="text-left py-3 px-4 font-semibold text-gray-600">Product</th>
              <th class="text-left py-3 px-4 font-semibold text-gray-600">Category</th>
              <th class="text-left py-3 px-4 font-semibold text-gray-600">Price</th>
              <th class="text-left py-3 px-4 font-semibold text-gray-600">Stock</th>
              <th class="text-left py-3 px-4 font-semibold text-gray-600">Status</th>
              <th class="py-3 px-4"></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="product in products.data" :key="product.id"
              class="border-b transition border-gray-50 hover:bg-gray-50">
              <td class="py-3 px-4">
                <div class="flex items-center gap-3">
                  <img :src="product.thumbnail" :alt="product.name"
                    class="w-12 h-12 object-cover rounded-lg bg-gray-100 flex-shrink-0" />
                  <div>
                    <div class="font-semibold text-gray-800">{{ product.name }}</div>
                    <div v-if="product.is_featured" class="text-xs font-medium text-amber-600">⭐ Featured</div>
                  </div>
                </div>
              </td>
              <td class="py-3 px-4 text-gray-500">{{ product.category }}</td>
              <td class="py-3 px-4 font-bold text-gray-900">₹{{ product.price }}</td>
              <td class="py-3 px-4">
                <span :class="[product.stock > 0 ? 'text-green-600' : 'text-red-500', 'font-medium']">
                  {{ product.stock }}
                </span>
              </td>
              <td class="py-3 px-4">
                <span :class="[product.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500', 'px-2 py-1 rounded-full text-xs font-semibold']">
                  {{ product.is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td class="py-3 px-4">
                <div class="flex items-center gap-2">
                  <Link :href="'/admin/products/' + product.id + '/edit'"
                    class="text-blue-500 hover:text-blue-600 text-xs font-medium">Edit</Link>
                  <button @click="confirmDelete(product)"
                    class="text-red-400 hover:text-red-500 text-xs font-medium">Delete</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <!-- Pagination -->
      <div v-if="products.last_page > 1" class="flex justify-center gap-2 p-4 border-t border-gray-100">
        <Link v-for="link in products.links" :key="link.label" :href="link.url || '#'" v-html="link.label"
          class="px-3 py-1.5 rounded-lg text-sm border transition"
          :class="link.active 
            ? 'bg-orange-500 text-white border-orange-500' 
            : (isDark ? 'border-slate-600 text-slate-400 hover:bg-slate-700' : 'border-gray-200 text-gray-600 hover:bg-gray-50')" />
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({ products: Object, categories: Array, filters: Object })
const searchQuery = ref(props.filters?.search || '')

let timer = null
function doSearch() {
  clearTimeout(timer)
  timer = setTimeout(() => router.get('/admin/products', { search: searchQuery.value }, { preserveState: true, preserveScroll: true, replace: true }), 400)
}

function confirmDelete(product) {
  if (confirm(`Delete "${product.name}"? This cannot be undone.`)) {
    router.delete('/admin/products/' + product.id)
  }
}
</script>
