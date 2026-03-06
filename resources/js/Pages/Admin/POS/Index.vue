<template>
  <AdminLayout title="Point of Sale (POS)" subtitle="Scan products to process in-store sales">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 h-[calc(100vh-140px)] min-h-[600px]">
      
      <!-- Left: Camera Scanner Feed -->
      <div class="lg:col-span-5 h-full flex flex-col">
        <div class="bg-black rounded-2xl shadow-sm overflow-hidden relative flex-1 flex flex-col items-center justify-center border border-gray-800">
          <div id="pos-qr-reader" class="w-full h-full [&>video]:w-full [&>video]:h-full [&>video]:object-cover absolute inset-0"></div>
          
          <div v-if="!isScanning && !hasCameraError" class="absolute inset-0 flex flex-col items-center justify-center bg-black">
             <div class="animate-spin rounded-full h-10 w-10 border-4 border-orange-500 border-t-transparent mx-auto mb-4"></div>
             <p class="text-white/80 font-medium tracking-wide">Initializing Scanner...</p>
          </div>
          
          <div v-if="hasCameraError" class="absolute inset-0 flex flex-col items-center justify-center bg-gray-900 px-6 text-center">
            <div class="text-red-500 text-4xl mb-3">⚠️</div>
            <p class="text-white font-bold mb-2">Camera Unavailable</p>
            <p class="text-gray-400 text-sm max-w-[300px]">{{ errorMessage }}</p>
            <button @click="startScanner" class="mt-6 px-6 py-2 bg-orange-500 hover:bg-orange-600 rounded-xl text-white font-semibold transition">
              Retry Camera
            </button>
          </div>
          
          <!-- Scanning Overlay / Reticle -->
          <div v-if="isScanning" class="absolute inset-0 pointer-events-none border-[16px] border-black/40">
             <div class="w-full h-full border-2 border-orange-500/50 relative">
               <div class="absolute inset-y-0 w-full animate-scan bg-orange-500/20 shadow-[0_0_15px_rgba(249,115,22,0.5)] h-1 my-auto"></div>
             </div>
          </div>
        </div>

        <!-- Scan Status Feedback -->
        <div class="mt-4 p-4 rounded-xl bg-white border border-gray-100 shadow-sm min-h-[80px] flex items-center">
          <div v-if="scanFeedback.type === 'processing'" class="flex items-center gap-3 w-full">
            <div class="animate-spin rounded-full h-5 w-5 border-2 border-orange-500 border-t-transparent"></div>
            <p class="text-gray-600 font-medium">Looking up barcode: <span class="text-gray-900 font-mono">{{ scanFeedback.barcode }}</span>...</p>
          </div>
          <div v-else-if="scanFeedback.type === 'success'" class="flex items-center gap-3 w-full bg-green-50 p-3 rounded-lg border border-green-100">
            <div class="text-green-500 text-xl font-bold">✓</div>
            <div>
              <p class="text-green-800 font-bold leading-tight">{{ scanFeedback.product.name }}</p>
              <p class="text-green-600 text-sm leading-tight">Added to bill (₹{{ scanFeedback.product.price }})</p>
            </div>
          </div>
          <div v-else-if="scanFeedback.type === 'error'" class="flex items-center gap-3 w-full bg-red-50 p-3 rounded-lg border border-red-100">
            <div class="text-red-500 text-xl font-bold">❌</div>
            <div>
              <p class="text-red-800 font-bold leading-tight">Scan Failed</p>
              <p class="text-red-600 text-sm leading-tight">{{ scanFeedback.message }}</p>
            </div>
          </div>
          <div v-else class="text-gray-400 font-bold text-center w-full uppercase tracking-wider text-sm flex flex-col items-center gap-1">
             <svg class="w-6 h-6 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
             Ready to Scan
          </div>
        </div>
      </div>

      <!-- Right: Bill / Cart -->
      <div class="lg:col-span-7 h-full flex flex-col bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
          <h2 class="font-bold text-gray-900 text-lg flex items-center gap-2">
            <span>🧾</span> Current Bill
          </h2>
          <span class="bg-orange-100 text-orange-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wide">
            {{ cart.length }} items
          </span>
        </div>

        <!-- Cart Items List -->
        <div class="flex-1 overflow-y-auto p-5">
          <div v-if="cart.length === 0" class="h-full flex flex-col items-center justify-center text-gray-400 pt-10">
            <div class="text-5xl mb-4 opacity-50">🛒</div>
            <p class="font-medium text-lg text-gray-500">Bill is empty</p>
            <p class="text-sm mt-1">Scan a product barcode to add it here.</p>
          </div>
          
          <div v-else class="space-y-3">
            <transition-group name="list">
              <div v-for="(item, index) in cart" :key="item.id" 
                class="flex gap-4 p-4 rounded-xl border border-gray-100 bg-white shadow-sm hover:border-orange-200 transition relative group">
                
                <img :src="item.image" class="w-16 h-16 rounded-lg object-cover border border-gray-100" />
                
                <div class="flex-1 min-w-0">
                  <h3 class="font-bold text-gray-900 truncate pr-8">{{ item.name }}</h3>
                  <div class="flex items-center gap-2 mt-1">
                    <span class="text-orange-600 font-bold">₹{{ item.price }}</span>
                    <span v-if="item.mrp > item.price" class="text-xs line-through text-gray-400">₹{{ item.mrp }}</span>
                  </div>
                  <p class="text-xs text-gray-500 mt-1 font-mono">Stock: {{ item.stock }}</p>
                </div>

                <!-- Quantity Controls -->
                <div class="flex items-center gap-3">
                  <div class="flex items-center bg-gray-50 rounded-lg border border-gray-200 p-0.5">
                    <button @click="updateQty(index, -1)" class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-white hover:shadow-sm rounded-md transition font-bold disabled:opacity-30" :disabled="item.quantity <= 1">-</button>
                    <span class="w-8 text-center font-bold text-gray-900">{{ item.quantity }}</span>
                    <button @click="updateQty(index, 1)" class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-white hover:shadow-sm rounded-md transition font-bold disabled:opacity-30" :disabled="item.quantity >= item.stock">+</button>
                  </div>
                  
                  <div class="w-20 text-right">
                    <p class="font-bold text-gray-900">₹{{ (item.price * item.quantity).toFixed(2) }}</p>
                  </div>
                </div>

                <!-- Remove Button -->
                <button @click="removeItem(index)" class="absolute top-2 right-2 w-7 h-7 bg-red-50 text-red-500 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition hover:bg-red-500 hover:text-white">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
              </div>
            </transition-group>
          </div>
        </div>

        <!-- Checkout Section -->
        <div class="p-5 border-t border-gray-100 bg-gray-50/50 space-y-4">
          <div class="flex justify-between items-center text-sm font-semibold text-gray-600">
            <span>Subtotal</span>
            <span>₹{{ cartTotal.toFixed(2) }}</span>
          </div>
          <div class="flex justify-between items-center text-sm font-semibold text-gray-600">
            <span>Tax (Included)</span>
            <span>₹0.00</span>
          </div>
          <div class="h-px bg-gray-200 w-full"></div>
          <div class="flex justify-between items-end">
            <div>
              <span class="block text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Total Amount</span>
              <span class="text-3xl font-extrabold text-gray-900 leading-none">₹{{ cartTotal.toFixed(2) }}</span>
            </div>
            
            <div class="space-x-2">
              <select v-model="paymentMethod" class="border-gray-300 rounded-xl px-4 py-3 text-sm font-medium focus:ring-orange-500 focus:border-orange-500 bg-white">
                <option value="cash">💵 Cash</option>
                <option value="upi">📱 UPI</option>
                <option value="card">💳 Card</option>
              </select>
              
              <button @click="checkout" :disabled="cart.length === 0 || isCheckingOut" 
                class="bg-green-500 hover:bg-green-600 disabled:bg-gray-300 disabled:cursor-not-allowed text-white px-8 py-3 rounded-xl font-bold transition shadow-lg hover:-translate-y-0.5"
                :class="{'animate-pulse': isCheckingOut}">
                {{ isCheckingOut ? 'Processing...' : 'Complete Sale' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { Html5Qrcode } from "html5-qrcode"
import axios from 'axios'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const html5QrCode = ref(null)
const isScanning = ref(false)
const hasCameraError = ref(false)
const errorMessage = ref('')
const scanFeedback = ref({ type: null }) // null, 'processing', 'success', 'error'

const cart = ref([])
const paymentMethod = ref('cash')
const isCheckingOut = ref(false)

const cartTotal = computed(() => {
  return cart.value.reduce((total, item) => total + (item.price * item.quantity), 0)
})

onMounted(() => {
  setTimeout(() => {
    startScanner()
  }, 500) // slight delay to ensure DOM is ready
})

onBeforeUnmount(() => {
  stopScanner()
})

async function startScanner() {
  hasCameraError.value = false
  isScanning.value = false
  
  try {
    const devices = await Html5Qrcode.getCameras()
    if (devices && devices.length) {
      if(!html5QrCode.value) {
        html5QrCode.value = new Html5Qrcode("pos-qr-reader")
      }
      
      const config = {
        fps: 20,
        qrbox: { width: 300, height: 150 }, // Rectangular reticle for barcodes
        aspectRatio: 1.0,
        disableFlip: false, // Don't flip by default, rely on native camera orientation
      }

      await html5QrCode.value.start(
        { facingMode: "environment" },
        config,
        async (decodedText, decodedResult) => {
          // Pause scanning while processing to prevent duplicate hits immediately
          if (scanFeedback.value.type === 'processing') return;
          
          playBeep()
          await processBarcode(decodedText)
        },
        (error) => {
          // Ignore empty scans
        }
      )
      
      // Attempt to force continuous autofocus if the hardware supports it
      setTimeout(async () => {
        try {
          const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } })
          const track = stream.getVideoTracks()[0]
          const capabilities = track.getCapabilities()
          if (capabilities && capabilities.focusMode && capabilities.focusMode.includes('continuous')) {
             await track.applyConstraints({
               advanced: [{ focusMode: "continuous" }]
             })
          }
        } catch (e) {
             // Hardware might not support manual constraint application
        }
      }, 1000)
      
      isScanning.value = true
    } else {
      hasCameraError.value = true
      errorMessage.value = "No camera devices found on this device."
    }
  } catch (err) {
    hasCameraError.value = true
    errorMessage.value = "Camera access denied or unavailable."
    console.error(err)
  }
}

