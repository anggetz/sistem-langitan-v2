<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Checkbox from '@/BaseComponents/Checkbox.vue';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
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
    <Head title="Login" />
    <div class="card max-w-[370px] w-full">
      <form @submit.prevent="submit" class="card-body flex flex-col gap-5 p-10" id="sign_in_form">
        <div class="text-center mb-2.5">
          <h3 class="text-lg font-medium text-gray-900 leading-none mb-2.5">
            Sign in
          </h3>
          <div class="flex items-center justify-center font-medium">
            <span class="text-2sm text-gray-700 me-1.5">
              Need an account?
            </span>
            <Link class="text-2sm link" :href="route('register')">
              Sign up
            </Link>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-2.5">
          <a class="btn btn-light btn-sm justify-center" href="#">
            <img alt="" class="size-3.5 shrink-0" src="assets/media/brand-logos/google.svg" />
            Use Google
          </a>
          <a class="btn btn-light btn-sm justify-center" href="#">
            <img alt="" class="size-3.5 shrink-0 dark:hidden" src="assets/media/brand-logos/apple-black.svg" />
            <img alt="" class="size-3.5 shrink-0 light:hidden" src="assets/media/brand-logos/apple-white.svg" />
            Use Apple
          </a>
        </div>
        <div class="flex items-center gap-2">
          <span class="border-t border-gray-200 w-full">
          </span>
          <span class="text-2xs text-gray-500 font-medium uppercase">
            Or
          </span>
          <span class="border-t border-gray-200 w-full">
          </span>
        </div>
        <div class="flex flex-col gap-1">
          <label class="form-label font-normal text-gray-900">
            Email
          </label>
          <input v-model="form.email" class="input" placeholder="email@email.com" type="text" value="" />
          <span v-show="form.errors.email" class="form-info text-danger font-normal">
            {{ form.errors.email }}
          </span>
        </div>
        <div class="flex flex-col gap-1">
          <div class="flex items-center justify-between gap-1">
            <label class="form-label font-normal text-gray-900">
              Password
            </label>
            <Link class="text-2sm link shrink-0" :href="route('password.request')">
              Forgot Password?
            </Link>
          </div>
          <div class="input" data-toggle-password="true">
            <input v-model="form.password" name="user_password" placeholder="Enter Password" type="password" value="" />
            <button class="btn btn-icon" data-toggle-password-trigger="true" type="button">
              <i class="ki-filled ki-eye text-gray-500 toggle-password-active:hidden">
              </i>
              <i class="ki-filled ki-eye-slash text-gray-500 hidden toggle-password-active:block">
              </i>
            </button>
          </div>
          <span v-show="form.errors.password" class="form-info text-danger font-normal">
            {{ form.errors.password }}
          </span>
        </div>
        <label class="checkbox-group">
          <Checkbox v-model:checked="form.remember" class="checkbox checkbox-sm" name="check" type="checkbox" value="1" />
          <span class="checkbox-label">
            Remember me
          </span>
        </label>
        <button class="btn btn-primary flex justify-center grow">
          Sign In
        </button>

        <div v-if="status" class="text-center mb-2.5">
          <div class="flex items-center justify-center font-medium">
            <span class="text-2sm text-gray-700 me-1.5">
              {{ status }}
            </span>
          </div>
        </div>

      </form>
    </div>
  </GuestLayout>
</template>
