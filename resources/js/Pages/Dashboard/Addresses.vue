<template>
  <UserDashboardLayout>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-gray-900">My Addresses</h2>
        <button @click="showForm = !showForm"
          class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-xl text-sm font-medium transition">
          + Add New Address
        </button>
      </div>

      <!-- Add Address Form -->
      <div v-if="showForm" class="bg-orange-50 rounded-2xl p-6 mb-6 border border-orange-100">
        <h3 class="font-semibold text-gray-800 mb-4">New Address</h3>
        <form @submit.prevent="saveAddress" class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Label</label>
            <select v-model="form.label" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-500 outline-none text-sm">
              <option>Home</option><option>Work</option><option>Other</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
            <input v-model="form.name" type="text" required class="input" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
            <input v-model="form.phone" type="tel" required class="input" />
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 1</label>
            <input v-model="form.line1" type="text" required class="input" />
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 2 (Optional)</label>
            <input v-model="form.line2" type="text" class="input" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
            <input v-model="form.city" type="text" required class="input" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">State</label>
            <input v-model="form.state" type="text" required class="input" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Pincode</label>
            <input v-model="form.pincode" type="text" required class="input" />
          </div>
          <div class="flex items-end gap-3">
            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2.5 rounded-xl font-semibold transition text-sm">Save Address</button>
            <button type="button" @click="showForm = false" class="text-sm text-gray-500 hover:text-gray-700">Cancel</button>
          </div>
        </form>
      </div>

      <!-- Address List -->
      <div v-if="addresses.length === 0 && !showForm" class="text-center py-10 text-gray-400">
        <div class="text-4xl mb-3">📍</div>
        <p>No addresses saved yet. Add one to enable delivery orders.</p>
      </div>
      <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div v-for="addr in addresses" :key="addr.id"
          class="border rounded-2xl p-5 transition"
          :class="addr.is_default ? 'border-orange-300 bg-orange-50' : 'border-gray-100 hover:border-gray-200'">
          <div class="flex items-start justify-between gap-2">
            <div>
              <div class="flex items-center gap-2 mb-2">
                <span class="bg-orange-100 text-orange-700 text-xs font-bold px-2 py-0.5 rounded-full">{{ addr.label }}</span>
                <span v-if="addr.is_default" class="bg-green-100 text-green-700 text-xs font-bold px-2 py-0.5 rounded-full">Default</span>
              </div>
              <p class="font-semibold text-gray-800 text-sm">{{ addr.name }}</p>
              <p class="text-gray-500 text-xs mt-1">{{ addr.line1 }}, {{ addr.line2 ? addr.line2 + ', ' : '' }}{{ addr.city }}, {{ addr.state }} {{ addr.pincode }}</p>
              <p class="text-gray-500 text-xs mt-0.5">📞 {{ addr.phone }}</p>
            </div>
          </div>
          <div class="flex items-center gap-2 mt-4">
            <button v-if="!addr.is_default" @click="setDefault(addr.id)"
              class="text-xs text-orange-500 hover:text-orange-600 font-medium">Set Default</button>
            <Link :href="'/addresses/' + addr.id" method="delete" as="button"
              class="text-xs text-red-400 hover:text-red-500 font-medium ml-auto">
              Delete
            </Link>
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

const props = defineProps({ addresses: Array })

const showForm = ref(false)
const form = ref({ label: 'Home', name: '', phone: '', line1: '', line2: '', city: '', state: '', pincode: '' })

function saveAddress() {
  router.post('/addresses', form.value, {
    onSuccess: () => {
      showForm.value = false
      form.value = { label: 'Home', name: '', phone: '', line1: '', line2: '', city: '', state: '', pincode: '' }
    }
  })
}

function setDefault(id) {
  router.post('/addresses/' + id + '/default')
}
</script>

<style scoped>
@reference "../../../css/app.css";
.input {
  @apply w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-500 outline-none text-sm;
}
</style>
