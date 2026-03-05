<template>
  <AdminLayout title="Edit Category" subtitle="Update product categorisation">
    <div class="max-w-2xl bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
      <form @submit.prevent="submit" enctype="multipart/form-data" class="space-y-6">
        <div>
          <label class="label">Category Name *</label>
          <input v-model="form.name" type="text" required class="input" placeholder="e.g. Rice & Grains" />
        </div>
        <div>
          <label class="label">Description</label>
          <textarea v-model="form.description" rows="3" class="input resize-none"
            placeholder="Category description..."></textarea>
        </div>
        <div>
          <label class="label">Sort Order</label>
          <input v-model="form.sort_order" type="number" min="0" class="input" placeholder="0" />
          <p class="text-xs text-gray-400 mt-1">Lower numbers appear first in the menu</p>
        </div>
        <div>
          <label class="label">Category Image</label>
          <div v-if="category.image" class="mb-3">
            <img :src="'/storage/' + category.image" class="w-24 h-24 object-cover rounded-xl border border-gray-200 shadow-sm" />
          </div>
          <input @change="e => form.image = e.target.files[0]" type="file" accept="image/*"
            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100" />
        </div>
        <div>
          <label class="flex items-center gap-2 cursor-pointer">
            <input v-model="form.is_active" type="checkbox" :checked="form.is_active" class="rounded text-orange-500 focus:ring-orange-500 w-4 h-4" />
            <span class="text-sm font-medium text-gray-700">Active</span>
          </label>
        </div>

        <div class="flex gap-4 pt-4 border-t border-gray-100">
          <button type="submit" :disabled="submitting"
            class="bg-orange-500 hover:bg-orange-600 text-white px-8 py-3 rounded-xl font-bold transition disabled:opacity-70">
            {{ submitting ? 'Updating...' : 'Update Category' }}
          </button>
          <Link href="/admin/categories" class="border border-gray-200 text-gray-600 px-6 py-3 rounded-xl font-medium hover:bg-gray-50 transition text-sm flex items-center">
            Cancel
          </Link>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({ category: Object })
const submitting = ref(false)

const form = ref({
  name: props.category.name,
  description: props.category.description || '',
  sort_order: props.category.sort_order || 0,
  is_active: Boolean(props.category.is_active),
  image: null,
})

function submit() {
  submitting.value = true
  const data = new FormData()
  data.append('_method', 'PUT')
  
  Object.entries(form.value).forEach(([key, val]) => {
    if (val !== null && val !== undefined) {
      data.append(key, val)
    }
  })
  
  router.post(`/admin/categories/${props.category.id}`, data, {
    onFinish: () => { submitting.value = false },
    forceFormData: true
  })
}
</script>

<style scoped>
@reference "../../../../css/app.css";
.label { @apply block text-sm font-semibold text-gray-700 mb-1.5; }
.input { @apply w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 outline-none; }
</style>
