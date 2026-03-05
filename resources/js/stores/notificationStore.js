import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useNotificationStore = defineStore('notification', () => {
  const notifications = ref([])
  let nextId = 0

  function add(message, type = 'success', duration = 4000) {
    const id = ++nextId
    notifications.value.push({ id, message, type })
    setTimeout(() => remove(id), duration)
  }

  function remove(id) {
    const idx = notifications.value.findIndex(n => n.id === id)
    if (idx !== -1) notifications.value.splice(idx, 1)
  }

  function success(message) { add(message, 'success') }
  function error(message) { add(message, 'error') }
  function info(message) { add(message, 'info') }

  return { notifications, add, remove, success, error, info }
})
