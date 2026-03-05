<template>
  <GuestLayout>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
      <h1 class="text-4xl font-extrabold text-gray-900 mb-2">About Us</h1>
      <p class="text-orange-500 font-medium text-lg mb-8">संतोष स्टोर • Santosh Store</p>

      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-6">
        <p class="text-gray-600 text-lg leading-relaxed">
          {{ settings?.about_text || 'Santosh Store has been serving the residents of Kirti Nagar and Sector 15, Gurugram since 1995. We pride ourselves on offering fresh, high-quality groceries at the best prices. Our store provides in-store shopping, pickup, and home delivery services.' }}
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
          <h2 class="font-bold text-gray-900 text-lg mb-4">📍 Location</h2>
          <p class="text-gray-600 text-sm">Kirti Nagar, Sector 15 Part 1<br>Sector 15, Gurugram<br>Haryana 122001, India</p>
          <p class="text-gray-400 text-xs mt-1">Plus Code: F23P+8X Gurugram</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
          <h2 class="font-bold text-gray-900 text-lg mb-4">🕐 Business Hours</h2>
          <div class="space-y-2 text-sm text-gray-600">
            <div class="flex justify-between"><span>Monday – Sunday</span><span class="font-bold text-gray-800">6:00 AM – 10:00 PM</span></div>
          </div>
          <p class="mt-3 text-sm font-bold text-green-600">🟢 Open Today</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
          <h2 class="font-bold text-gray-900 text-lg mb-4">📞 Contact</h2>
          <p class="text-gray-600 text-sm">
            Phone: <a href="tel:+919650671568" class="text-orange-500 font-semibold hover:underline">+91 96506 71568</a>
          </p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
          <h2 class="font-bold text-gray-900 text-lg mb-4">⭐ Rating</h2>
          <div class="text-3xl text-yellow-400">⭐⭐⭐⭐</div>
          <p class="text-2xl font-extrabold text-gray-900 mt-1">4.0 <span class="text-sm font-normal text-gray-400">/ 5.0</span></p>
          <p class="text-gray-400 text-sm">Based on 35 reviews</p>
        </div>
      </div>
    </div>
  </GuestLayout>
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'
defineProps({ settings: Object })
</script>
