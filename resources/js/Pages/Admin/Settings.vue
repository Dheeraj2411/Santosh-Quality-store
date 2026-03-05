<template>
  <AdminLayout title="Store Settings" subtitle="Manage store information and configuration">
    <div class="max-w-2xl bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
      <form @submit.prevent="save" class="space-y-5">
        <div>
          <label class="label">Store Name *</label>
          <input v-model="form.store_name" type="text" required class="input" />
        </div>
        <div>
          <label class="label">Tagline</label>
          <input v-model="form.store_tagline" type="text" class="input" />
        </div>
        <div>
          <label class="label">Address</label>
          <textarea v-model="form.address" rows="2" class="input resize-none"></textarea>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <div>
            <label class="label">Phone</label>
            <input v-model="form.phone" type="text" class="input" />
          </div>
          <div>
            <label class="label">Email</label>
            <input v-model="form.email" type="email" class="input" />
          </div>
          <div>
            <label class="label">Opening Time</label>
            <input v-model="form.open_time" type="time" class="input" />
          </div>
          <div>
            <label class="label">Closing Time</label>
            <input v-model="form.close_time" type="time" class="input" />
          </div>
          <div>
            <label class="label">Free Delivery Above (₹)</label>
            <input v-model="form.delivery_free_above" type="number" class="input" />
          </div>
          <div>
            <label class="label">Delivery Charge (₹)</label>
            <input v-model="form.delivery_charge" type="number" class="input" />
          </div>
        </div>
        <div>
          <label class="label">About Text</label>
          <textarea v-model="form.about_text" rows="4" class="input resize-none"></textarea>
        </div>
        <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-8 py-3 rounded-xl font-bold transition">
          Save Settings
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

<style scoped>
@reference "../../../css/app.css";
.label { @apply block text-sm font-semibold text-gray-700 mb-1.5; }
.input { @apply w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 outline-none; }
</style>
