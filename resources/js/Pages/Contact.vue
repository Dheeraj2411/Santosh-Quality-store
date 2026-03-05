<template>
  <GuestLayout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
      <div class="text-center mb-12">
        <h1 class="text-4xl font-extrabold text-gray-900 mb-3">Contact Us</h1>
        <p class="text-gray-500 text-lg">We'd love to hear from you</p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
        <!-- Form -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
          <h2 class="font-bold text-gray-900 text-xl mb-6">Send a Message</h2>
          <div v-if="success" class="bg-green-50 border border-green-200 text-green-700 px-5 py-3 rounded-xl mb-4 text-sm">
            ✅ {{ success }}
          </div>
          <form @submit.prevent="submit" class="space-y-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">Your Name *</label>
              <input v-model="form.name" type="text" required class="input" placeholder="Amit Sharma" />
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email Address *</label>
              <input v-model="form.email" type="email" required class="input" placeholder="amit@example.com" />
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">Subject *</label>
              <input v-model="form.subject" type="text" required class="input" placeholder="Order inquiry, delivery, etc." />
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">Message *</label>
              <textarea v-model="form.message" rows="5" required class="input resize-none" placeholder="How can we help you?"></textarea>
            </div>
            <button type="submit" :disabled="sending"
              class="w-full bg-orange-500 hover:bg-orange-600 text-white py-3.5 rounded-xl font-bold text-lg transition disabled:opacity-70">
              {{ sending ? 'Sending...' : '📩 Send Message' }}
            </button>
          </form>
        </div>

        <!-- Info -->
        <div class="space-y-6">
          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="text-3xl mb-3">📍</div>
            <h3 class="font-bold text-gray-900 mb-2">Visit Our Store</h3>
            <p class="text-gray-500 text-sm leading-relaxed">Kirti Nagar, Sector 15 Part 1<br>Sector 15, Gurugram<br>Haryana 122001, India</p>
          </div>
          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="text-3xl mb-3">📞</div>
            <h3 class="font-bold text-gray-900 mb-2">Call Us</h3>
            <a href="tel:+919650671568" class="text-orange-500 font-semibold text-lg hover:underline">+91 96506 71568</a>
          </div>
          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="text-3xl mb-3">🕐</div>
            <h3 class="font-bold text-gray-900 mb-2">Business Hours</h3>
            <p class="text-gray-500 text-sm">Daily: <strong>6:00 AM – 10:00 PM</strong></p>
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
import { ref } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/GuestLayout.vue'

defineProps({ settings: Object })

const page = usePage()
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

<style scoped>
@reference "../../css/app.css";
.input { @apply w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 outline-none; }
</style>
