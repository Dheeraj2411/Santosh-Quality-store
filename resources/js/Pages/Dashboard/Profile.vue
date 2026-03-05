<template>
  <UserDashboardLayout>
    <div class="space-y-6">
      <!-- Stats Cards -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div v-for="(stat, key) in stats" :key="key"
          class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 text-center">
          <div class="text-3xl font-extrabold text-orange-600">{{ stat }}</div>
          <div class="text-sm text-gray-500 mt-1 capitalize">{{ key.replace('_', ' ') }}</div>
        </div>
      </div>

      <!-- Profile Card -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-xl font-bold text-gray-900">My Profile</h2>
          <button @click="editing = !editing"
            class="text-sm text-orange-500 hover:text-orange-600 font-medium">
            {{ editing ? 'Cancel' : 'Edit Profile' }}
          </button>
        </div>

        <div v-if="!editing" class="space-y-4">
          <div class="flex items-center gap-4">
            <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center text-3xl font-bold text-orange-600">
              {{ user.name[0] }}
            </div>
            <div>
              <div class="text-xl font-bold text-gray-900">{{ user.name }}</div>
              <div class="text-gray-500 text-sm">{{ user.email }}</div>
              <div v-if="user.phone" class="text-gray-500 text-sm mt-0.5">{{ user.phone }}</div>
            </div>
          </div>
        </div>

        <form v-else @submit.prevent="updateProfile" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
            <input v-model="form.name" type="text" required
              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-500 outline-none" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
            <input v-model="form.phone" type="tel"
              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-500 outline-none" />
          </div>
          <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2.5 rounded-xl font-semibold transition">
            Save Changes
          </button>
        </form>
      </div>

      <!-- Recent Orders -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-xl font-bold text-gray-900">Recent Orders</h2>
          <Link href="/dashboard/orders" class="text-sm text-orange-500 hover:text-orange-600">View All →</Link>
        </div>
        <div v-if="recent_orders.length === 0" class="text-gray-400 text-center py-8">
          <div class="text-4xl mb-2">📦</div>
          <p>No orders yet. <Link href="/products" class="text-orange-500 underline">Start shopping!</Link></p>
        </div>
        <div v-else class="space-y-3">
          <div v-for="order in recent_orders" :key="order.id"
            class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
            <div>
              <div class="font-semibold text-gray-800 text-sm">#{{ order.order_number }}</div>
              <div class="text-xs text-gray-400 mt-0.5">{{ order.created_at }} • {{ order.items_count }} items</div>
            </div>
            <div class="flex items-center gap-3">
              <span class="text-orange-600 font-bold">₹{{ Number(order.total).toLocaleString('en-IN') }}</span>
              <span class="px-3 py-1 rounded-full text-xs font-medium capitalize"
                :class="`bg-${order.badge_color}-100 text-${order.badge_color}-700`">
                {{ order.status }}
              </span>
              <Link :href="'/dashboard/orders/' + order.id" class="text-blue-500 hover:text-blue-600 text-xs">View</Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </UserDashboardLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import UserDashboardLayout from '@/Layouts/UserDashboardLayout.vue'

const props = defineProps({
  user:          Object,
  recent_orders: Array,
  stats:         Object,
})

const editing = ref(false)
const form = ref({ name: props.user.name, phone: props.user.phone })

function updateProfile() {
  router.put('/dashboard/profile', form.value, {
    onSuccess: () => { editing.value = false },
  })
}
</script>
