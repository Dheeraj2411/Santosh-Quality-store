<template>
  <UserDashboardLayout>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
      <h2 class="text-xl font-bold text-gray-900 mb-6">❤️ Favorite Products</h2>
      <div v-if="favorites.data.length === 0" class="text-center py-16 text-gray-400">
        <div class="text-6xl mb-4">❤️</div>
        <p class="text-lg font-medium text-gray-500">No favorite products yet</p>
        <Link href="/products" class="mt-4 inline-block bg-orange-500 text-white px-6 py-2.5 rounded-full font-medium hover:bg-orange-600 transition text-sm">
          Browse Products
        </Link>
      </div>
      <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <ProductCard v-for="product in favorites.data" :key="product.id" :product="product" />
      </div>
    </div>
  </UserDashboardLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import UserDashboardLayout from '@/Layouts/UserDashboardLayout.vue'
import ProductCard from '@/Components/ProductCard.vue'
defineProps({ favorites: Object })
</script>
