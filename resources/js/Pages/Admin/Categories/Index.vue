<template>
  <AdminLayout title="Categories" subtitle="Manage product categories">
    <div class="flex flex-col sm:flex-row gap-4 justify-between mb-6">
      <Link href="/admin/categories/create"
        class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition flex items-center gap-2 self-start">
        + Add Category
      </Link>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
              <th class="text-left py-3 px-4 font-semibold text-gray-600">Category Name</th>
              <th class="text-left py-3 px-4 font-semibold text-gray-600">Products #</th>
              <th class="text-left py-3 px-4 font-semibold text-gray-600">Sort Order</th>
              <th class="text-left py-3 px-4 font-semibold text-gray-600">Status</th>
              <th class="py-3 px-4"></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="category in categories.data" :key="category.id"
              class="border-b border-gray-50 hover:bg-gray-50">
              <td class="py-3 px-4">
                <div class="font-semibold text-gray-800">{{ category.name }}</div>
                <div v-if="category.description" class="text-xs text-gray-500 truncate max-w-[250px]">{{ category.description }}</div>
              </td>
              <td class="py-3 px-4 text-gray-500 font-medium">{{ category.products_count }}</td>
              <td class="py-3 px-4 text-gray-500">{{ category.sort_order }}</td>
              <td class="py-3 px-4">
                <span :class="category.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'"
                  class="px-2 py-1 rounded-full text-xs font-semibold">
                  {{ category.is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td class="py-3 px-4">
                <div class="flex items-center gap-2">
                  <Link :href="'/admin/categories/' + category.id + '/edit'"
                    class="text-blue-500 hover:text-blue-600 text-xs font-medium">Edit</Link>
                  <button @click="confirmDelete(category)"
                    class="text-red-400 hover:text-red-500 text-xs font-medium">Delete</button>
                </div>
              </td>
            </tr>
            <tr v-if="categories.data.length === 0">
              <td colspan="5" class="py-12 text-center text-gray-500">No categories found.</td>
            </tr>
          </tbody>
        </table>
      </div>
      <!-- Pagination -->
      <div v-if="categories.last_page > 1" class="flex justify-center gap-2 p-4 border-t border-gray-100">
        <Link v-for="link in categories.links" :key="link.label" :href="link.url || '#'" v-html="link.label"
          class="px-3 py-1.5 rounded-lg text-sm border transition"
          :class="link.active ? 'bg-orange-500 text-white border-orange-500' : 'border-gray-200 text-gray-600'" />
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineProps({ categories: Object })

function confirmDelete(category) {
  if (category.products_count > 0) {
    alert(`Cannot delete "${category.name}" because it has ${category.products_count} products assigned.`)
    return
  }
  if (confirm(`Delete category "${category.name}"? This cannot be undone.`)) {
    router.delete('/admin/categories/' + category.id)
  }
}
</script>

<style scoped>
@reference "../../../../css/app.css";
</style>