async function stopScanner() {
  if (html5QrCode.value && html5QrCode.value.isScanning) {
    try {
      await html5QrCode.value.stop()
    } catch (err) { }
  }
  isScanning.value = false
}

async function processBarcode(barcode) {
  scanFeedback.value = { type: 'processing', barcode }
  
  try {
    const res = await axios.post('/admin/pos/scan', { barcode })
    if (res.data.success) {
      const product = res.data.product
      
      // Check if already in cart
      const existingIndex = cart.value.findIndex(item => item.id === product.id)
      
      if (existingIndex > -1) {
        // Increment qty if stock allows
        if (cart.value[existingIndex].quantity < product.stock) {
          cart.value[existingIndex].quantity++
          scanFeedback.value = { type: 'success', product }
        } else {
          scanFeedback.value = { type: 'error', message: 'No more stock available!' }
        }
      } else {
        // Add new
        cart.value.unshift({
          ...product,
          quantity: 1
        })
        scanFeedback.value = { type: 'success', product }
      }
    }
  } catch (err) {
    const msg = err.response?.data?.message || 'Barcode not found.'
    scanFeedback.value = { type: 'error', message: msg }
  }

  // Clear feedback after a short delay so they know it's ready again
  setTimeout(() => {
    if (scanFeedback.value.barcode === barcode) {
      scanFeedback.value = { type: null }
    }
  }, 2000)
}

