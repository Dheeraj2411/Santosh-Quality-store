<template>
  <GuestLayout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
      <!-- Header -->
      <div class="mb-8 flex items-center gap-3">
        <Link href="/products" class="transition text-gray-400 hover:text-orange-500">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
        </Link>
        <div>
          <h1 class="text-3xl font-extrabold text-gray-900">Checkout</h1>
          <p class="text-sm mt-0.5 text-gray-500">Review your order and complete payment</p>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- ─── Left: Address + Payment Method ─────────────────────────────── -->
        <div class="lg:col-span-2 space-y-6">

          <!-- Delivery Address -->
          <div class="rounded-2xl shadow-sm p-6 bg-white border border-gray-100">
            <div class="flex items-center justify-between mb-4">
              <h2 class="font-bold text-lg flex items-center gap-2 text-gray-900">
                📍 Delivery Address
              </h2>
              <Link href="/dashboard/addresses" class="text-xs text-orange-500 hover:text-orange-600 font-medium">
                + Add Address
              </Link>
            </div>

            <div v-if="addresses.length === 0" class="bg-orange-50 border border-orange-100 rounded-xl p-4 text-sm text-orange-700">
              ⚠️ No saved addresses. <Link href="/dashboard/addresses" class="underline font-medium">Add one</Link> to proceed.
            </div>

            <div v-else class="space-y-3">
              <label v-for="addr in addresses" :key="addr.id"
                class="flex items-start gap-3 p-4 border-2 rounded-xl cursor-pointer transition"
                :class="form.address_id === addr.id ? 'border-orange-400 bg-orange-50' : 'border-gray-100 hover:border-orange-200'">
                <input type="radio" v-model="form.address_id" :value="addr.id"
                  class="mt-1 text-orange-500 focus:ring-orange-500" />
                <div class="flex-1">
                  <div class="flex flex-wrap items-center gap-2 mb-1">
                    <span class="font-semibold text-gray-800 text-sm">{{ addr.name }}</span>
                    <span class="bg-orange-100 text-orange-700 text-xs font-bold px-2 py-0.5 rounded-full">{{ addr.label }}</span>
                    <span v-if="addr.is_default" class="bg-green-100 text-green-700 text-xs font-bold px-2 py-0.5 rounded-full">✓ Default</span>
                  </div>
                  <p class="text-gray-500 text-xs leading-relaxed">
                    {{ addr.line1 }}{{ addr.line2 ? ', ' + addr.line2 : '' }}, {{ addr.city }}, {{ addr.state }} {{ addr.pincode }}
                  </p>
                  <p class="text-gray-400 text-xs mt-0.5">📞 {{ addr.phone }}</p>
                </div>
              </label>
            </div>
          </div>

          <!-- Payment Method -->
          <div class="rounded-2xl shadow-sm p-6 bg-white border border-gray-100">
            <h2 class="font-bold text-lg mb-4 text-gray-900">💳 Payment Method</h2>

            <div class="space-y-3">
              <!-- Razorpay -->
              <label class="flex items-start gap-3 p-4 border-2 rounded-xl cursor-pointer transition"
                :class="form.payment_method === 'razorpay' ? 'border-blue-400 bg-blue-50' : 'border-gray-100 hover:border-blue-200'">
                <input type="radio" v-model="form.payment_method" value="razorpay"
                  class="mt-1 text-blue-500 focus:ring-blue-500" />
                <div class="flex-1">
                  <div class="flex items-center gap-3 mb-1">
                    <span class="font-bold text-gray-900 text-sm">Razorpay</span>
                    <div class="flex gap-1 flex-wrap">
                      <span class="bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded font-medium">UPI</span>
                      <span class="bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded font-medium">Cards</span>
                      <span class="bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded font-medium">Net Banking</span>
                      <span class="bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded font-medium">Wallets</span>
                    </div>
                  </div>
                  <p class="text-gray-400 text-xs">Secure online payment — UPI, Debit/Credit Card, Paytm, PhonePe & more</p>
                </div>
              </label>

              <!-- Cash on Delivery -->
              <label class="flex items-start gap-3 p-4 border-2 rounded-xl cursor-pointer transition"
                :class="form.payment_method === 'cod' ? 'border-green-400 bg-green-50' : 'border-gray-100 hover:border-green-200'">
                <input type="radio" v-model="form.payment_method" value="cod"
                  class="mt-1 text-green-500 focus:ring-green-500" />
                <div>
                  <span class="font-bold text-gray-900 text-sm">💵 Cash on Delivery</span>
                  <p class="text-gray-400 text-xs mt-0.5">Pay with cash when your order arrives</p>
                </div>
              </label>
            </div>
          </div>

          <!-- Order Notes -->
          <div class="rounded-2xl shadow-sm p-6 bg-white border border-gray-100">
            <h2 class="font-bold text-lg mb-3 text-gray-900">📝 Order Notes <span class="font-normal text-sm text-gray-400">(Optional)</span></h2>
            <textarea v-model="form.notes" rows="3"
              placeholder="Any special instructions for delivery? e.g. Door code, timing preference..."
              class="w-full rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 outline-none resize-none transition border border-gray-200"
            ></textarea>
          </div>
        </div>

        <!-- ─── Right: Order Summary ──────────────────────────────────────── -->
        <div>
          <div class="rounded-2xl shadow-sm p-6 sticky top-20 bg-white border border-gray-100">
            <h2 class="font-bold text-lg mb-4 text-gray-900">📋 Order Summary</h2>

            <!-- Cart Items -->
            <div class="space-y-3 mb-5 max-h-64 overflow-y-auto pr-1">
              <div v-if="cart.isEmpty" class="text-center text-gray-400 py-4 text-sm">
                Cart is empty
              </div>
              <div v-for="item in cart.items" :key="item.product_id" class="flex items-center gap-3">
                <img :src="item.thumbnail" :alt="item.name"
                  class="w-12 h-12 object-cover rounded-lg bg-gray-100 flex-shrink-0" />
                <div class="flex-1 min-w-0">
                  <p class="text-xs font-medium text-gray-800 truncate">{{ item.name }}</p>
                  <p class="text-xs text-gray-400">{{ item.unit }} × {{ item.quantity }}</p>
                </div>
                <span class="text-sm font-bold text-gray-900 flex-shrink-0">
                  ₹{{ (item.price * item.quantity).toLocaleString('en-IN') }}
                </span>
              </div>
            </div>

            <!-- Totals -->
            <div class="border-t pt-4 space-y-2 text-sm">
              <div class="flex justify-between text-gray-500">
                <span>Subtotal</span>
                <span>₹{{ Number(cart.subtotal).toLocaleString('en-IN') }}</span>
              </div>
              <div class="flex justify-between text-gray-500">
                <span>Delivery Charge</span>
                <span :class="cart.deliveryCharge === 0 ? 'text-green-600 font-semibold' : ''">
                  {{ cart.deliveryCharge === 0 ? '🎉 FREE' : '₹' + cart.deliveryCharge }}
                </span>
              </div>
              <div v-if="cart.deliveryCharge > 0" class="text-xs text-orange-500 bg-orange-50 px-3 py-1.5 rounded-lg">
                Add ₹{{ (500 - cart.subtotal).toLocaleString('en-IN') }} more for free delivery!
              </div>
              <div class="flex justify-between font-bold text-xl text-gray-900 border-t pt-3 mt-3">
                <span>Total</span>
                <span class="text-orange-600">₹{{ Number(cart.total).toLocaleString('en-IN') }}</span>
              </div>
            </div>

            <!-- Pay Button -->
            <button @click="handlePayment"
              :disabled="submitting || !form.address_id || !form.payment_method || cart.isEmpty"
              class="mt-5 w-full py-4 rounded-xl font-bold text-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
              :class="form.payment_method === 'razorpay'
                ? 'bg-blue-600 hover:bg-blue-700 text-white'
                : 'bg-orange-500 hover:bg-orange-600 text-white'">
              <span v-if="submitting" class="flex items-center justify-center gap-2">
                <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                {{ form.payment_method === 'razorpay' ? 'Opening Payment...' : 'Placing Order...' }}
              </span>
              <span v-else>
                {{ form.payment_method === 'razorpay' ? '🔐 Pay with Razorpay' : '🎉 Place Order (COD)' }}
              </span>
            </button>

            <div class="mt-3 flex items-center justify-center gap-4 text-xs text-gray-400">
              <span>🔒 Secure checkout</span>
              <span>•</span>
              <span>🚚 Local delivery</span>
            </div>

            <!-- Razorpay trust badges -->
            <div v-if="form.payment_method === 'razorpay'" class="mt-3 text-center">
              <p class="text-xs text-gray-400">Powered by</p>
              <p class="text-sm font-bold text-blue-600 mt-0.5">Razorpay</p>
              <p class="text-xs text-gray-400 mt-1">256-bit SSL encrypted • PCI DSS compliant</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Error Alert -->
      <div v-if="paymentError"
        class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-red-500 text-white px-6 py-3 rounded-xl shadow-lg z-50 flex items-center gap-2 text-sm font-medium">
        ❌ {{ paymentError }}
      </div>
    </div>
  </GuestLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { useCartStore } from '@/stores/cartStore'
