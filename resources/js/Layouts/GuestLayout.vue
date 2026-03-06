<template>
  <div class="min-h-screen flex flex-col bg-amber-50">
    <!-- Navbar -->
    <nav class="sticky top-0 z-50 shadow-md glass-nav border-b border-white/30 relative">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 gap-2">
          <!-- Logo -->
          <Link href="/" class="flex items-center gap-3 flex-shrink-0">
            <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-amber-500 rounded-full flex items-center justify-center shadow-lg">
              <span class="text-white font-bold text-lg">स</span>
            </div>
            <div class="leading-tight hidden sm:block">
              <div class="font-bold text-lg text-orange-600">{{ storeName }}</div>
            </div>
          </Link>

          <!-- Desktop Nav -->
          <div class="hidden lg:flex items-center gap-5 flex-shrink-0">
            <Link v-for="link in navLinks" :key="link.href" :href="link.href"
              class="font-medium transition hover:text-orange-500 text-gray-700 whitespace-nowrap text-sm">
              {{ link.label }}
            </Link>
          </div>

          <!-- Search Bar (Desktop) -->
          <div class="hidden md:flex items-center flex-1 max-w-[220px] lg:max-w-xs mx-2">
            <form @submit.prevent="submitSearch" class="relative w-full">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search products..."
                class="w-full pl-9 pr-3 py-2 rounded-full border border-gray-200 bg-white/80 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent transition shadow-sm"
              />
              <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
            </form>
          </div>

          <!-- Right Side -->
          <div class="flex items-center gap-2 flex-shrink-0">
            <!-- Cart Button -->
            <button @click="cart.toggleDrawer()"
              class="relative p-2 rounded-full transition text-gray-600 hover:text-orange-500 hover:bg-orange-50">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17M17 13v4a2 2 0 11-4 0v-4m4 0H7" />
              </svg>
              <span v-if="cart.count > 0"
                class="absolute -top-1 -right-1 bg-orange-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold animate-fade-in">
                {{ cart.count }}
              </span>
            </button>

            <!-- Auth -->
            <template v-if="$page.props.auth.user">
              <div class="relative group">
                <button class="flex items-center gap-2 text-sm text-gray-700 hover:text-orange-500">
                  <div class="w-8 h-8 bg-gradient-to-br from-orange-400 to-amber-500 rounded-full flex items-center justify-center font-semibold text-white text-sm shadow-sm">
                    {{ $page.props.auth.user.name[0] }}
                  </div>
                  <span class="hidden sm:block">{{ $page.props.auth.user.name.split(' ')[0] }}</span>
                </button>
                <div class="absolute right-0 mt-1 w-48 rounded-xl shadow-xl opacity-0 group-hover:opacity-100 invisible group-hover:visible transition-all z-50 glass-card">
                  <template v-if="!isAdmin">
                    <Link href="/dashboard" class="block px-4 py-3 text-sm rounded-t-xl transition text-gray-700 hover:bg-orange-50">Dashboard</Link>
                    <Link href="/dashboard/orders" class="block px-4 py-3 text-sm transition text-gray-700 hover:bg-orange-50">My Orders</Link>
                    <Link href="/dashboard/favorites" class="block px-4 py-3 text-sm transition text-gray-700 hover:bg-orange-50">Favorites</Link>
                  </template>
                  <Link v-if="isAdmin" href="/admin" class="block px-4 py-3 text-sm rounded-t-xl transition text-indigo-700 hover:bg-indigo-50">Admin Panel</Link>
                  <Link href="/logout" method="post" as="button" class="block w-full text-left px-4 py-3 text-sm rounded-b-xl transition text-red-600 hover:bg-red-50">Logout</Link>
                </div>
              </div>
            </template>
            <template v-else>
              <Link href="/login" class="text-sm font-medium transition text-orange-600 hover:text-orange-700">Login</Link>
              <Link href="/register"
                class="bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white px-4 py-2 rounded-full text-sm font-medium transition shadow-md hover:shadow-lg">
                Register
              </Link>
            </template>

            <!-- Mobile menu button -->
            <button @click="mobileOpen = !mobileOpen" class="lg:hidden p-2 rounded-lg transition text-gray-600 hover:text-orange-500">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path v-if="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Mobile Menu Backdrop + Panel -->
    <template v-if="mobileOpen">
      <!-- Backdrop (click to close) -->
      <div class="lg:hidden fixed inset-0 top-16 bg-black/30 z-30 animate-fade-in" @click="mobileOpen = false"></div>
      <!-- Menu Panel -->
      <div class="lg:hidden fixed inset-x-0 top-16 bg-white shadow-lg border-t border-gray-100 py-3 space-y-2 animate-fade-in z-40">
        <!-- Mobile Search -->
        <form @submit.prevent="submitSearch" class="relative px-4">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search products..."
            class="w-full pl-10 pr-4 py-2.5 rounded-full border border-gray-200 bg-white text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent transition"
          />
          <svg class="absolute left-7 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
        </form>
        <Link v-for="link in navLinks" :key="link.href" :href="link.href"
          class="block px-4 py-2.5 rounded-lg transition text-gray-700 hover:text-orange-500 hover:bg-orange-50 font-medium"
          @click="mobileOpen = false">
          {{ link.label }}
        </Link>
      </div>
    </template>

    <!-- Flash Messages -->
    <div v-if="$page.props.flash" class="fixed top-20 right-4 z-50 space-y-2">
      <div v-if="$page.props.flash.success"
        class="bg-emerald-500 text-white px-5 py-3 rounded-xl shadow-lg flex items-center gap-2 animate-fade-in backdrop-blur-sm">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        {{ $page.props.flash.success }}
      </div>
      <div v-if="$page.props.flash.error"
        class="bg-red-500 text-white px-5 py-3 rounded-xl shadow-lg flex items-center gap-2 animate-fade-in backdrop-blur-sm">
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
            <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-amber-500 rounded-full flex items-center justify-center shadow-lg">
              <span class="text-white font-bold text-lg">स</span>
            </div>
            <div>
              <div class="font-bold text-lg text-orange-400">{{ storeName }}</div>
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
      <div class="border-t border-gray-800 py-4 text-center text-sm text-gray-500">
        © {{ new Date().getFullYear() }} {{ storeName }}. All rights reserved. Made with ❤️ in Gurugram.
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'
import { useCartStore } from '@/stores/cartStore'
import CartDrawer from '@/Components/CartDrawer.vue'

const cart = useCartStore()
const page = usePage()
const mobileOpen = ref(false)
const searchQuery = ref('')

// Lock body scroll when mobile menu is open
watch(mobileOpen, (open) => {
  document.body.style.overflow = open ? 'hidden' : ''
})
const isAdmin = computed(() => page.props.auth?.user?.role === 'admin' || false)
const storeName = computed(() => page.props.settings?.store_name || 'Santosh Store')
const storeTagline = computed(() => page.props.settings?.store_tagline || '')

function submitSearch() {
  if (searchQuery.value.trim()) {
    router.get('/products', { search: searchQuery.value.trim() })
    mobileOpen.value = false
  }
}

const navLinks = [
  { href: '/', label: 'Home' },
  { href: '/products', label: 'Products' },
  { href: '/about', label: 'About' },
  { href: '/contact', label: 'Contact' },
]

// Fetch cart on mount
cart.fetchCart()
</script>