function updateQty(index, change) {
  const item = cart.value[index]
  const newQty = item.quantity + change
  if (newQty >= 1 && newQty <= item.stock) {
    item.quantity = newQty
  }
}

function removeItem(index) {
  cart.value.splice(index, 1)
}

async function checkout() {
  if (cart.value.length === 0) return
  
  isCheckingOut.value = true
  
  try {
    const payload = {
      payment_method: paymentMethod.value,
      items: cart.value.map(item => ({
        id: item.id,
        quantity: item.quantity
      }))
    }
    
    const res = await axios.post('/admin/pos/checkout', payload)
    
    if (res.data.success) {
      alert(`Sale Complete! Order #${res.data.order_id}`)
      cart.value = [] // Clear the bill
      scanFeedback.value = { type: null }
    }
  } catch (err) {
    const msg = err.response?.data?.message || 'Error processing sale.'
    alert("Checkout Failed: " + msg)
  } finally {
    isCheckingOut.value = false
  }
}

function playBeep() {
  try {
    const audioCtx = new (window.AudioContext || window.webkitAudioContext)()
    const oscillator = audioCtx.createOscillator()
    const gainNode = audioCtx.createGain()

    oscillator.type = 'sine'
    // 800Hz is a standard scanner beep frequency
    oscillator.frequency.setValueAtTime(800, audioCtx.currentTime)
    
    // Quick fade out to avoid speaker pop
    gainNode.gain.setValueAtTime(0.1, audioCtx.currentTime)
    gainNode.gain.exponentialRampToValueAtTime(0.00001, audioCtx.currentTime + 0.1)
    
    oscillator.connect(gainNode)
    gainNode.connect(audioCtx.destination)
    
    oscillator.start()
    oscillator.stop(audioCtx.currentTime + 0.1)
  } catch (e) {
    console.warn("Audio beep failed", e)
  }
}
</script>

<style scoped>
@keyframes scan {
  0% { transform: translateY(-75px); }
  50% { transform: translateY(75px); }
  100% { transform: translateY(-75px); }
}
.animate-scan {
  animation: scan 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.list-enter-active,
.list-leave-active {
  transition: all 0.3s ease;
}
.list-enter-from {
  opacity: 0;
  transform: translateX(30px);
}
.list-leave-to {
  opacity: 0;
  transform: scale(0.9);
}
</style>
