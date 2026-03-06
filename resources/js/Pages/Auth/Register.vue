<script setup>
import { computed, inject } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

const storeName = computed(() => usePage().props.settings?.store_name || 'Santosh Store')

const form = useForm({
    name: '',
    phone: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <div class="min-h-[70vh] flex items-center justify-center px-4 py-12 bg-gradient-to-br from-orange-50 via-amber-50 to-yellow-50">

          <div class="w-full max-w-md animate-slide-up">
            <!-- Logo & Header -->
            <div class="text-center mb-8">
              <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-amber-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                <span class="text-white font-bold text-2xl">स</span>
              </div>
              <h1 class="text-3xl font-extrabold text-gray-900">Create Account</h1>
              <p class="mt-2 text-gray-500">Join {{ storeName }} for the best grocery deals</p>
            </div>

            <!-- Glass Card -->
            <div class="rounded-2xl p-8 shadow-xl glass-card">

              <form @submit.prevent="submit" class="space-y-5">
                <div>
                  <InputLabel for="name" value="Full Name" class="dark:text-slate-300" />
                  <TextInput id="name" type="text"
                    class="mt-1.5 block w-full rounded-xl px-4 py-3 text-sm transition bg-white/80 border-gray-200 focus:border-orange-500 focus:ring-orange-500"
                    v-model="form.name" required autofocus autocomplete="name"
                    placeholder="Your name" />
                  <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div>
                  <InputLabel for="phone" value="Phone Number *" class="dark:text-slate-300" />
                  <div class="mt-1.5 flex items-center">
                    <span class="inline-flex items-center px-3.5 py-3 rounded-l-xl border border-r-0 border-gray-200 bg-gray-50 text-sm text-gray-600 font-medium select-none">+91</span>
                    <TextInput id="phone" type="tel"
                      class="block w-full rounded-r-xl rounded-l-none px-4 py-3 text-sm transition bg-white/80 border-gray-200 focus:border-orange-500 focus:ring-orange-500"
                      v-model="form.phone" required autocomplete="tel"
                      placeholder="9876543210" />
                  </div>
                  <InputError class="mt-2" :message="form.errors.phone" />
                </div>

                <div>
                  <InputLabel for="email" value="Email Address" class="dark:text-slate-300" />
                  <TextInput id="email" type="email"
                    class="mt-1.5 block w-full rounded-xl px-4 py-3 text-sm transition bg-white/80 border-gray-200 focus:border-orange-500 focus:ring-orange-500"
                    v-model="form.email" required autocomplete="username"
                    placeholder="you@example.com" />
                  <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <InputLabel for="password" value="Password" class="dark:text-slate-300" />
                    <TextInput id="password" type="password"
                      class="mt-1.5 block w-full rounded-xl px-4 py-3 text-sm transition bg-white/80 border-gray-200 focus:border-orange-500 focus:ring-orange-500"
                      v-model="form.password" required autocomplete="new-password"
                      placeholder="••••••••" />
                    <InputError class="mt-2" :message="form.errors.password" />
                  </div>

                  <div>
                    <InputLabel for="password_confirmation" value="Confirm" class="dark:text-slate-300" />
                    <TextInput id="password_confirmation" type="password"
                      class="mt-1.5 block w-full rounded-xl px-4 py-3 text-sm transition bg-white/80 border-gray-200 focus:border-orange-500 focus:ring-orange-500"
                      v-model="form.password_confirmation" required autocomplete="new-password"
                      placeholder="••••••••" />
                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                  </div>
                </div>

                <button type="submit"
                  class="w-full py-3.5 rounded-xl font-bold text-white text-sm transition shadow-lg hover:shadow-xl hover:-translate-y-0.5 duration-200 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 disabled:opacity-50"
                  :disabled="form.processing">
                  {{ form.processing ? 'Creating account...' : 'Create Account' }}
                </button>
              </form>

              <p class="text-center mt-6 text-sm text-gray-500">
                Already have an account?
                <Link :href="route('login')" class="font-semibold transition text-orange-600 hover:text-orange-700">
                  Sign in
                </Link>
              </p>
            </div>
          </div>
        </div>
    </GuestLayout>
</template>
