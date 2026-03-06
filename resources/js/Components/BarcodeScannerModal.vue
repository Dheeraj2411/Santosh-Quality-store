<template>
  <div v-if="show" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden flex flex-col">
      <!-- Header -->
      <div class="flex items-center justify-between p-4 border-b border-gray-100 bg-gray-50/50">
        <h3 class="font-bold text-gray-900 flex items-center gap-2">
          <span class="text-xl">📷</span> Scan Barcode
        </h3>
        <button @click="close" class="text-gray-400 hover:text-red-500 transition-colors p-1 rounded-lg hover:bg-red-50">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
      </div>

      <!-- Scanner Area -->
      <div class="p-4 relative bg-black aspect-square flex items-center justify-center">
        <!-- Reader Div -->
        <div id="qr-reader" class="w-full h-full rounded-xl overflow-hidden [&>video]:w-full [&>video]:h-full [&>video]:object-cover"></div>
        
        <!-- Loading State -->
        <div v-if="!isScanning && !hasCameraError" class="absolute text-center">
          <div class="animate-spin rounded-full h-8 w-8 border-2 border-orange-500 border-t-transparent mx-auto mb-2"></div>
          <p class="text-white/70 text-sm">Accessing camera...</p>
        </div>

        <!-- Error State -->
        <div v-if="hasCameraError" class="absolute text-center px-6">
          <div class="text-red-500 text-3xl mb-2">⚠️</div>
          <p class="text-white font-medium mb-1">Camera Error</p>
          <p class="text-red-200 text-sm">{{ errorMessage }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onBeforeUnmount } from 'vue'
import { Html5Qrcode } from "html5-qrcode"

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close', 'scan'])

const html5QrCode = ref(null)
const isScanning = ref(false)
const hasCameraError = ref(false)
const errorMessage = ref('')

// Initialize scanner when modal opens
watch(() => props.show, async (newVal) => {
  if (newVal) {
    hasCameraError.value = false
    isScanning.value = false
    
    // Give DOM time to render the modal
    setTimeout(() => {
      startScanner()
    }, 100)
  } else {
    stopScanner()
  }
})

async function startScanner() {
  try {
    const devices = await Html5Qrcode.getCameras()
    if (devices && devices.length) {
      // Create instance
      html5QrCode.value = new Html5Qrcode("qr-reader")
      
      // Select best back camera if available, otherwise just use environment facing mode
      const config = {
        fps: 20,
        qrbox: { width: 250, height: 250 },
        aspectRatio: 1.0,
        disableFlip: false,
      }

      await html5QrCode.value.start(
        { facingMode: "environment" },
        config,
        (decodedText, decodedResult) => {
          // Success! Emit the barcode exactly once
          playBeep()
          stopScanner()
          emit('scan', decodedText)
        },
        (error) => {
          // Ignore parse errors (happens constantly while scanning empty space)
        }
      )
      
      // Attempt to force continuous autofocus if hardware supports it
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
      errorMessage.value = "No camera devices found."
    }
  } catch (err) {
    hasCameraError.value = true
    errorMessage.value = "Could not access camera. Please check browser permissions."
    console.error(err)
  }
}

async function stopScanner() {
  if (html5QrCode.value && html5QrCode.value.isScanning) {
    try {
      await html5QrCode.value.stop()
      html5QrCode.value.clear()
    } catch (err) {
      console.error("Failed to stop scanner", err)
    }
  }
  isScanning.value = false
}

function close() {
  stopScanner()
  emit('close')
}

onBeforeUnmount(() => {
  stopScanner()
})

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