import axios from 'axios'


const props = defineProps({
  addresses: { type: Array, default: () => [] },
})

const cart = useCartStore()
const submitting  = ref(false)
const paymentError = ref('')

const form = ref({
  address_id:     props.addresses.find(a => a.is_default)?.id ?? props.addresses[0]?.id ?? null,
  payment_method: 'razorpay',
  notes:          '',
})

// Load cart data on mount
onMounted(() => cart.fetchCart())

// ─── Load Razorpay SDK ─────────────────────────────────────────────────────
function loadRazorpaySDK() {
  return new Promise((resolve) => {
    if (window.Razorpay) return resolve(true)
    const script = document.createElement('script')
    script.src = 'https://checkout.razorpay.com/v1/checkout.js'
    script.onload  = () => resolve(true)
    script.onerror = () => resolve(false)
    document.body.appendChild(script)
  })
}

// ─── Main Payment Handler ──────────────────────────────────────────────────
async function handlePayment() {
  paymentError.value = ''

  if (!form.value.address_id) {
    paymentError.value = 'Please select a delivery address.'
    setTimeout(() => { paymentError.value = '' }, 4000)
    return
  }

  if (form.value.payment_method === 'cod') {
    return placeCodOrder()
  }

  if (form.value.payment_method === 'razorpay') {
    return initiateRazorpay()
  }
}

