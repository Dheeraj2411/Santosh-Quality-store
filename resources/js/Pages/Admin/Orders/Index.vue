<template>
  <AdminLayout title="Orders" subtitle="View and manage all orders">
    <!-- Filters -->
    <div class="flex flex-wrap gap-3 mb-6">
      <select v-model="statusFilter" @change="applyFilter"
        class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 outline-none">
        <option value="">All Statuses</option>
        <option value="pending">Pending</option>
        <option value="processing">Processing</option>
        <option value="shipped">Shipped</option>
        <option value="delivered">Delivered</option>
        <option value="cancelled">Cancelled</option>
      </select>
      <input v-model="searchQuery" @input="applyFilter" type="text" placeholder="🔍 Search by order #..."
        class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 outline-none" />
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
              <th class="text-left py-3 px-4 font-semibold text-gray-600">Order #</th>
              <th class="text-left py-3 px-4 font-semibold text-gray-600">Customer</th>
              <th class="text-left py-3 px-4 font-semibold text-gray-600">Total</th>
              <th class="text-left py-3 px-4 font-semibold text-gray-600">Status</th>
              <th class="text-left py-3 px-4 font-semibold text-gray-600">Date</th>
              <th class="py-3 px-4"></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in orders.data" :key="order.id" class="border-b border-gray-50 hover:bg-gray-50">
              <td class="py-3 px-4 font-mono font-medium text-gray-800">#{{ order.order_number }}</td>
              <td class="py-3 px-4 text-gray-600">{{ order.user_name }}</td>
              <td class="py-3 px-4 font-bold text-orange-600">₹{{ Number(order.total).toLocaleString('en-IN') }}</td>
              <td class="py-3 px-4">
                <span class="px-2 py-1 rounded-full text-xs font-semibold capitalize"
                  :class="{
                    'bg-yellow-100 text-yellow-700': order.status === 'pending',
                    'bg-blue-100 text-blue-700': order.status === 'processing',
                    'bg-orange-100 text-orange-700': order.status === 'shipped',
                    'bg-green-100 text-green-700': order.status === 'delivered',
                    'bg-red-100 text-red-700': order.status === 'cancelled',
                  }">{{ order.status }}
                </span>
              </td>
              <td class="py-3 px-4 text-gray-400 text-xs">{{ order.created_at }}</td>
              <td class="py-3 px-4">
                <Link :href="'/admin/orders/' + order.id" class="text-blue-500 hover:text-blue-600 text-xs font-medium">View</Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({ orders: Object, filters: Object })
const statusFilter = ref(props.filters?.status || '')
const searchQuery = ref(props.filters?.search || '')

let timer = null
function applyFilter() {
  clearTimeout(timer)
  timer = setTimeout(() => {
    const q = {}
    if (statusFilter.value) q.status = statusFilter.value
    if (searchQuery.value) q.search = searchQuery.value
    router.get('/admin/orders', q, { preserveScroll: true, replace: true })
  }, 400)
}
</script>
