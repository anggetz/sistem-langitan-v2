<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
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
  <Head title="Sign up" />
  <GuestLayout>
    <div class="flex items-center justify-center grow bg-center bg-no-repeat page-bg">
      <div class="card max-w-[370px] w-full">
        <form class="card-body flex flex-col gap-5 p-10" id="sign_up_form" @submit.prevent="submit">
        <div class="text-center mb-2.5">
          <h3 class="text-lg font-medium text-gray-900 leading-none mb-2.5">
          Sign up
          </h3>
          <div class="flex items-center justify-center">
          <span class="text-2sm text-gray-700 me-1.5">
            Already have an Account ?
          </span>
          <a class="text-2sm link" :href="route('login')">
            Sign In
          </a>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-2.5">
          <a class="btn btn-light btn-sm justify-center" href="#">
          <img alt="" class="size-3.5 shrink-0" src="assets/media/brand-logos/google.svg"/>
          Use Google
          </a>
          <a class="btn btn-light btn-sm justify-center" href="#">
          <img alt="" class="size-3.5 shrink-0 dark:hidden" src="assets/media/brand-logos/apple-black.svg"/>
          <img alt="" class="size-3.5 shrink-0 light:hidden" src="assets/media/brand-logos/apple-white.svg"/>
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
          <InputLabel for="name">Name</InputLabel>
          <input class="input" name="name" type="text" placeholder="Enter your full name" v-model="form.name" autocomplete="on" />
          <InputError :message="form.errors.name" />
        </div>
        <div class="flex flex-col gap-1">
          <InputLabel for="user_email">Email</InputLabel>
          <input class="input" name="user_email" placeholder="email@email.com" type="text" v-model="form.email" autocomplete="on" />
          <InputError :message="form.errors.email" />
        </div>
        <div class="flex flex-col gap-1">
          <InputLabel>Password</InputLabel>
          <div class="input" data-toggle-password="true">
          <input name="user_password" placeholder="Enter Password" type="password" v-model="form.password">
            <button class="btn btn-icon" data-toggle-password-trigger="true" type="button">
            <i class="ki-filled ki-eye text-gray-500 toggle-password-active:hidden">
            </i>
            <i class="ki-filled ki-eye-slash text-gray-500 hidden toggle-password-active:block">
            </i>
            </button>
          </input>
          </div>
          <InputError :message="form.errors.password" />
        </div>
        <div class="flex flex-col gap-1">
          <InputLabel>Confirm Password</InputLabel>
          <div class="input" data-toggle-password="true">
          <input name="user_password" placeholder="Re-enter Password" type="password" v-model="form.password_confirmation"/>
          <button class="btn btn-icon" data-toggle-password-trigger="true" type="button">
            <i class="ki-filled ki-eye text-gray-500 toggle-password-active:hidden">
            </i>
            <i class="ki-filled ki-eye-slash text-gray-500 hidden toggle-password-active:block">
            </i>
          </button>
          </div>
          <InputError :message="form.errors.password_confirmation" />
        </div>
        <PrimaryButton class="flex justify-center grow">Sign up</PrimaryButton>
        </form>
      </div>
    </div>
  </GuestLayout>
</template>
