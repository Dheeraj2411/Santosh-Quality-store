<template>
  <div class="min-h-screen flex bg-gray-100 text-gray-900">
    <!-- Mobile Sidebar Backdrop -->
    <div v-show="isSidebarOpen" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-30 md:hidden" @click="isSidebarOpen = false"></div>

    <!-- Sidebar -->
    <aside :class="[isSidebarOpen ? 'translate-x-0' : '-translate-x-full', 'w-64 bg-slate-900 text-white flex flex-col min-h-screen fixed top-0 left-0 z-40 transition-transform duration-300 md:translate-x-0']">
      <!-- Logo -->
      <div class="p-6 border-b border-slate-800 flex justify-between items-center">
        <Link href="/admin" class="flex items-center gap-3">
          <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-amber-500 rounded-full flex items-center justify-center flex-shrink-0 shadow-lg">
            <span class="text-white font-bold text-lg">स</span>
          </div>
          <div>
            <div class="font-bold text-sm text-orange-400">{{ storeName }}</div>
            <div class="text-xs text-slate-400">Admin Panel</div>
          </div>
        </Link>
        <button @click="isSidebarOpen = false" class="md:hidden text-slate-400 hover:text-white p-2 rounded-lg hover:bg-slate-800 transition">
          ✕
        </button>
      </div>

      <!-- Nav -->
      <nav class="flex-1 p-4 space-y-1 overflow-y-auto" @click="isSidebarOpen = false">
        <NavItem href="/admin" :active="isActive('/admin', true)">
          <template #icon>📊</template> Dashboard
        </NavItem>
        <NavItem href="/admin/products" :active="isActive('/admin/products')">
          <template #icon>🛒</template> Products
        </NavItem>
        <NavItem href="/admin/categories" :active="isActive('/admin/categories')">
          <template #icon>📂</template> Categories
        </NavItem>
        <NavItem href="/admin/orders" :active="isActive('/admin/orders')">
          <template #icon>📦</template> Orders
        </NavItem>
        <NavItem href="/admin/pos" :active="isActive('/admin/pos')">
          <template #icon>📷</template> POS / Scanner
        </NavItem>
        <NavItem href="/admin/customers" :active="isActive('/admin/customers')">
          <template #icon>👥</template> Customers
        </NavItem>
        <NavItem href="/admin/reviews" :active="isActive('/admin/reviews')">
          <template #icon>⭐</template> Reviews
        </NavItem>
        <NavItem href="/admin/settings" :active="isActive('/admin/settings')">
          <template #icon>⚙️</template> Settings
        </NavItem>
        <NavItem href="/profile" :active="isActive('/profile')">
          <template #icon>👤</template> Profile
        </NavItem>
        <div class="pt-4 border-t border-slate-800">
          <NavItem href="/" :active="false">
            <template #icon>🏠</template> View Store
          </NavItem>
          <Link href="/logout" method="post" as="button"
            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-red-400 hover:bg-red-900/20 transition mt-1 font-medium">
            🚪 Logout
          </Link>
        </div>
      </nav>

      <!-- User info -->
      <div class="p-4 border-t border-slate-800">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 bg-gradient-to-br from-orange-500 to-amber-500 rounded-full flex items-center justify-center text-sm font-bold shadow-sm">
            {{ $page.props.auth.user?.name?.[0] ?? 'A' }}
          </div>
          <div class="overflow-hidden">
            <div class="text-sm font-medium truncate">{{ $page.props.auth.user?.name }}</div>
            <div class="text-xs text-slate-400">Administrator</div>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main content -->
    <div class="flex-1 flex flex-col min-h-screen transition-all duration-300 md:ml-64 w-full">
      <!-- Top Bar -->
      <header class="px-4 md:px-6 py-4 sticky top-0 z-20 bg-white border-b border-gray-200">
        <div class="flex items-center justify-between gap-4">
          <div class="flex items-center gap-3">
            <button @click="isSidebarOpen = true" class="md:hidden p-2 -ml-2 focus:outline-none rounded-lg transition text-gray-600 hover:text-orange-500">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
              </svg>
            </button>
            <div>
              <h1 class="text-lg md:text-xl font-bold truncate text-gray-800">{{ title }}</h1>
              <p v-if="subtitle" class="text-xs md:text-sm mt-0.5 truncate text-gray-500">{{ subtitle }}</p>
            </div>
          </div>
          <div class="flex items-center gap-3">
            <!-- Flash Messages (Desktop) -->
            <div v-if="$page.props.flash?.success || $page.props.flash?.error" class="hidden sm:flex items-center">
              <div v-if="$page.props.flash.success"
                class="px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 max-w-sm truncate bg-green-100 text-green-700">
                ✅ {{ $page.props.flash.success }}
              </div>
              <div v-if="$page.props.flash.error"
                class="px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 max-w-sm truncate bg-red-100 text-red-700">
                ❌ {{ $page.props.flash.error }}
              </div>
            </div>
          </div>
        </div>

        <!-- Mobile Flash Message Row -->
        <div v-if="$page.props.flash?.success || $page.props.flash?.error" class="mt-3 sm:hidden">
          <div v-if="$page.props.flash.success"
            class="px-3 py-2 rounded text-xs font-medium flex items-center gap-2 bg-green-100 text-green-700">
            ✅ {{ $page.props.flash.success }}
          </div>
          <div v-if="$page.props.flash.error"
            class="px-3 py-2 rounded text-xs font-medium flex items-center gap-2 bg-red-100 text-red-700">
            ❌ {{ $page.props.flash.error }}
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="flex-1 p-4 md:p-6 overflow-x-hidden">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

defineProps({
  title:    { type: String, default: 'Admin' },
  subtitle: { type: String, default: '' },
})

const page = usePage()
const isSidebarOpen = ref(false)
const storeName = computed(() => page.props.settings?.store_name || 'Santosh Store')

function isActive(path, exact = false) {
  const current = page.url
  return exact ? current === path : current.startsWith(path)
}
</script>

<script>
// NavItem sub-component
import { defineComponent, h } from 'vue'
import { Link } from '@inertiajs/vue3'

const NavItem = defineComponent({
  props: {
    href:   { type: String, required: true },
    active: { type: Boolean, default: false },
  },
  setup(props, { slots }) {
    return () => h(
      Link,
      {
        href: props.href,
        class: [
          'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition font-medium',
          props.active
            ? 'bg-gradient-to-r from-orange-500 to-amber-500 text-white shadow-sm'
            : 'text-slate-400 hover:bg-slate-800 hover:text-white',
        ],
      },
      () => [
        h('span', { class: 'text-base' }, slots.icon?.()),
        slots.default?.(),
      ]
    )
  },
})

export default { components: { NavItem } }
</script>
