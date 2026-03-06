<template>
  <AdminLayout title="Analytics Dashboard" :subtitle="'Welcome to the ' + storeName + ' admin panel'">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5 mb-8">
      <StatCard label="Total Users"    :value="stats.total_users"    icon="👥" color="blue" />
      <StatCard label="Products"       :value="stats.total_products" icon="🛒" color="green" />
      <StatCard label="Total Orders"   :value="stats.total_orders"   icon="📦" color="purple" />
      <StatCard label="Pending Orders" :value="stats.pending_orders" icon="⏳" color="yellow" />
      <StatCard label="Revenue (₹)"    :value="'₹' + Number(stats.total_revenue).toLocaleString('en-IN')" icon="💰" color="orange" />
    </div>

    <!-- Revenue Chart (Simple Bar) -->
    <div class="rounded-2xl shadow-sm border p-6 mb-8 bg-white border-gray-100">
      <h2 class="font-bold text-lg mb-5 text-gray-900">📈 Revenue — Last 7 Days</h2>
      <div class="flex items-end gap-3 h-40">
        <div v-for="day in revenue_chart" :key="day.date" class="flex-1 flex flex-col items-center gap-2">
          <div class="w-full bg-orange-50 rounded-t-lg relative overflow-hidden"
            :style="{ height: Math.max(4, (day.revenue / maxRevenue) * 120) + 'px' }">
            <div class="absolute inset-0 bg-orange-400 rounded-t-lg"></div>
          </div>
          <span class="text-xs text-gray-500">{{ day.date }}</span>
          <span class="text-xs font-semibold text-orange-600">₹{{ day.revenue }}</span>
        </div>
      </div>
    </div>

    <!-- Recent Orders -->
    <div class="rounded-2xl shadow-sm border p-6 bg-white border-gray-100">
      <div class="flex items-center justify-between mb-5">
        <h2 class="font-bold text-lg text-gray-900">📦 Recent Orders</h2>
        <Link href="/admin/orders" class="text-sm font-medium transition text-orange-500 hover:text-orange-600">View All →</Link>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-gray-100">
              <th class="text-left py-3 px-2 font-semibold text-gray-600">Order</th>
              <th class="text-left py-3 px-2 font-semibold text-gray-600">Customer</th>
              <th class="text-left py-3 px-2 font-semibold text-gray-600">Amount</th>
              <th class="text-left py-3 px-2 font-semibold text-gray-600">Status</th>
              <th class="text-left py-3 px-2 font-semibold text-gray-600">Date</th>
              <th class="py-3 px-2"></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in recent_orders" :key="order.id" class="border-b transition border-gray-50 hover:bg-gray-50 text-gray-900">
              <td class="py-3 px-2 font-medium text-gray-900">#{{ order.order_number }}</td>
              <td class="py-3 px-2 text-gray-600">{{ order.user_name }}</td>
              <td class="py-3 px-2 font-bold text-orange-600">₹{{ Number(order.total).toLocaleString('en-IN') }}</td>
              <td class="py-3 px-2">
                <span class="px-2 py-1 rounded-full text-xs font-semibold capitalize"
                  :class="{
                    'bg-yellow-100 text-yellow-700': order.status === 'pending',
                    'bg-blue-100 text-blue-700': order.status === 'processing',
                    'bg-green-100 text-green-700': order.status === 'delivered',
                    'bg-red-100 text-red-700': order.status === 'cancelled',
                  }">{{ order.status }}</span>
              </td>
              <td class="py-3 px-2 text-xs text-gray-400">{{ order.created_at }}</td>
              <td class="py-3 px-2">
                <Link :href="'/admin/orders/' + order.id" class="text-xs font-medium transition text-blue-500 hover:text-blue-600">View</Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed, h } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const storeName = computed(() => usePage().props.settings?.store_name || 'Santosh Store')

const props = defineProps({
  stats:          Object,
  recent_orders:  Array,
  revenue_chart:  Array,
})

const maxRevenue = computed(() => {
  const max = Math.max(...(props.revenue_chart?.map(d => d.revenue) ?? [1]))
  return max === 0 ? 1 : max
})
</script>

<script>
import { defineComponent } from 'vue'

const colorMap = {
  blue:   'bg-blue-50 text-blue-600',
  green:  'bg-green-50 text-green-600',
  purple: 'bg-purple-50 text-purple-600',
  yellow: 'bg-yellow-50 text-yellow-600',
  orange: 'bg-orange-50 text-orange-600',
}

const StatCard = defineComponent({
  props: {
    label: String, value: [String, Number], icon: String, color: String,
  },
  setup(props) {
    return () => h('div', {
      class: 'rounded-2xl p-5 shadow-sm border bg-white border-gray-100'
    }, [
      h('div', {
        class: `w-12 h-12 rounded-xl flex items-center justify-center text-xl mb-3 ${colorMap[props.color] || 'bg-gray-50 text-gray-600'}`
      }, props.icon),
      h('div', { class: 'text-2xl font-extrabold mb-1 text-gray-900' }, String(props.value)),
      h('div', { class: 'text-sm text-gray-400' }, props.label),
    ])
  },
})

export default { components: { StatCard } }
</script>
