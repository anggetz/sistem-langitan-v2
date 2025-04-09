<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

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
  <Head title="Sign in" />
  <GuestLayout>
    <div class="flex items-center justify-center grow bg-center bg-no-repeat page-bg">
    <div class="card max-w-[370px] w-full">
      <form class="card-body flex flex-col gap-5 p-10" id="sign_in_form" @submit.prevent="submit">
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
        <span class="text-2xs text-gray-500 font-medium uppercase">
        Or
        </span>
        <span class="border-t border-gray-200 w-full">
        </span>
      </div>
      <div v-if="status" class="flex flex-col gap-1">
        <div class="font-medium text-center text-2sm text-success">
          {{ status }}
        </div>
      </div>
      <div class="flex flex-col gap-1">
        <InputLabel for="email">Email</InputLabel>
        <input class="input" type="email" placeholder="email@email.com" v-model="form.email" autocomplete="on" />
        <InputError :message="form.errors.email" />
      </div>
      <div class="flex flex-col gap-1">
        <div class="flex items-center justify-between gap-1">
        <InputLabel>Password</InputLabel>
        <Link class="text-2sm link shrink-0" :href="route('password.request')">
          Forgot Password?
        </Link>
        </div>
        <div class="input" data-toggle-password="true">
        <input name="password" placeholder="Enter Password" type="password" v-model="form.password"/>
        <button class="btn btn-icon" data-toggle-password-trigger="true" type="button">
          <i class="ki-filled ki-eye text-gray-500 toggle-password-active:hidden">
          </i>
          <i class="ki-filled ki-eye-slash text-gray-500 hidden toggle-password-active:block">
          </i>
        </button>
        </div>
        <InputError :message="form.errors.password" />
      </div>
      <label class="checkbox-group">
        <Checkbox class="checkbox-sm" name="remember" v-model:checked="form.remember" />
        <!-- <input class="checkbox checkbox-sm" name="check" type="checkbox" value="1"/> -->
        <span class="checkbox-label">
        Remember me
        </span>
      </label>
      <PrimaryButton class="flex justify-center grow">
        Sign In
      </PrimaryButton>
      </form>
    </div>
    </div>
  </GuestLayout>
</template>
