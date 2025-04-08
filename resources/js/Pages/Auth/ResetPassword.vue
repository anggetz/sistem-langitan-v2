<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
  <Head title="Reset password" />
  <GuestLayout>
    <div class="flex items-center justify-center grow bg-center bg-no-repeat page-bg">
    <div class="card max-w-[370px] w-full">
      <form @submit.prevent="submit" class="card-body flex flex-col gap-5 p-10" id="reset_password_change_password_form">
      <div class="text-center">
        <h3 class="text-lg font-medium text-gray-900">
        Reset Password
        </h3>
        <span class="text-2sm text-gray-700">
        Enter your new password
        </span>
      </div>
      <div class="flex flex-col gap-1">
        <InputLabel for="user_email">Email</InputLabel>
        <input class="input" name="user_email" placeholder="email@email.com" type="text" v-model="form.email" readonly />
        <InputError :message="form.errors.email" />
      </div>
      <div class="flex flex-col gap-1">
        <InputLabel for="user_new_password">New Password</InputLabel>
        <label class="input" data-toggle-password="true">
        <input name="user_new_password" placeholder="Enter a new password" type="password" v-model="form.password"/>
        <div class="btn btn-icon" data-toggle-password-trigger="true">
          <i class="ki-filled ki-eye text-gray-500 toggle-password-active:hidden">
          </i>
          <i class="ki-filled ki-eye-slash text-gray-500 hidden toggle-password-active:block">
          </i>
        </div>
        </label>
        <InputError :message="form.errors.password" />
      </div>
      <div class="flex flex-col gap-1">
        <InputLabel for="user_confirm_password">Confirm New Password</InputLabel>
        <label class="input" data-toggle-password="true">
        <input name="user_confirm_password" placeholder="Re-enter a new Password" type="password" v-model="form.password_confirmation"/>
        <div class="btn btn-icon" data-toggle-password-trigger="true">
          <i class="ki-filled ki-eye text-gray-500 toggle-password-active:hidden">
          </i>
          <i class="ki-filled ki-eye-slash text-gray-500 hidden toggle-password-active:block">
          </i>
        </div>
        </label>
        <InputError :message="form.errors.password_confirmation" />
      </div>
      <PrimaryButton class="flex justify-center grow">Submit</PrimaryButton>
      </form>
    </div>
    </div>
  </GuestLayout>
</template>
