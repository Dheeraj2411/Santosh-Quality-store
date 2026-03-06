<template>
  <AdminLayout title="Store Settings" subtitle="Manage store information and configuration">
    <div class="max-w-2xl rounded-2xl shadow-sm p-8 bg-white border border-gray-100">
      <form @submit.prevent="save" class="space-y-5">
        <div>
          <label class="block text-sm font-semibold mb-1.5 text-gray-700">Store Name *</label>
          <input v-model="form.store_name" type="text" required
            class="w-full rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 outline-none transition border border-gray-200 text-gray-900" />
        </div>
        <div>
          <label class="block text-sm font-semibold mb-1.5 text-gray-700">Tagline</label>
          <input v-model="form.store_tagline" type="text"
            class="w-full rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 outline-none transition border border-gray-200 text-gray-900" />
        </div>
        <div>
          <label class="block text-sm font-semibold mb-1.5 text-gray-700">Address</label>
          <textarea v-model="form.address" rows="2"
            class="w-full rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 outline-none resize-none transition border border-gray-200 text-gray-900"></textarea>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <div>
            <label class="block text-sm font-semibold mb-1.5 text-gray-700">Phone</label>
            <input v-model="form.phone" type="text"
              class="w-full rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 outline-none transition border border-gray-200 text-gray-900" />
          </div>
          <div>
            <label class="block text-sm font-semibold mb-1.5 text-gray-700">Email</label>
            <input v-model="form.email" type="email"
              class="w-full rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 outline-none transition border border-gray-200 text-gray-900" />
          </div>
          <div>
            <label class="block text-sm font-semibold mb-1.5 text-gray-700">Opening Time</label>
            <input v-model="form.open_time" type="time"
              class="w-full rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 outline-none transition border border-gray-200 text-gray-900" />
          </div>
          <div>
            <label class="block text-sm font-semibold mb-1.5 text-gray-700">Closing Time</label>
            <input v-model="form.close_time" type="time"
              class="w-full rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 outline-none transition border border-gray-200 text-gray-900" />
          </div>
          <div>
            <label class="block text-sm font-semibold mb-1.5 text-gray-700">Free Delivery Above (₹)</label>
            <input v-model="form.delivery_free_above" type="number"
              class="w-full rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 outline-none transition border border-gray-200 text-gray-900" />
          </div>
          <div>
            <label class="block text-sm font-semibold mb-1.5 text-gray-700">Delivery Charge (₹)</label>
            <input v-model="form.delivery_charge" type="number"
              class="w-full rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 outline-none transition border border-gray-200 text-gray-900" />
          </div>
        </div>
        <div>
          <label class="block text-sm font-semibold mb-1.5 text-gray-700">About Text</label>
          <textarea v-model="form.about_text" rows="4"
            class="w-full rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 outline-none resize-none transition border border-gray-200 text-gray-900"></textarea>
        </div>
        <button type="submit"
          class="bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white px-8 py-3 rounded-xl font-bold transition shadow-lg hover:shadow-xl hover:-translate-y-0.5 duration-200">
          💾 Save Settings
        </button>
      </form>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({ settings: Object })

const form = ref({
  store_name:          props.settings?.store_name || '',
  store_tagline:       props.settings?.store_tagline || '',
  address:             props.settings?.address || '',
  phone:               props.settings?.phone || '',
  email:               props.settings?.email || '',
  open_time:           props.settings?.open_time || '06:00',
  close_time:          props.settings?.close_time || '22:00',
  delivery_free_above: props.settings?.delivery_free_above || '500',
  delivery_charge:     props.settings?.delivery_charge || '40',
  about_text:          props.settings?.about_text || '',
})

function save() {
  router.put('/admin/settings', form.value)
}
</script>
