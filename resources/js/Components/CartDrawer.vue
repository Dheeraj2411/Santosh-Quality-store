<template>
  <!-- Backdrop -->
  <Teleport to="body">
    <div v-if="cart.isOpen" class="fixed inset-0 z-50 flex">
      <!-- Overlay -->
      <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="cart.closeDrawer()"></div>

      <!-- Drawer -->
      <div class="relative ml-auto flex flex-col bg-white w-full max-w-md h-full shadow-2xl z-50">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b bg-orange-500 text-white">
          <h2 class="text-xl font-bold flex items-center gap-2">
            🛒 Your Cart
            <span class="bg-white text-orange-600 text-sm font-bold px-2 py-0.5 rounded-full">{{ cart.count }}</span>
          </h2>
          <button @click="cart.closeDrawer()" class="p-2 rounded-lg hover:bg-orange-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <!-- Items -->
        <div class="flex-1 overflow-y-auto px-4 py-4 space-y-3">
          <div v-if="cart.isEmpty" class="text-center py-16 text-gray-400">
            <div class="text-6xl mb-4">🛒</div>
            <p class="font-medium text-gray-600">Your cart is empty</p>
            <p class="text-sm mt-1">Add some delicious groceries!</p>
            <Link href="/products" @click="cart.closeDrawer()"
              class="mt-4 inline-block bg-orange-500 text-white px-6 py-2.5 rounded-full text-sm font-medium hover:bg-orange-600 transition">
              Browse Products
            </Link>
          </div>

          <div v-for="item in cart.items" :key="item.product_id"
            class="flex items-center gap-3 bg-gray-50 rounded-xl p-3">
            <img :src="item.thumbnail" :alt="item.name"
              class="w-16 h-16 object-cover rounded-lg flex-shrink-0 bg-gray-200" />
            <div class="flex-1 min-w-0">
              <p class="font-medium text-sm text-gray-800 truncate">{{ item.name }}</p>
              <p class="text-xs text-gray-400 mt-0.5">{{ item.unit }}</p>
              <p class="text-orange-600 font-bold text-sm mt-1">₹{{ formatPrice(item.price) }}</p>
            </div>
            <!-- Quantity -->
            <div class="flex items-center gap-2 flex-shrink-0">
              <button @click="cart.updateQuantity(item.product_id, item.quantity - 1)"
                class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center hover:bg-orange-200 transition font-bold text-lg leading-none">−</button>
              <span class="w-6 text-center font-bold text-sm">{{ item.quantity }}</span>
              <button @click="cart.updateQuantity(item.product_id, item.quantity + 1)"
                class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center hover:bg-orange-200 transition font-bold text-lg leading-none">+</button>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div v-if="!cart.isEmpty" class="border-t px-6 py-5 bg-gray-50 space-y-3">
          <div class="flex justify-between text-sm text-gray-600">
            <span>Subtotal</span>
            <span>₹{{ formatPrice(cart.subtotal) }}</span>
          </div>
          <div class="flex justify-between text-sm text-gray-600">
            <span>Delivery</span>
            <span :class="cart.deliveryCharge === 0 ? 'text-green-600 font-medium' : ''">
              {{ cart.deliveryCharge === 0 ? 'FREE' : '₹' + cart.deliveryCharge }}
            </span>
          </div>
          <div v-if="cart.deliveryCharge > 0" class="text-xs text-gray-400">
            Add ₹{{ formatPrice(500 - cart.subtotal) }} more for free delivery
          </div>
          <div class="flex justify-between font-bold text-lg text-gray-900 border-t pt-3">
            <span>Total</span>
            <span class="text-orange-600">₹{{ formatPrice(cart.total) }}</span>
          </div>
          <Link href="/checkout" @click="cart.closeDrawer()"
            class="block w-full bg-orange-500 hover:bg-orange-600 text-white text-center py-3.5 rounded-xl font-bold transition text-sm">
            Proceed to Checkout →
          </Link>
          <button @click="cart.closeDrawer()"
            class="block w-full text-center text-sm text-gray-500 hover:text-gray-700 py-1 transition">
            Continue Shopping
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { useCartStore } from '@/stores/cartStore'

const cart = useCartStore()

function formatPrice(val) {
  return Number(val).toLocaleString('en-IN')
}
</script>
