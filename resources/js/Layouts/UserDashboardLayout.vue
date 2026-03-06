<template>
  <GuestLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
      <div class="flex flex-col md:flex-row gap-8">
        <!-- Sidebar -->
        <aside class="w-full md:w-64 flex-shrink-0">
          <div class="rounded-2xl shadow-sm overflow-hidden bg-white border border-gray-100">
            <!-- Profile Header -->
            <div class="bg-gradient-to-br from-orange-500 to-amber-500 p-6 text-white text-center">
              <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3 text-3xl font-bold backdrop-blur-sm">
                {{ $page.props.auth.user?.name?.[0] }}
              </div>
              <div class="font-bold text-lg">{{ $page.props.auth.user?.name }}</div>
              <div class="text-white/80 text-sm mt-1">{{ $page.props.auth.user?.email }}</div>
            </div>

            <!-- Nav Links -->
            <nav class="p-3 space-y-1">
              <SidebarLink href="/dashboard" :active="isActive('/dashboard', true)">
                👤 My Profile
              </SidebarLink>
              <SidebarLink href="/profile" :active="isActive('/profile')">
                ⚙️ Account Settings
              </SidebarLink>
              <SidebarLink href="/dashboard/orders" :active="isActive('/dashboard/orders')">
                📦 My Orders
              </SidebarLink>
              <SidebarLink href="/dashboard/favorites" :active="isActive('/dashboard/favorites')">
                ❤️ Favorites
              </SidebarLink>
              <SidebarLink href="/dashboard/addresses" :active="isActive('/dashboard/addresses')">
                📍 Addresses
              </SidebarLink>
              <SidebarLink href="/dashboard/reviews" :active="isActive('/dashboard/reviews')">
                ⭐ My Reviews
              </SidebarLink>
              <div class="pt-2 mt-2 border-t border-gray-100">
                <Link href="/logout" method="post" as="button"
                  class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-red-500 rounded-xl transition hover:bg-red-50">
                  🚪 Logout
                </Link>
              </div>
            </nav>
          </div>
        </aside>

        <!-- Content -->
        <main class="flex-1 min-w-0">
          <!-- Flash Messages -->
          <div v-if="$page.props.flash?.success || $page.props.flash?.error" class="mb-6">
            <div v-if="$page.props.flash.success"
              class="px-5 py-3 rounded-xl flex items-center gap-2 bg-green-50 border border-green-200 text-green-700">
              ✅ {{ $page.props.flash.success }}
            </div>
            <div v-if="$page.props.flash.error"
              class="px-5 py-3 rounded-xl flex items-center gap-2 bg-red-50 border border-red-200 text-red-700">
              ❌ {{ $page.props.flash.error }}
            </div>
          </div>

          <slot />
        </main>
      </div>
    </div>
  </GuestLayout>
</template>

<script setup>
import { h } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/GuestLayout.vue'

const page = usePage()

function isActive(path, exact = false) {
  const current = page.url
  return exact ? current === path : current.startsWith(path)
}
</script>

<script>
import { defineComponent } from 'vue'
import { Link } from '@inertiajs/vue3'

const SidebarLink = defineComponent({
  props: {
    href:   String,
    active: { type: Boolean, default: false },
  },
  setup(props, { slots }) {
    return () => h(
      Link,
      {
        href: props.href,
        class: [
          'flex items-center gap-2 px-4 py-2.5 text-sm rounded-xl transition font-medium',
          props.active
            ? 'bg-gradient-to-r from-orange-500 to-amber-500 text-white shadow-sm'
            : 'text-gray-600 hover:bg-orange-50 hover:text-orange-600',
        ],
      },
      slots.default
    )
  },
})

export default { components: { SidebarLink } }
</script>
