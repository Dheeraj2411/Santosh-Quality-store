<template>
  <AdminLayout title="Review Moderation" subtitle="Approve or remove customer reviews">
    <!-- Filter -->
    <div class="flex gap-3 mb-6">
      <Link href="/admin/reviews" class="px-4 py-2 rounded-xl text-sm font-medium transition"
        :class="!filters?.status ? 'bg-orange-500 text-white' : 'bg-white border border-gray-200 text-gray-600'">All</Link>
      <Link href="/admin/reviews?status=pending" class="px-4 py-2 rounded-xl text-sm font-medium transition"
        :class="filters?.status === 'pending' ? 'bg-orange-500 text-white' : 'bg-white border border-gray-200 text-gray-600'">Pending</Link>
      <Link href="/admin/reviews?status=approved" class="px-4 py-2 rounded-xl text-sm font-medium transition"
        :class="filters?.status === 'approved' ? 'bg-orange-500 text-white' : 'bg-white border border-gray-200 text-gray-600'">Approved</Link>
    </div>

    <div class="space-y-4">
      <div v-if="reviews.data.length === 0" class="text-center py-16 text-gray-400">
        <div class="text-5xl mb-3">⭐</div>
        <p>No reviews to display</p>
      </div>
      <div v-for="review in reviews.data" :key="review.id"
        class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-start justify-between gap-4">
          <div class="flex-1">
            <div class="flex items-center gap-3 mb-2">
              <span class="font-semibold text-gray-800">{{ review.user_name }}</span>
              <div class="text-yellow-400 text-sm">{{ '⭐'.repeat(review.rating) }}</div>
              <span :class="review.is_approved ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'"
                class="text-xs font-semibold px-2 py-0.5 rounded-full">
                {{ review.is_approved ? 'Approved' : 'Pending' }}
              </span>
            </div>
            <p class="text-xs text-gray-400 mb-2">Product: {{ review.product_name }}</p>
            <p v-if="review.title" class="font-medium text-sm text-gray-800">{{ review.title }}</p>
            <p class="text-gray-600 text-sm mt-1">{{ review.body }}</p>
            <p class="text-xs text-gray-400 mt-2">{{ review.created_at }}</p>
          </div>
          <div class="flex flex-col gap-2 flex-shrink-0">
            <button v-if="!review.is_approved" @click="approve(review.id)"
              class="bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition">
              Approve
            </button>
            <button @click="deleteReview(review.id)"
              class="bg-red-50 hover:bg-red-100 text-red-600 px-3 py-1.5 rounded-lg text-xs font-medium transition">
              Delete
            </button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({ reviews: Object, filters: Object })

function approve(id) {
  router.post('/admin/reviews/' + id + '/approve', {}, { preserveScroll: true })
}
function deleteReview(id) {
  if (confirm('Delete this review permanently?')) {
    router.delete('/admin/reviews/' + id, { preserveScroll: true })
  }
}
</script>
