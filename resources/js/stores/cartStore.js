import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

export const useCartStore = defineStore('cart', () => {
  const items = ref([])
  const subtotal = ref(0)
  const deliveryCharge = ref(0)
  const total = ref(0)
  const count = ref(0)
  const loading = ref(false)
  const isOpen = ref(false)

  const isEmpty = computed(() => items.value.length === 0)

  async function fetchCart() {
    try {
      const res = await axios.get('/api/cart')
      updateFromResponse(res.data)
    } catch (e) {
      console.error('Cart fetch error', e)
    }
  }

  async function addItem(productId, quantity = 1) {
    loading.value = true
    try {
      const res = await axios.post('/api/cart/add', { product_id: productId, quantity })
      updateFromResponse(res.data.cart)
      isOpen.value = true
    } catch (e) {
      console.error('Add to cart error', e)
    } finally {
      loading.value = false
    }
  }

  async function updateQuantity(productId, quantity) {
    try {
      const res = await axios.put(`/api/cart/${productId}`, { quantity })
      updateFromResponse(res.data.cart)
    } catch (e) {
      console.error('Update cart error', e)
    }
  }

  async function removeItem(productId) {
    try {
      const res = await axios.delete(`/api/cart/${productId}`)
      updateFromResponse(res.data.cart)
    } catch (e) {
      console.error('Remove cart error', e)
    }
  }

  function updateFromResponse(cartData) {
    items.value         = cartData.items || []
    subtotal.value      = cartData.subtotal || 0
    deliveryCharge.value = cartData.delivery_charge || 0
    total.value         = cartData.total || 0
    count.value         = cartData.count || 0
  }

  function toggleDrawer() {
    isOpen.value = !isOpen.value
  }

  function closeDrawer() {
    isOpen.value = false
  }

  return { items, subtotal, deliveryCharge, total, count, loading, isOpen, isEmpty, fetchCart, addItem, updateQuantity, removeItem, toggleDrawer, closeDrawer }
})
