<template>
  <GuestLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900">All Products</h1>
        <p class="text-gray-500 mt-1">{{ products.total }} products found</p>
      </div>

      <div class="flex flex-col lg:flex-row gap-8">
        <!-- Filters Sidebar -->
        <aside class="w-full lg:w-64 flex-shrink-0">
          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sticky top-20">
            <h2 class="font-bold text-gray-900 mb-4 text-lg">🔍 Filters</h2>

            <!-- Search -->
            <div class="mb-5">
              <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
              <input v-model="filters.search" @input="debouncedFilter" type="text"
                placeholder="Search products..."
                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none" />
            </div>

            <!-- Categories -->
            <div class="mb-5">
              <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
              <div class="space-y-2">
                <label class="flex items-center gap-2 cursor-pointer">
                  <input type="radio" v-model="filters.category_id" :value="null" @change="applyFilters"
                    class="text-orange-500 focus:ring-orange-500" />
                  <span class="text-sm text-gray-600">All Categories</span>
                </label>
                <label v-for="cat in categories" :key="cat.id" class="flex items-center gap-2 cursor-pointer">
                  <input type="radio" v-model="filters.category_id" :value="cat.id" @change="applyFilters"
                    class="text-orange-500 focus:ring-orange-500" />
                  <span class="text-sm text-gray-600">{{ cat.name }}</span>
                </label>
              </div>
            </div>

            <!-- Price Range -->
            <div class="mb-5">
              <label class="block text-sm font-medium text-gray-700 mb-2">Price Range</label>
              <div class="flex gap-2">
                <input v-model="filters.min_price" @input="debouncedFilter" type="number" placeholder="Min ₹"
                  class="w-1/2 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 outline-none" />
                <input v-model="filters.max_price" @input="debouncedFilter" type="number" placeholder="Max ₹"
                  class="w-1/2 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 outline-none" />
              </div>
            </div>

            <!-- Sort -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
              <select v-model="filters.sort" @change="applyFilters"
                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 outline-none">
                <option value="sort_order">Default</option>
                <option value="price">Price: Low to High</option>
                <option value="name">Name A-Z</option>
              </select>
            </div>

            <button @click="clearFilters"
              class="mt-4 w-full text-sm text-orange-600 hover:text-orange-700 font-medium py-2">
              Clear All Filters
            </button>
          </div>
        </aside>

        <!-- Product Grid -->
        <div class="flex-1 min-w-0">
          <!-- Active Filters -->
          <div v-if="hasActiveFilters" class="flex flex-wrap gap-2 mb-5">
            <span v-if="filters.search" class="badge" @click="filters.search = null; applyFilters()">
              Search: {{ filters.search }} ×
            </span>
            <span v-if="filters.category_id" class="badge" @click="filters.category_id = null; applyFilters()">
              Category ×
            </span>
            <span v-if="filters.min_price" class="badge" @click="filters.min_price = null; applyFilters()">
              Min ₹{{ filters.min_price }} ×
            </span>
          </div>

          <div v-if="products.data.length === 0" class="text-center py-20 text-gray-400">
            <div class="text-6xl mb-4">🔍</div>
            <p class="text-xl font-semibold text-gray-600">No products found</p>
            <p class="text-sm mt-2">Try adjusting your filters</p>
            <button @click="clearFilters" class="mt-4 bg-orange-500 text-white px-6 py-2 rounded-full hover:bg-orange-600 transition text-sm">
              Clear Filters
            </button>
          </div>

          <div v-else class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-5">
            <ProductCard v-for="product in products.data" :key="product.id" :product="product" />
          </div>

          <!-- Pagination -->
          <div v-if="products.last_page > 1" class="flex justify-center gap-2 mt-10">
            <Link v-for="link in products.links" :key="link.label"
              :href="link.url || ''"
              v-html="link.label"
              class="px-4 py-2 rounded-lg text-sm border transition"
              :class="link.active
                ? 'bg-orange-500 text-white border-orange-500'
                : link.url ? 'border-gray-300 text-gray-600 hover:border-orange-300 hover:text-orange-600' : 'border-gray-200 text-gray-300 cursor-not-allowed pointer-events-none'"
            />
          </div>
        </div>
      </div>
    </div>
  </GuestLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import ProductCard from '@/Components/ProductCard.vue'

const props = defineProps({
  products:   Object,
  categories: Array,
  filters:    Object,
})

const filters = ref({
  search:      props.filters?.search || '',
  category_id: props.filters?.category_id || null,
  min_price:   props.filters?.min_price || null,
  max_price:   props.filters?.max_price || null,
  sort:        props.filters?.sort || 'sort_order',
})

const hasActiveFilters = computed(() =>
  filters.value.search || filters.value.category_id || filters.value.min_price || filters.value.max_price
)

let debounceTimer = null
function debouncedFilter() {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(applyFilters, 500)
}

function applyFilters() {
  const query = {}
  if (filters.value.search) query.search = filters.value.search
  if (filters.value.category_id) query.category_id = filters.value.category_id
  if (filters.value.min_price) query.min_price = filters.value.min_price
  if (filters.value.max_price) query.max_price = filters.value.max_price
  if (filters.value.sort) query.sort = filters.value.sort
  router.get('/products', query, { preserveScroll: true, replace: true })
}

function clearFilters() {
  filters.value = { search: '', category_id: null, min_price: null, max_price: null, sort: 'sort_order' }
  router.get('/products', {})
}
</script>

<style scoped>
@reference "../../../css/app.css";
.badge {
  @apply inline-flex items-center gap-1 bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-xs font-medium cursor-pointer hover:bg-orange-200;
}
</style>
