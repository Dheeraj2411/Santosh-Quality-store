<template>
  <div class="bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 group overflow-hidden flex flex-col">
    <!-- Image -->
    <div class="relative overflow-hidden">
      <Link :href="'/products/' + product.slug">
        <img :src="product.thumbnail" :alt="product.name"
          class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-500 bg-gray-100" />
      </Link>
      <!-- Discount Badge -->
      <div v-if="product.discount"
        class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-lg">
        {{ product.discount }}% OFF
      </div>
      <!-- Featured Badge -->
      <div v-if="product.is_featured"
        class="absolute top-2 right-2 bg-amber-400 text-amber-900 text-xs font-bold px-2 py-1 rounded-lg">
        ⭐ Featured
      </div>
      <!-- Wishlist -->
      <button v-if="isAuth" @click.prevent="toggleFavorite"
        class="absolute bottom-2 right-2 w-9 h-9 bg-white rounded-full shadow flex items-center justify-center hover:scale-110 transition-transform"
        :class="{ 'opacity-0 group-hover:opacity-100': !isFavorited }">
        <span :class="isFavorited ? 'text-red-500' : 'text-gray-400'">{{ isFavorited ? '❤️' : '🤍' }}</span>
      </button>
    </div>

    <!-- Content -->
    <div class="p-4 flex flex-col flex-1">
      <p class="text-xs text-orange-500 font-medium uppercase tracking-wide mb-1">{{ product.category }}</p>
      <Link :href="'/products/' + product.slug"
        class="font-semibold text-gray-800 hover:text-orange-600 transition text-sm leading-tight line-clamp-2 flex-1">
        {{ product.name }}
      </Link>
      <p class="text-xs text-gray-400 mt-1">{{ product.unit }}</p>

      <!-- Price -->
      <div class="flex items-center gap-2 mt-2">
        <span class="text-lg font-bold text-orange-600">₹{{ product.price }}</span>
        <span v-if="product.mrp && product.mrp > product.price"
          class="text-sm text-gray-400 line-through">₹{{ product.mrp }}</span>
      </div>

      <!-- Stock badge -->
      <div class="mt-2">
        <span v-if="product.in_stock"
          class="inline-flex items-center gap-1 text-xs text-green-600 font-medium">
          <div class="w-1.5 h-1.5 bg-green-500 rounded-full"></div> In Stock
        </span>
        <span v-else class="inline-flex items-center gap-1 text-xs text-red-400 font-medium">
          <div class="w-1.5 h-1.5 bg-red-400 rounded-full"></div> Out of Stock
        </span>
      </div>

      <!-- Add to Cart -->
      <button @click="addToCart"
        :disabled="!product.in_stock || cart.loading"
        class="mt-3 w-full py-2.5 rounded-xl text-sm font-semibold transition-all duration-200"
        :class="product.in_stock
          ? 'bg-orange-500 hover:bg-orange-600 text-white active:scale-95'
          : 'bg-gray-100 text-gray-400 cursor-not-allowed'">
        {{ cart.loading ? 'Adding...' : '🛒 Add to Cart' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { useCartStore } from '@/stores/cartStore'
import axios from 'axios'

const props = defineProps({
  product: { type: Object, required: true },
})

const cart = useCartStore()
const page = usePage()
const isAuth = computed(() => !!page.props.auth?.user)
const isFavorited = ref(false)

function addToCart() {
  cart.addItem(props.product.id)
}

async function toggleFavorite() {
  try {
    const res = await axios.post('/favorites/toggle', { product_id: props.product.id })
    isFavorited.value = res.data.favorited
  } catch (e) {
    console.error(e)
  }
}
</script>
