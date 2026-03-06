<template>
  <GuestLayout>
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-orange-500 via-amber-500 to-yellow-400 text-white overflow-hidden">
      <div class="absolute inset-0 opacity-10">
        <div class="absolute top-10 left-10 text-9xl">🌾</div>
        <div class="absolute top-20 right-20 text-8xl">🛒</div>
        <div class="absolute bottom-10 left-1/4 text-7xl">🌶️</div>
      </div>
      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28">
        <div class="max-w-2xl">
          <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm px-4 py-1.5 rounded-full text-sm font-medium mb-6">
            <span>⭐ 4.0 Rating</span>
            <span class="w-1 h-1 bg-white/50 rounded-full"></span>
            <span>35+ Happy Customers</span>
          </div>
          <h1 class="text-4xl md:text-6xl font-extrabold leading-tight mb-4 drop-shadow-sm">
            {{ storeName }}
          </h1>
          <p class="text-white/90 text-lg md:text-xl mb-8 leading-relaxed max-w-lg">
            Your trusted neighbourhood grocery store in Kirti Nagar, Gurugram. Fresh products, best prices, delivered to your doorstep.
          </p>
          <div class="flex flex-wrap gap-4">
            <Link href="/products"
              class="bg-white text-orange-600 px-8 py-3.5 rounded-full font-bold text-lg hover:bg-orange-50 transition shadow-lg hover:shadow-xl hover:-translate-y-0.5 duration-200">
              🛒 Shop Now
            </Link>
            <a href="tel:+919650671568"
              class="border-2 border-white text-white px-8 py-3.5 rounded-full font-bold text-lg hover:bg-white/10 transition backdrop-blur-sm">
              📞 Call Us
            </a>
          </div>
          <!-- Quick Stats -->
          <div class="flex flex-wrap gap-6 mt-10">
            <div class="flex items-center gap-2 text-white/90">
              <span class="text-2xl">🏠</span>
              <div>
                <div class="font-bold">Home Delivery</div>
                <div class="text-xs text-white/70">Within 2 hours</div>
              </div>
            </div>
            <div class="flex items-center gap-2 text-white/90">
              <span class="text-2xl">🕐</span>
              <div>
                <div class="font-bold">6 AM – 10 PM</div>
                <div class="text-xs text-white/70">Open Daily</div>
              </div>
            </div>
            <div class="flex items-center gap-2 text-white/90">
              <span class="text-2xl">🚚</span>
              <div>
                <div class="font-bold">Free Delivery</div>
                <div class="text-xs text-white/70">On orders ₹500+</div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Wave -->
      <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 60" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M0 60L1440 0V60H0Z" fill="#fffbeb"/>
        </svg>
      </div>
    </section>

    <!-- Services Strip -->
    <section class="py-6 border-b bg-amber-50 border-amber-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-3 gap-4 text-center">
          <div class="flex flex-col items-center gap-2 group">
            <div class="w-12 h-12 rounded-full flex items-center justify-center transition text-xl bg-orange-100 group-hover:bg-orange-200">🏪</div>
            <span class="text-sm font-semibold text-gray-700">In-store Shopping</span>
          </div>
          <div class="flex flex-col items-center gap-2 group">
            <div class="w-12 h-12 rounded-full flex items-center justify-center transition text-xl bg-orange-100 group-hover:bg-orange-200">📦</div>
            <span class="text-sm font-semibold text-gray-700">In-store Pickup</span>
          </div>
          <div class="flex flex-col items-center gap-2 group">
            <div class="w-12 h-12 rounded-full flex items-center justify-center transition text-xl bg-orange-100 group-hover:bg-orange-200">🛵</div>
            <span class="text-sm font-semibold text-gray-700">Home Delivery</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Categories -->
    <section class="py-14 bg-amber-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
          <h2 class="text-3xl md:text-4xl font-extrabold mb-3 text-gray-900">Shop by Category</h2>
          <p class="text-gray-500">Find everything you need for your kitchen</p>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
          <Link v-for="cat in categories" :key="cat.id"
            :href="'/products?category_id=' + cat.id"
            class="group flex flex-col items-center gap-3 rounded-2xl p-5 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 text-center bg-white border border-gray-100 hover:border-orange-200">
            <div class="w-14 h-14 rounded-full flex items-center justify-center transition text-2xl bg-orange-50 group-hover:bg-orange-100">
              {{ categoryEmoji(cat.name) }}
            </div>
            <div>
              <div class="font-semibold text-sm group-hover:text-orange-500 transition text-gray-800">{{ cat.name }}</div>
              <div class="text-xs mt-0.5 text-gray-400">{{ cat.products_count }} items</div>
            </div>
          </Link>
        </div>
      </div>
    </section>

    <!-- Featured Products -->
    <section class="py-14 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-10">
          <div>
            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900">⭐ Featured Products</h2>
            <p class="mt-1 text-gray-500">Hand-picked quality essentials</p>
          </div>
          <Link href="/products" class="text-orange-500 hover:text-orange-600 font-semibold text-sm flex items-center gap-1">
            View All <span>→</span>
          </Link>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-5">
          <ProductCard v-for="product in featuredProducts" :key="product.id" :product="product" />
        </div>
      </div>
    </section>

    <!-- Reviews -->
    <section class="py-14 bg-amber-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
          <h2 class="text-3xl md:text-4xl font-extrabold mb-3 text-gray-900">❤️ What Customers Say</h2>
          <div class="flex justify-center items-center gap-2 text-amber-500">
            <span class="text-3xl">⭐⭐⭐⭐</span>
            <span class="font-semibold text-gray-600">4.0 / 5.0 (35 reviews)</span>
          </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="review in reviews" :key="review.id"
            class="rounded-2xl p-6 shadow-sm hover:shadow-md transition hover:-translate-y-0.5 duration-300 bg-white border border-gray-100">
            <div class="flex items-center gap-3 mb-3">
              <img :src="review.user_avatar" :alt="review.user_name" class="w-11 h-11 rounded-full object-cover" />
              <div>
                <div class="font-semibold text-gray-800">{{ review.user_name }}</div>
                <div class="text-yellow-400 text-sm">{{ '⭐'.repeat(review.rating) }}</div>
              </div>
            </div>
            <p v-if="review.title" class="font-semibold text-sm mb-1 text-gray-800">{{ review.title }}</p>
            <p class="text-sm leading-relaxed line-clamp-3 text-gray-600">{{ review.body }}</p>
            <div class="mt-3 text-xs text-gray-400">{{ review.created_at }}</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Map + Contact -->
    <section class="py-14 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
          <div>
            <h2 class="text-3xl font-extrabold mb-6 text-gray-900">📍 Find Us</h2>
            <div class="space-y-5">
              <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg flex-shrink-0 bg-orange-100">📍</div>
                <div>
                  <div class="font-semibold text-gray-800">Address</div>
                  <div class="text-sm text-gray-500">Kirti Nagar, Sector 15 Part 1, Sector 15, Gurugram, Haryana 122001</div>
                </div>
              </div>
              <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg flex-shrink-0 bg-orange-100">📞</div>
                <div>
                  <div class="font-semibold text-gray-800">Phone</div>
                  <a href="tel:+919650671568" class="text-orange-500 hover:text-orange-600 font-medium">+91 96506 71568</a>
                </div>
              </div>
              <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg flex-shrink-0 bg-orange-100">🕐</div>
                <div>
                  <div class="font-semibold text-gray-800">Business Hours</div>
                  <div class="text-sm text-gray-500">Monday – Sunday: 6:00 AM – 10:00 PM</div>
                </div>
              </div>
            </div>
            <a href="https://maps.google.com/?q=Kirti+Nagar+Sector+15+Gurugram+Haryana" target="_blank"
              class="mt-6 inline-flex items-center gap-2 bg-gradient-to-r from-orange-500 to-amber-500 text-white px-6 py-3 rounded-xl font-semibold hover:from-orange-600 hover:to-amber-600 transition shadow-md">
              🗺️ Open in Google Maps
            </a>
          </div>
          <!-- Map Embed -->
          <div class="rounded-2xl overflow-hidden shadow-lg h-80">
            <iframe
              src="https://maps.google.com/maps?q=Kirti+Nagar+Sector+15+Gurugram+Haryana&output=embed&z=15"
              width="100%" height="100%" style="border:0;" allowfullscreen loading="lazy"
              referrerpolicy="no-referrer-when-downgrade">
            </iframe>
          </div>
        </div>
      </div>
    </section>
  </GuestLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import ProductCard from '@/Components/ProductCard.vue'

const storeName = computed(() => usePage().props.settings?.store_name || 'Santosh Store')
const storeTagline = computed(() => usePage().props.settings?.store_tagline || '')

defineProps({
  featuredProducts: Array,
  categories:       Array,
  reviews:          Array,
  settings:         Object,
})

const emojiMap = {
  'Dal & Pulses':     '🫘',
  'Rice & Grains':    '🌾',
  'Spices & Masala':  '🌶️',
  'Oil & Ghee':       '🫙',
  'Snacks & Namkeen': '🍟',
  'Beverages':        '☕',
  'Dairy & Paneer':   '🥛',
  'Atta & Flour':     '🌽',
  'Sugar & Salt':     '🧂',
  'Personal Care':    '🧴',
}

function categoryEmoji(name) {
  return emojiMap[name] ?? '🛒'
}
</script>
