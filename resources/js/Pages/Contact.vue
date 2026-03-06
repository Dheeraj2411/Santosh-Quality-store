<template>
  <GuestLayout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
      <div class="text-center mb-12">
        <h1 class="text-4xl font-extrabold mb-3 text-gray-900">Contact Us</h1>
        <p class="text-gray-500 text-lg">We'd love to hear from you</p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
        <!-- Form -->
        <div class="rounded-2xl shadow-sm p-6 sm:p-8 bg-white border border-gray-100">
          <h2 class="font-bold text-xl mb-6 text-gray-900">Send a Message</h2>
          <div v-if="success" class="px-5 py-3 rounded-xl mb-4 text-sm bg-green-50 border border-green-200 text-green-700">
            ✅ {{ success }}
          </div>
          <form @submit.prevent="submit" class="space-y-4">
            <div>
              <label class="block text-sm font-semibold mb-1.5 text-gray-700">Your Name *</label>
              <input v-model="form.name" type="text" required placeholder="Amit Sharma"
                class="w-full rounded-xl px-4 py-2.5 text-sm transition outline-none border border-gray-200 focus:ring-2 focus:ring-orange-500 focus:border-orange-500" />
            </div>
            <div>
              <label class="block text-sm font-semibold mb-1.5 text-gray-700">Email Address *</label>
              <input v-model="form.email" type="email" required placeholder="amit@example.com"
                class="w-full rounded-xl px-4 py-2.5 text-sm transition outline-none border border-gray-200 focus:ring-2 focus:ring-orange-500 focus:border-orange-500" />
            </div>
            <div>
              <label class="block text-sm font-semibold mb-1.5 text-gray-700">Subject *</label>
              <input v-model="form.subject" type="text" required placeholder="Order inquiry, delivery, etc."
                class="w-full rounded-xl px-4 py-2.5 text-sm transition outline-none border border-gray-200 focus:ring-2 focus:ring-orange-500 focus:border-orange-500" />
            </div>
            <div>
              <label class="block text-sm font-semibold mb-1.5 text-gray-700">Message *</label>
              <textarea v-model="form.message" rows="5" required placeholder="How can we help you?"
                class="w-full rounded-xl px-4 py-2.5 text-sm transition outline-none resize-none border border-gray-200 focus:ring-2 focus:ring-orange-500 focus:border-orange-500"></textarea>
            </div>
            <button type="submit" :disabled="sending"
              class="w-full py-3.5 rounded-xl font-bold text-lg text-white transition shadow-lg hover:shadow-xl hover:-translate-y-0.5 duration-200 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 disabled:opacity-70">
              {{ sending ? 'Sending...' : '📩 Send Message' }}
            </button>
          </form>
        </div>

        <!-- Info -->
        <div class="space-y-6">
          <div class="rounded-2xl shadow-sm p-6 bg-white border border-gray-100">
            <div class="text-3xl mb-3">📍</div>
            <h3 class="font-bold mb-2 text-gray-900">Visit Our Store</h3>
            <p class="text-sm leading-relaxed text-gray-500">Kirti Nagar, Sector 15 Part 1<br>Sector 15, Gurugram<br>Haryana 122001, India</p>
          </div>
          <div class="rounded-2xl shadow-sm p-6 bg-white border border-gray-100">
            <div class="text-3xl mb-3">📞</div>
            <h3 class="font-bold mb-2 text-gray-900">Call Us</h3>
            <a href="tel:+919650671568" class="text-orange-500 font-semibold text-lg hover:underline">+91 96506 71568</a>
          </div>
          <div class="rounded-2xl shadow-sm p-6 bg-white border border-gray-100">
            <div class="text-3xl mb-3">🕐</div>
            <h3 class="font-bold mb-2 text-gray-900">Business Hours</h3>
            <p class="text-sm text-gray-500">Daily: <strong>6:00 AM – 10:00 PM</strong></p>
          </div>
          <!-- Map -->
          <div class="rounded-2xl overflow-hidden shadow-md h-52">
            <iframe src="https://maps.google.com/maps?q=Kirti+Nagar+Sector+15+Gurugram+Haryana&output=embed&z=15"
              width="100%" height="100%" style="border:0;" allowfullscreen loading="lazy"></iframe>
          </div>
        </div>
      </div>
    </div>
  </GuestLayout>
</template>

<script setup>
import { ref, inject } from 'vue'
import { router } from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/GuestLayout.vue'


defineProps({ settings: Object })

const sending = ref(false)
const success = ref('')
const form = ref({ name: '', email: '', subject: '', message: '' })

function submit() {
  sending.value = true
  router.post('/contact', form.value, {
    onSuccess: () => {
      form.value = { name: '', email: '', subject: '', message: '' }
      success.value = 'Thank you! We will get back to you shortly.'
    },
    onFinish: () => { sending.value = false },
  })
}
</script>
