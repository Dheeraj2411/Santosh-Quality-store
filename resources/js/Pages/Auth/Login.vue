<script setup>
import { inject, computed } from 'vue';
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

const storeName = computed(() => usePage().props.settings?.store_name || 'Santosh Store')

defineProps({
    canResetPassword: { type: Boolean },
    status: { type: String },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div class="min-h-[70vh] flex items-center justify-center px-4 py-12 bg-gradient-to-br from-orange-50 via-amber-50 to-yellow-50">

          <div class="w-full max-w-md animate-slide-up">
            <!-- Logo & Header -->
            <div class="text-center mb-8">
              <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-amber-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                <span class="text-white font-bold text-2xl">स</span>
              </div>
              <h1 class="text-3xl font-extrabold text-gray-900">Welcome Back</h1>
              <p class="mt-2 text-gray-500">Sign in to your {{ storeName }} account</p>
            </div>

            <!-- Glass Card -->
            <div class="rounded-2xl p-8 shadow-xl glass-card">

              <div v-if="status" class="mb-4 text-sm font-medium px-4 py-3 rounded-xl bg-green-50 text-green-600">
                {{ status }}
              </div>

              <form @submit.prevent="submit" class="space-y-5">
                <div>
                  <InputLabel for="email" value="Email Address"
                    class="dark:text-slate-300" />
                  <TextInput id="email" type="email"
                    class="mt-1.5 block w-full rounded-xl px-4 py-3 text-sm transition bg-white/80 border-gray-200 focus:border-orange-500 focus:ring-orange-500"
                    v-model="form.email" required autofocus autocomplete="username"
                    placeholder="you@example.com" />
                  <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div>
                  <InputLabel for="password" value="Password"
                    class="dark:text-slate-300" />
                  <TextInput id="password" type="password"
                    class="mt-1.5 block w-full rounded-xl px-4 py-3 text-sm transition bg-white/80 border-gray-200 focus:border-orange-500 focus:ring-orange-500"
                    v-model="form.password" required autocomplete="current-password"
                    placeholder="••••••••" />
                  <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="flex items-center justify-between">
                  <label class="flex items-center gap-2 cursor-pointer">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="text-sm text-gray-600">Remember me</span>
                  </label>
                  <Link v-if="canResetPassword" :href="route('password.request')"
                    class="text-sm font-medium transition text-orange-600 hover:text-orange-700">
                    Forgot password?
                  </Link>
                </div>

                <button type="submit"
                  class="w-full py-3.5 rounded-xl font-bold text-white text-sm transition shadow-lg hover:shadow-xl hover:-translate-y-0.5 duration-200 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 disabled:opacity-50"
                  :disabled="form.processing">
                  {{ form.processing ? 'Signing in...' : 'Sign In' }}
                </button>
              </form>

              <p class="text-center mt-6 text-sm text-gray-500">
                Don't have an account?
                <Link :href="route('register')" class="font-semibold transition text-orange-600 hover:text-orange-700">
                  Create one
                </Link>
              </p>
            </div>
          </div>
        </div>
    </GuestLayout>
</template>
