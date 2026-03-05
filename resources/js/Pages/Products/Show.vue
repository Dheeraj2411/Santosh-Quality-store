<template>
  <GuestLayout>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Breadcrumb -->
      <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <Link href="/">Home</Link>
        <span>/</span>
        <Link href="/products">Products</Link>
        <span>/</span>
        <Link :href="'/products?category_id=' + product.category?.id">{{ product.category?.name }}</Link>
        <span>/</span>
        <span class="text-gray-900 font-medium truncate">{{ product.name }}</span>
      </nav>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
        <!-- Images -->
        <div>
          <div class="rounded-2xl overflow-hidden bg-gray-100 mb-4 aspect-square">
            <img :src="activeImage" :alt="product.name" class="w-full h-full object-contain p-4" />
          </div>
          <div v-if="product.images?.length > 0" class="flex gap-3 flex-wrap">
            <button v-for="img in product.images" :key="img.id"
              @click="activeImage = img.url"
              class="w-20 h-20 rounded-xl overflow-hidden border-2 transition"
              :class="activeImage === img.url ? 'border-orange-500' : 'border-gray-200 hover:border-gray-300'">
              <img :src="img.url" :alt="product.name" class="w-full h-full object-cover" />
            </button>
          </div>
        </div>

        <!-- Details -->
        <div>
          <div class="flex items-start justify-between gap-4 mb-2">
            <div>
              <Link v-if="product.category" :href="'/products?category_id=' + product.category.id"
                class="text-xs text-orange-500 font-semibold uppercase tracking-widest">
                {{ product.category.name }}
              </Link>
              <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 mt-1">{{ product.name }}</h1>
            </div>
            <button v-if="isAuth" @click="toggleFavorite"
              class="flex-shrink-0 p-3 bg-gray-100 rounded-full hover:bg-red-50 hover:text-red-500 transition text-xl">
              {{ isFavorited ? '❤️' : '🤍' }}
            </button>
          </div>

          <!-- Rating -->
          <div v-if="product.reviews?.length > 0" class="flex items-center gap-2 mb-4">
            <div class="flex text-yellow-400">{{ '⭐'.repeat(Math.round(product.avg_rating)) }}</div>
            <span class="text-sm text-gray-500">({{ product.reviews.length }} reviews)</span>
          </div>

          <!-- Price -->
          <div class="flex items-center gap-3 mb-4">
            <span class="text-4xl font-extrabold text-orange-600">₹{{ product.price }}</span>
            <div v-if="product.mrp && product.mrp > product.price">
              <span class="text-xl text-gray-400 line-through">₹{{ product.mrp }}</span>
              <span class="ml-2 bg-red-100 text-red-600 text-sm font-bold px-2 py-0.5 rounded-lg">
                {{ product.discount }}% OFF
              </span>
            </div>
          </div>
          <p class="text-sm text-gray-500 mb-4">Unit: <strong>{{ product.unit }}</strong></p>

          <!-- Stock -->
          <div class="mb-5">
            <span v-if="product.in_stock"
              class="inline-flex items-center gap-2 bg-green-50 text-green-700 px-4 py-2 rounded-full text-sm font-semibold">
              ✅ In Stock ({{ product.stock }} available)
            </span>
            <span v-else class="inline-flex items-center gap-2 bg-red-50 text-red-600 px-4 py-2 rounded-full text-sm font-semibold">
              ❌ Out of Stock
            </span>
          </div>

          <!-- Quantity & Add to Cart -->
          <div v-if="product.in_stock" class="flex items-center gap-4 mb-6">
            <div class="flex items-center gap-3 bg-gray-100 rounded-xl px-3 py-2">
              <button @click="qty = Math.max(1, qty - 1)" class="w-8 h-8 flex items-center justify-center hover:bg-gray-200 rounded-lg font-bold text-lg">−</button>
              <span class="w-8 text-center font-bold">{{ qty }}</span>
              <button @click="qty++" class="w-8 h-8 flex items-center justify-center hover:bg-gray-200 rounded-lg font-bold text-lg">+</button>
            </div>
            <button @click="addToCart"
              class="flex-1 bg-orange-500 hover:bg-orange-600 active:scale-95 text-white py-3 rounded-xl font-bold text-lg transition">
              🛒 Add to Cart
            </button>
          </div>

          <!-- Description -->
          <div v-if="product.description" class="bg-gray-50 rounded-2xl p-5 mb-6">
            <h3 class="font-bold text-gray-900 mb-2">Product Description</h3>
            <p class="text-gray-600 text-sm leading-relaxed">{{ product.description }}</p>
          </div>

          <!-- Store info -->
          <div class="flex items-center gap-3 text-sm text-gray-500 border-t pt-4">
            <span>🚚 Free delivery on orders ₹500+</span>
            <span>•</span>
            <span>📞 <a href="tel:+919650671568" class="text-orange-500 hover:underline">+91 96506 71568</a></span>
          </div>
        </div>
      </div>

      <!-- Reviews -->
      <div class="mt-14">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Customer Reviews</h2>

        <!-- Review Form (logged in users) -->
        <div v-if="isAuth" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-8">
          <h3 class="font-semibold text-gray-800 mb-4">Write a Review</h3>
          <form @submit.prevent="submitReview" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Rating</label>
              <div class="flex gap-2">
                <button v-for="star in 5" :key="star" type="button"
                  @click="reviewForm.rating = star"
                  class="text-3xl transition-transform hover:scale-110"
                  :class="star <= reviewForm.rating ? 'text-yellow-400' : 'text-gray-200'">⭐</button>
              </div>
            </div>
            <input v-model="reviewForm.title" type="text" placeholder="Review title (optional)"
              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 outline-none" />
            <textarea v-model="reviewForm.body" rows="3" placeholder="Share your experience..."
              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 outline-none resize-none"></textarea>
            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition">
              Submit Review
            </button>
          </form>
        </div>

        <!-- Reviews List -->
        <div v-if="product.reviews?.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <div v-for="review in product.reviews" :key="review.id"
            class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-start gap-3">
              <img :src="review.user_avatar" :alt="review.user_name" class="w-10 h-10 rounded-full object-cover flex-shrink-0" />
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-2">
                  <span class="font-semibold text-gray-800 text-sm">{{ review.user_name }}</span>
                  <span class="text-xs text-gray-400">{{ review.created_at }}</span>
                </div>
                <div class="text-yellow-400 text-sm mt-0.5">{{ '⭐'.repeat(review.rating) }}</div>
                <p v-if="review.title" class="font-medium text-sm mt-1">{{ review.title }}</p>
                <p class="text-gray-600 text-sm mt-1 leading-relaxed">{{ review.body }}</p>
              </div>
            </div>
          </div>
        </div>
        <div v-else class="text-gray-400 text-center py-10">
          No reviews yet. Be the first to review!
        </div>
      </div>

      <!-- Related Products -->
      <div v-if="related?.length > 0" class="mt-14">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Related Products</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
          <ProductCard v-for="p in related" :key="p.id" :product="p" />
        </div>
      </div>
    </div>
  </GuestLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'
import { useCartStore } from '@/stores/cartStore'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import ProductCard from '@/Components/ProductCard.vue'
import axios from 'axios'

const props = defineProps({
  product: Object,
  related: Array,
})

const cart = useCartStore()
const page = usePage()
const isAuth = computed(() => !!page.props.auth?.user)

const activeImage = ref(props.product.thumbnail)
const qty = ref(1)
const isFavorited = ref(false)

const reviewForm = ref({
  rating: 5,
  title:  '',
  body:   '',
})

function addToCart() {
  cart.addItem(props.product.id, qty.value)
}

async function toggleFavorite() {
  try {
    const res = await axios.post('/favorites/toggle', { product_id: props.product.id })
    isFavorited.value = res.data.favorited
  } catch (e) {
    console.error(e)
  }
}

function submitReview() {
  router.post('/reviews', {
    product_id: props.product.id,
    rating:     reviewForm.value.rating,
    title:      reviewForm.value.title,
    body:       reviewForm.value.body,
  }, {
    onSuccess: () => {
      reviewForm.value = { rating: 5, title: '', body: '' }
    }
  })
}
</script>