// ─── COD flow ─────────────────────────────────────────────────────────────
function placeCodOrder() {
  submitting.value = true
  router.post('/checkout/cod', {
    address_id: form.value.address_id,
    notes:      form.value.notes,
  }, {
    onSuccess: (page) => {
      cart.fetchCart()
      // Open WhatsApp with order details
      const waUrl = page.props.flash?.whatsapp_url
      if (waUrl) {
        window.open(waUrl, '_blank')
      }
    },
    onFinish:  () => { submitting.value = false },
  })
}

// ─── Razorpay flow ────────────────────────────────────────────────────────
async function initiateRazorpay() {
  submitting.value = true

  // 1. Load SDK
  const sdkLoaded = await loadRazorpaySDK()
  if (!sdkLoaded) {
    paymentError.value = 'Could not load payment gateway. Please try again.'
    submitting.value = false
    setTimeout(() => { paymentError.value = '' }, 5000)
    return
  }

  // 2. Create Razorpay order on server
  let rzpData
  try {
    const res = await axios.post('/payment/razorpay/create-order', {
      address_id: form.value.address_id,
    })
    rzpData = res.data
  } catch (err) {
    paymentError.value = err.response?.data?.error ?? 'Could not initiate payment.'
    submitting.value = false
    setTimeout(() => { paymentError.value = '' }, 5000)
    return
  }

  // 3. Open Razorpay modal
  const options = {
    key:         rzpData.key_id,
    amount:      rzpData.amount,
    currency:    rzpData.currency,
    name:        usePage().props.settings?.store_name || 'Santosh Store',
    description: 'Grocery Order',
    order_id:    rzpData.razorpay_order_id,
    prefill: {
      name:    rzpData.user.name,
      email:   rzpData.user.email,
      contact: rzpData.user.phone,
    },
    theme: { color: '#f97316' },
    handler: async (response) => {
      // 4. Verify payment on server and create order
      try {
        const verifyRes = await axios.post('/payment/razorpay/verify', {
          razorpay_payment_id: response.razorpay_payment_id,
          razorpay_order_id:   response.razorpay_order_id,
          razorpay_signature:  response.razorpay_signature,
          address_id:          form.value.address_id,
          notes:               form.value.notes,
        })

        if (verifyRes.data.success) {
          await cart.fetchCart()
          // Open WhatsApp with order details
          if (verifyRes.data.whatsapp_url) {
            window.open(verifyRes.data.whatsapp_url, '_blank')
          }
          window.location.href = verifyRes.data.redirect
        }
      } catch (err) {
        paymentError.value = err.response?.data?.error ?? 'Payment verification failed.'
        submitting.value = false
        setTimeout(() => { paymentError.value = '' }, 6000)
      }
    },
    modal: {
      ondismiss: () => {
        submitting.value = false
      },
    },
  }

  const rzp = new window.Razorpay(options)
  rzp.on('payment.failed', (response) => {
    paymentError.value = `Payment failed: ${response.error.description}`
    submitting.value = false
    setTimeout(() => { paymentError.value = '' }, 6000)
  })
  rzp.open()
}
</script>
