<template>
  <UserDashboardLayout>
    <div class="max-w-3xl mx-auto space-y-6">

      <!-- Success Banner -->
      <div v-if="$page.props.flash?.success"
        class="relative overflow-hidden bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl p-5 text-white shadow-lg">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full"></div>
        <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-white/5 rounded-full"></div>
        <div class="relative flex items-center gap-4">
          <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center text-2xl shrink-0">✅</div>
          <div>
            <h3 class="font-bold text-lg">Order Placed Successfully!</h3>
            <p class="text-green-100 text-sm mt-0.5">{{ $page.props.flash.success }}</p>
          </div>
        </div>
      </div>

      <!-- Order Header Card -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-5 text-white">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-orange-200 text-xs font-medium uppercase tracking-wider">Order Number</p>
              <h2 class="text-2xl font-bold mt-0.5">#{{ order.order_number }}</h2>
            </div>
            <span class="px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wide"
              :class="{
                'bg-yellow-400 text-yellow-900': order.status === 'pending',
                'bg-blue-400 text-blue-900': order.status === 'processing',
                'bg-orange-300 text-orange-900': order.status === 'shipped',
                'bg-green-400 text-green-900': order.status === 'delivered',
                'bg-red-400 text-red-900': order.status === 'cancelled',
              }">
              {{ order.status }}
            </span>
          </div>
        </div>
        <div class="px-6 py-4 flex flex-wrap gap-6 text-sm bg-orange-50/50">
          <div class="flex items-center gap-2">
            <span class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center text-orange-600">📅</span>
            <div>
              <p class="text-gray-400 text-xs">Placed on</p>
              <p class="font-semibold text-gray-700">{{ order.created_at }}</p>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <span class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600">💳</span>
            <div>
              <p class="text-gray-400 text-xs">Payment</p>
              <p class="font-semibold text-gray-700 uppercase">{{ order.payment_method }}</p>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg flex items-center justify-center"
              :class="order.payment_status === 'paid' ? 'bg-green-100 text-green-600' : 'bg-yellow-100 text-yellow-600'">
              {{ order.payment_status === 'paid' ? '✅' : '⏳' }}
            </span>
            <div>
              <p class="text-gray-400 text-xs">Payment Status</p>
              <p class="font-semibold capitalize" :class="order.payment_status === 'paid' ? 'text-green-600' : 'text-yellow-600'">
                {{ order.payment_status }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Order Items -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
          <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
            <span class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center text-sm">📦</span>
            Order Items
            <span class="ml-auto text-sm font-normal text-gray-400">{{ order.items.length }} item{{ order.items.length > 1 ? 's' : '' }}</span>
          </h3>
        </div>
        <div class="divide-y divide-gray-50">
          <div v-for="(item, index) in order.items" :key="item.id"
            class="px-6 py-4 flex items-center gap-4 hover:bg-gray-50/50 transition">
            <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center text-orange-600 font-bold text-sm shrink-0">
              {{ index + 1 }}
            </div>
            <div class="flex-1 min-w-0">
              <p class="font-semibold text-gray-900 truncate">{{ item.product_name }}</p>
              <p class="text-sm text-gray-400 mt-0.5">
                {{ item.quantity }} × ₹{{ Number(item.price).toLocaleString('en-IN') }}
              </p>
            </div>
            <div class="font-bold text-gray-900 text-right shrink-0">
              ₹{{ Number(item.total).toLocaleString('en-IN') }}
            </div>
          </div>
        </div>

        <!-- Totals -->
        <div class="px-6 py-4 bg-gray-50/80 space-y-3">
          <div class="flex justify-between text-sm text-gray-500">
            <span>Subtotal</span>
            <span class="font-medium">₹{{ Number(order.subtotal).toLocaleString('en-IN') }}</span>
          </div>
          <div class="flex justify-between text-sm text-gray-500">
            <span>Delivery Charge</span>
            <span class="font-medium" :class="Number(order.delivery_charge) === 0 ? 'text-green-600' : ''">
              {{ Number(order.delivery_charge) > 0 ? '₹' + Number(order.delivery_charge).toLocaleString('en-IN') : '🎉 FREE' }}
            </span>
          </div>
          <div class="flex justify-between items-center pt-3 border-t border-gray-200">
            <span class="text-lg font-bold text-gray-900">Total</span>
            <span class="text-2xl font-extrabold text-orange-600">₹{{ Number(order.total).toLocaleString('en-IN') }}</span>
          </div>
        </div>
      </div>

      <!-- Shipping Address -->
      <div v-if="order.shipping_address" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
          <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
            <span class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center text-sm">📍</span>
            Delivery Address
          </h3>
        </div>
        <div class="px-6 py-4">
          <div class="flex items-start gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-emerald-500 rounded-xl flex items-center justify-center text-white font-bold text-lg shrink-0">
              {{ (order.shipping_address.name || '?')[0].toUpperCase() }}
            </div>
            <div class="text-sm space-y-1">
              <p class="font-bold text-gray-900 text-base">{{ order.shipping_address.name }}</p>
              <p class="text-gray-600">{{ order.shipping_address.line1 }}</p>
              <p v-if="order.shipping_address.line2" class="text-gray-600">{{ order.shipping_address.line2 }}</p>
              <p class="text-gray-600">
                {{ order.shipping_address.city }}, {{ order.shipping_address.state }}
                <span class="font-medium">{{ order.shipping_address.pincode }}</span>
              </p>
              <p v-if="order.shipping_address.phone" class="flex items-center gap-2 mt-2 text-gray-700 font-medium">
                <span class="w-6 h-6 bg-green-100 rounded-md flex items-center justify-center text-xs">📞</span>
                {{ order.shipping_address.phone }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Notes -->
      <div v-if="order.notes" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
          <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
            <span class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center text-sm">📝</span>
            Order Notes
          </h3>
        </div>
        <div class="px-6 py-4">
          <p class="text-gray-600 text-sm leading-relaxed">{{ order.notes }}</p>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex items-center justify-between">
        <Link href="/dashboard/orders"
          class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-medium text-sm transition">
          ← Back to Orders
        </Link>
        <Link href="/products"
          class="inline-flex items-center gap-2 px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white rounded-xl font-medium text-sm transition shadow-sm">
          🛒 Continue Shopping
        </Link>
      </div>
    </div>
  </UserDashboardLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import UserDashboardLayout from '@/Layouts/UserDashboardLayout.vue'
defineProps({ order: Object })
</script>
