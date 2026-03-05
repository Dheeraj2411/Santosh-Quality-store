<template>
  <AdminLayout title="Customers" subtitle="Manage your registered customers">
    <div class="flex flex-col sm:flex-row gap-4 justify-between mb-6">
      <div class="flex gap-3 flex-wrap">
        <input v-model="searchQuery" @input="doSearch" type="text" placeholder="🔍 Search customers..."
          class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 outline-none w-64" />
      </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
              <th class="text-left py-3 px-4 font-semibold text-gray-600">Customer</th>
              <th class="text-left py-3 px-4 font-semibold text-gray-600">Contact</th>
              <th class="text-left py-3 px-4 font-semibold text-gray-600">Orders</th>
              <th class="text-left py-3 px-4 font-semibold text-gray-600">Joined</th>
              <th class="text-left py-3 px-4 font-semibold text-gray-600">Status</th>
              <th class="py-3 px-4"></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="customer in customers.data" :key="customer.id"
              class="border-b border-gray-50 hover:bg-gray-50">
              <td class="py-3 px-4">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center font-bold text-lg">
                    {{ customer.name[0]?.toUpperCase() }}
                  </div>
                  <div>
                    <div class="font-semibold text-gray-800">{{ customer.name }}</div>
                  </div>
                </div>
              </td>
              <td class="py-3 px-4">
                <div class="text-gray-900">{{ customer.email }}</div>
                <div class="text-xs text-gray-500">{{ customer.phone || 'No phone' }}</div>
              </td>
              <td class="py-3 px-4 text-gray-600 font-medium pb-4">
                <span class="bg-gray-100 px-2 py-1 rounded inline-block mt-1">{{ customer.orders_count }} orders</span>
              </td>
              <td class="py-3 px-4 text-gray-500">{{ customer.created_at }}</td>
              <td class="py-3 px-4">
                <span :class="customer.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                  class="px-2 py-1 rounded-full text-xs font-semibold">
                  {{ customer.is_active ? 'Active' : 'Banned' }}
                </span>
              </td>
              <td class="py-3 px-4 text-right">
                <button @click="toggleStatus(customer)"
                  :class="customer.is_active ? 'text-red-500 hover:text-red-600' : 'text-green-500 hover:text-green-600'"
                  class="text-xs font-bold transition">
                  {{ customer.is_active ? 'Disable' : 'Enable' }}
                </button>
              </td>
            </tr>
            <tr v-if="customers.data.length === 0">
              <td colspan="6" class="py-12 text-center text-gray-500">No customers found.</td>
            </tr>
          </tbody>
        </table>
      </div>
      <!-- Pagination -->
      <div v-if="customers.last_page > 1" class="flex justify-center gap-2 p-4 border-t border-gray-100">
        <Link v-for="link in customers.links" :key="link.label" :href="link.url || '#'" v-html="link.label"
          class="px-3 py-1.5 rounded-lg text-sm border transition"
          :class="link.active ? 'bg-orange-500 text-white border-orange-500' : 'border-gray-200 text-gray-600'" />
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({ customers: Object, filters: Object })
const searchQuery = ref(props.filters?.search || '')

let timer = null
function doSearch() {
  clearTimeout(timer)
  timer = setTimeout(() => {
    router.get('/admin/customers', { search: searchQuery.value }, { preserveState: true, replace: true })
  }, 400)
}

function toggleStatus(customer) {
  const action = customer.is_active ? 'disable' : 'enable'
  if (confirm(`Are you sure you want to ${action} ${customer.name}?`)) {
    router.post(`/admin/customers/${customer.id}/toggle`, {}, {
      preserveScroll: true
    })
  }
}
</script>

<style scoped>
@reference "../../../../css/app.css";
</style>
