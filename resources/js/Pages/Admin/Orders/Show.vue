<template>
  <AdminLayout :title="'Order #' + order.order_number" subtitle="Order details and status management">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Left: Items -->
      <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
          <h2 class="font-bold text-gray-900 mb-4">Order Items</h2>
          <table class="w-full text-sm">
            <thead class="border-b border-gray-100">
              <tr>
                <th class="text-left pb-3 font-semibold text-gray-600">Product</th>
                <th class="text-right pb-3 font-semibold text-gray-600">Price</th>
                <th class="text-right pb-3 font-semibold text-gray-600">Qty</th>
                <th class="text-right pb-3 font-semibold text-gray-600">Total</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in order.items" :key="item.id" class="border-b border-gray-50">
                <td class="py-3 font-medium text-gray-800">{{ item.product_name }}</td>
                <td class="py-3 text-right text-gray-600">₹{{ item.price }}</td>
                <td class="py-3 text-right text-gray-600">{{ item.quantity }}</td>
                <td class="py-3 text-right font-bold text-gray-900">₹{{ item.total }}</td>
              </tr>
            </tbody>
          </table>
          <div class="mt-4 pt-4 border-t border-gray-100 space-y-2 text-sm">
            <div class="flex justify-between text-gray-500"><span>Subtotal</span><span>₹{{ order.subtotal }}</span></div>
            <div class="flex justify-between text-gray-500"><span>Delivery</span><span>₹{{ order.delivery_charge }}</span></div>
            <div class="flex justify-between font-bold text-gray-900 text-base">
              <span>Total</span><span class="text-orange-600">₹{{ order.total }}</span>
            </div>
          </div>
        </div>

        <!-- Shipping Address -->
        <div v-if="order.shipping_address" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
          <h2 class="font-bold text-gray-900 mb-3">Shipping Address</h2>
          <div class="text-sm text-gray-600 space-y-1">
            <p class="font-semibold">{{ order.shipping_address.name }}</p>
            <p>{{ order.shipping_address.phone }}</p>
            <p>{{ order.shipping_address.line1 }}, {{ order.shipping_address.line2 }}</p>
            <p>{{ order.shipping_address.city }}, {{ order.shipping_address.state }} {{ order.shipping_address.pincode }}</p>
          </div>
        </div>
      </div>

      <!-- Right: Status & Customer -->
      <div class="space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
          <h2 class="font-bold text-gray-900 mb-4">Update Status</h2>
          <form @submit.prevent="updateStatus" class="space-y-3">
            <select v-model="newStatus" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 outline-none">
              <option value="pending">Pending</option>
              <option value="processing">Processing</option>
              <option value="shipped">Shipped</option>
              <option value="delivered">Delivered</option>
              <option value="cancelled">Cancelled</option>
            </select>
            <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white py-2.5 rounded-xl font-semibold text-sm transition">
              Update Status
            </button>
          </form>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
          <h2 class="font-bold text-gray-900 mb-3">Customer</h2>
          <div class="text-sm">
            <p class="font-semibold text-gray-800">{{ order.user?.name }}</p>
            <p class="text-gray-500">{{ order.user?.email }}</p>
          </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
          <h2 class="font-bold text-gray-900 mb-3">Payment</h2>
          <div class="space-y-2 text-sm">
            <div class="flex justify-between"><span class="text-gray-500">Method</span><span class="font-medium capitalize">{{ order.payment_method }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Status</span><span class="font-medium capitalize">{{ order.payment_status }}</span></div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({ order: Object })
const newStatus = ref(props.order.status)

function updateStatus() {
  router.post('/admin/orders/' + props.order.id + '/status', { status: newStatus.value })
}
</script>
