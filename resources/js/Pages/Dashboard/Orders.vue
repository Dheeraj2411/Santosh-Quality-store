<template>
  <UserDashboardLayout>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
      <h2 class="text-xl font-bold text-gray-900 mb-6">My Orders</h2>
      <div v-if="orders.data.length === 0" class="text-center py-16 text-gray-400">
        <div class="text-6xl mb-4">📦</div>
        <p class="text-lg font-medium text-gray-500">No orders yet</p>
        <Link href="/products" class="mt-4 inline-block bg-orange-500 text-white px-6 py-2.5 rounded-full font-medium hover:bg-orange-600 transition text-sm">
          Start Shopping
        </Link>
      </div>
      <div v-else class="space-y-4">
        <div v-for="order in orders.data" :key="order.id"
          class="border border-gray-100 rounded-2xl p-5 hover:shadow-sm transition">
          <div class="flex items-center justify-between mb-3">
            <div>
              <div class="font-bold text-gray-900">#{{ order.order_number }}</div>
              <div class="text-xs text-gray-400 mt-0.5">{{ order.created_at }}</div>
            </div>
            <div class="flex items-center gap-3">
              <span class="text-lg font-bold text-orange-600">₹{{ Number(order.total).toLocaleString('en-IN') }}</span>
              <span class="px-3 py-1 rounded-full text-xs font-semibold capitalize"
                :class="{
                  'bg-yellow-100 text-yellow-700': order.status === 'pending',
                  'bg-blue-100 text-blue-700': order.status === 'processing',
                  'bg-orange-100 text-orange-700': order.status === 'shipped',
                  'bg-green-100 text-green-700': order.status === 'delivered',
                  'bg-red-100 text-red-700': order.status === 'cancelled',
                }">
                {{ order.status }}
              </span>
            </div>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-sm text-gray-500">{{ order.items_count }} item{{ order.items_count > 1 ? 's' : '' }}</span>
            <Link :href="'/dashboard/orders/' + order.id"
              class="text-sm text-orange-500 hover:text-orange-600 font-medium">
              View Details →
            </Link>
          </div>
        </div>
      </div>
      <!-- Pagination -->
      <div v-if="orders.last_page > 1" class="flex justify-center gap-2 mt-6">
        <Link v-for="link in orders.links" :key="link.label"
          :href="link.url || '#'" v-html="link.label"
          class="px-3 py-1.5 rounded-lg text-sm border transition"
          :class="link.active ? 'bg-orange-500 text-white border-orange-500' : 'border-gray-200 text-gray-600'"
        />
      </div>
    </div>
  </UserDashboardLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import UserDashboardLayout from '@/Layouts/UserDashboardLayout.vue'
defineProps({ orders: Object })
</script>
