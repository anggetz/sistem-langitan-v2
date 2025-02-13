<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

const form = useForm({
    name: '',
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

    <div class="card max-w-[370px] w-full">
      <form @submit.prevent="submit" class="card-body flex flex-col gap-5 p-10" id="sign_up_form">
        <div class="text-center mb-2.5">
          <h3 class="text-lg font-medium text-gray-900 leading-none mb-2.5">
            Sign up
          </h3>
          <div class="flex items-center justify-center">
            <span class="text-2sm text-gray-700 me-1.5">
              Already have an Account ?
            </span>
            <Link class="text-2sm link" :href="route('login')">
            Sign In
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
          <span class="text-2xs text-gray-600 uppercase">
            or
          </span>
          <span class="border-t border-gray-200 w-full">
          </span>
        </div>
        <div class="flex flex-col gap-1">
          <label class="form-label text-gray-900">
            Name
          </label>
          <input v-model="form.name" class="input" name="name" placeholder="Your name" type="text" value="" />
          <span v-show="form.errors.name" class="form-info text-danger font-normal">
            {{ form.errors.name }}
          </span>
        </div>
        <div class="flex flex-col gap-1">
          <label class="form-label text-gray-900">
            Email
          </label>
          <input v-model="form.email" class="input" name="user_email" placeholder="email@email.com" type="text" value="" />
          <span v-show="form.errors.email" class="form-info text-danger font-normal">
            {{ form.errors.email }}
          </span>
        </div>
        <div class="flex flex-col gap-1">
          <label class="form-label font-normal text-gray-900">
            Password
          </label>
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
        <div class="flex flex-col gap-1">
          <label class="form-label font-normal text-gray-900">
            Confirm Password
          </label>
          <div class="input" data-toggle-password="true">
            <input v-model="form.password_confirmation" name="user_password" placeholder="Re-enter Password" type="password" value="" />
            <button class="btn btn-icon" data-toggle-password-trigger="true" type="button">
              <i class="ki-filled ki-eye text-gray-500 toggle-password-active:hidden">
              </i>
              <i class="ki-filled ki-eye-slash text-gray-500 hidden toggle-password-active:block">
              </i>
            </button>
          </div>
          <span v-show="form.errors.password_confirmation" class="form-info text-danger font-normal">
              {{ form.errors.password }}
            </span>
        </div>
        <label class="checkbox-group">
          <input class="checkbox checkbox-sm" name="check" type="checkbox" value="1" />
          <span class="checkbox-label">
            I accept
            <a class="text-2sm link" href="#">
              Terms & Conditions
            </a>
          </span>
        </label>
        <button class="btn btn-primary flex justify-center grow" :class="{ 'disabled': form.processing }" :disabled="form.processing" type="submit">
          Sign up
        </button>
      </form>
    </div>
  </GuestLayout>
</template>
