<template>
  <AdminLayout :title="'Order #' + order.order_number" subtitle="Order details and status management">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Left: Items -->
      <div class="lg:col-span-2 space-y-6">
        <div class="rounded-2xl shadow-sm border p-6 transition-colors bg-white border-gray-100">
          <h2 class="font-bold mb-4 text-gray-900">Order Items</h2>
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
          <div class="mt-4 pt-4 border-t space-y-2 text-sm border-gray-100">
            <div class="flex justify-between text-gray-500"><span>Subtotal</span><span>₹{{ order.subtotal }}</span></div>
            <div class="flex justify-between text-gray-500"><span>Delivery</span><span>₹{{ order.delivery_charge }}</span></div>
            <div class="flex justify-between font-bold text-base text-gray-900">
              <span>Total</span><span class="text-orange-600">₹{{ order.total }}</span>
            </div>
          </div>
        </div>

        <!-- Shipping Address -->
        <div v-if="order.shipping_address" class="rounded-2xl shadow-sm border p-6 transition-colors bg-white border-gray-100">
          <h2 class="font-bold mb-3 text-gray-900">Shipping Address</h2>
          <div class="text-sm space-y-1 text-gray-600">
            <p class="font-semibold text-gray-900">{{ order.shipping_address.name }}</p>
            <p>{{ order.shipping_address.phone }}</p>
            <p>{{ order.shipping_address.line1 }}, {{ order.shipping_address.line2 }}</p>
            <p>{{ order.shipping_address.city }}, {{ order.shipping_address.state }} {{ order.shipping_address.pincode }}</p>
          </div>
        </div>
      </div>

      <!-- Right: Status & Customer -->
      <div class="space-y-6">
        <div class="rounded-2xl shadow-sm border p-6 transition-colors bg-white border-gray-100">
          <h2 class="font-bold mb-4 text-gray-900">Update Status</h2>
          <form @submit.prevent="updateStatus" class="space-y-3">
            <select v-model="newStatus" class="w-full border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 outline-none transition-colors bg-white border-gray-200">
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

        <div class="rounded-2xl shadow-sm border p-6 transition-colors bg-white border-gray-100">
          <h2 class="font-bold mb-3 text-gray-900">Customer</h2>
          <div class="text-sm">
            <p class="font-semibold text-gray-800">{{ order.user?.name }}</p>
            <p class="text-gray-500">{{ order.user?.email }}</p>
          </div>
        </div>

        <div class="rounded-2xl shadow-sm border p-6 transition-colors bg-white border-gray-100">
          <h2 class="font-bold mb-3 text-gray-900">Payment</h2>
          <div class="space-y-2 text-sm">
            <div class="flex justify-between"><span class="text-gray-500">Method</span><span class="font-medium capitalize text-gray-900">{{ order.payment_method }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Status</span><span class="font-medium capitalize text-gray-900">{{ order.payment_status }}</span></div>
          </div>
        </div>

        <!-- WhatsApp Notification -->
        <div class="rounded-2xl shadow-sm border p-6 transition-colors bg-white border-gray-100">
          <h2 class="font-bold mb-3 text-gray-900">📱 WhatsApp</h2>
          <a :href="whatsappUrl" target="_blank" rel="noopener"
            class="flex items-center justify-center gap-2 w-full bg-green-500 hover:bg-green-600 text-white py-2.5 rounded-xl font-semibold text-sm transition">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            Send Order to WhatsApp
          </a>
          <p class="text-xs mt-2 text-center text-gray-400">Opens WhatsApp with order details pre-filled</p>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({ order: Object })
const newStatus = ref(props.order.status)

function updateStatus() {
  router.post('/admin/orders/' + props.order.id + '/status', { status: newStatus.value })
}

const whatsappUrl = computed(() => {
  const o = props.order
  const addr = o.shipping_address || {}
  const addressLine = [addr.line1, addr.line2, addr.city, addr.state, addr.pincode].filter(Boolean).join(', ')
  const items = (o.items || []).map((item, i) =>
    `${i + 1}. ${item.product_name} (Qty: ${item.quantity}) - ₹${item.total}`
  ).join('\n')

  const sn = usePage().props.settings?.store_name || 'Santosh Store'
  const message = `🛒 *New Order - ${sn}*\n\n`
    + `Order ID: ${o.order_number}\n`
    + `Payment: ${(o.payment_method || '').toUpperCase()} (${o.payment_status})\n\n`
    + `*Customer Details:*\n`
    + `Name: ${addr.name || o.user?.name || 'N/A'}\n`
    + `Phone: ${addr.phone || 'N/A'}\n`
    + `Address: ${addressLine}\n\n`
    + `*Order Items:*\n${items}\n\n`
    + `Total Amount: ₹${o.total}\n`
    + (o.notes ? `\nNotes: ${o.notes}\n` : '')

  return `https://wa.me/919910494819?text=${encodeURIComponent(message)}`
})
</script>
