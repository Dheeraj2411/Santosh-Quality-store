<template>
  <div class="min-h-screen bg-amber-50 flex flex-col">
    <!-- Navbar -->
    <nav class="bg-white shadow-md sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <!-- Logo -->
          <Link href="/" class="flex items-center gap-3">
            <div class="w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center">
              <span class="text-white font-bold text-lg">स</span>
            </div>
            <div class="leading-tight">
              <div class="font-bold text-orange-600 text-lg">Santosh Store</div>
              <div class="text-xs text-gray-500 hidden sm:block">संतोष स्टोर</div>
            </div>
          </Link>

          <!-- Desktop Nav -->
          <div class="hidden md:flex items-center gap-6">
            <Link href="/" class="text-gray-700 hover:text-orange-500 transition font-medium">Home</Link>
            <Link href="/products" class="text-gray-700 hover:text-orange-500 transition font-medium">Products</Link>
            <Link href="/about" class="text-gray-700 hover:text-orange-500 transition font-medium">About</Link>
            <Link href="/contact" class="text-gray-700 hover:text-orange-500 transition font-medium">Contact</Link>
          </div>

          <!-- Right Side -->
          <div class="flex items-center gap-3">
            <!-- Cart Button -->
            <button @click="cart.toggleDrawer()"
              class="relative p-2 text-gray-600 hover:text-orange-500 hover:bg-orange-50 rounded-full transition">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17M17 13v4a2 2 0 11-4 0v-4m4 0H7" />
              </svg>
              <span v-if="cart.count > 0"
                class="absolute -top-1 -right-1 bg-orange-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">
                {{ cart.count }}
              </span>
            </button>

            <!-- Auth -->
            <template v-if="$page.props.auth.user">
              <div class="relative group">
                <button class="flex items-center gap-2 text-sm text-gray-700 hover:text-orange-500">
                  <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center font-semibold text-orange-600">
                    {{ $page.props.auth.user.name[0] }}
                  </div>
                  <span class="hidden sm:block">{{ $page.props.auth.user.name.split(' ')[0] }}</span>
                </button>
                <div class="absolute right-0 mt-1 w-48 bg-white rounded-xl shadow-lg opacity-0 group-hover:opacity-100 invisible group-hover:visible transition-all z-50 border border-gray-100">
                  <Link href="/dashboard" class="block px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 rounded-t-xl">Dashboard</Link>
                  <Link href="/dashboard/orders" class="block px-4 py-3 text-sm text-gray-700 hover:bg-orange-50">My Orders</Link>
                  <Link href="/dashboard/favorites" class="block px-4 py-3 text-sm text-gray-700 hover:bg-orange-50">Favorites</Link>
                  <Link v-if="isAdmin" href="/admin" class="block px-4 py-3 text-sm text-indigo-700 hover:bg-indigo-50">Admin Panel</Link>
                  <Link href="/logout" method="post" as="button" class="block w-full text-left px-4 py-3 text-sm text-red-600 hover:bg-red-50 rounded-b-xl">Logout</Link>
                </div>
              </div>
            </template>
            <template v-else>
              <Link href="/login" class="text-sm text-orange-600 font-medium hover:text-orange-700">Login</Link>
              <Link href="/register" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-full text-sm font-medium transition">Register</Link>
            </template>

            <!-- Mobile menu button -->
            <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 text-gray-600 hover:text-orange-500 rounded-lg">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path v-if="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Mobile Menu -->
        <div v-if="mobileOpen" class="md:hidden border-t border-gray-100 py-3 space-y-1">
          <Link href="/" class="block px-3 py-2 text-gray-700 hover:text-orange-500 hover:bg-orange-50 rounded-lg">Home</Link>
          <Link href="/products" class="block px-3 py-2 text-gray-700 hover:text-orange-500 hover:bg-orange-50 rounded-lg">Products</Link>
          <Link href="/about" class="block px-3 py-2 text-gray-700 hover:text-orange-500 hover:bg-orange-50 rounded-lg">About</Link>
          <Link href="/contact" class="block px-3 py-2 text-gray-700 hover:text-orange-500 hover:bg-orange-50 rounded-lg">Contact</Link>
        </div>
      </div>
    </nav>

    <!-- Flash Messages -->
    <div v-if="$page.props.flash" class="fixed top-20 right-4 z-50 space-y-2">
      <div v-if="$page.props.flash.success"
        class="bg-green-500 text-white px-5 py-3 rounded-xl shadow-lg flex items-center gap-2 animate-fade-in">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        {{ $page.props.flash.success }}
      </div>
      <div v-if="$page.props.flash.error"
        class="bg-red-500 text-white px-5 py-3 rounded-xl shadow-lg flex items-center gap-2 animate-fade-in">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        {{ $page.props.flash.error }}
      </div>
    </div>

    <!-- Cart Drawer -->
    <CartDrawer />

    <!-- Page Content -->
    <main class="flex-1">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white">
      <div class="max-w-7xl mx-auto px-4 py-12 grid grid-cols-1 md:grid-cols-3 gap-8">
        <div>
          <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center">
              <span class="text-white font-bold text-lg">स</span>
            </div>
            <div>
              <div class="font-bold text-lg text-orange-400">Santosh Store</div>
              <div class="text-xs text-gray-400">संतोष स्टोर</div>
            </div>
          </div>
          <p class="text-gray-400 text-sm leading-relaxed">Your trusted neighbourhood grocery store. Fresh produce, quality products, and the best prices in Kirti Nagar, Gurugram.</p>
          <div class="mt-4 flex items-center gap-2 text-yellow-400">
            ⭐⭐⭐⭐ <span class="text-gray-300 text-sm">4.0 (35 reviews)</span>
          </div>
        </div>
        <div>
          <h3 class="font-bold text-orange-400 mb-4 text-lg">Quick Links</h3>
          <div class="space-y-2">
            <Link href="/products" class="block text-gray-400 hover:text-orange-400 text-sm transition">All Products</Link>
            <Link href="/about" class="block text-gray-400 hover:text-orange-400 text-sm transition">About Us</Link>
            <Link href="/contact" class="block text-gray-400 hover:text-orange-400 text-sm transition">Contact Us</Link>
            <Link href="/dashboard" class="block text-gray-400 hover:text-orange-400 text-sm transition">My Account</Link>
          </div>
        </div>
        <div>
          <h3 class="font-bold text-orange-400 mb-4 text-lg">Contact Us</h3>
          <div class="space-y-2 text-gray-400 text-sm">
            <div class="flex items-start gap-2">
              <span>📍</span>
              <span>Kirti Nagar, Sector 15 Part 1, Gurugram, Haryana 122001</span>
            </div>
            <div class="flex items-center gap-2">
              <span>📞</span>
              <a href="tel:+919650671568" class="hover:text-orange-400 transition">+91 96506 71568</a>
            </div>
            <div class="flex items-center gap-2">
              <span>🕐</span>
              <span>6:00 AM – 10:00 PM (Daily)</span>
            </div>
            <div class="flex items-center gap-2">
              <span>🚚</span>
              <span>Free delivery on orders above ₹500</span>
            </div>
          </div>
        </div>
      </div>
      <div class="border-t border-gray-800 py-4 text-center text-gray-500 text-sm">
        © {{ new Date().getFullYear() }} Santosh Store. All rights reserved. Made with ❤️ in Gurugram.
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { useCartStore } from '@/stores/cartStore'
import CartDrawer from '@/Components/CartDrawer.vue'

const cart = useCartStore()
const page = usePage()
const mobileOpen = ref(false)
const isAdmin = computed(() => page.props.auth?.user?.role === 'admin' || false)

// Fetch cart on mount
cart.fetchCart()
</script>
