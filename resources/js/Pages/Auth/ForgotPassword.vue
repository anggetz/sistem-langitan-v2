<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
  <Head title="Forgot password" />
  <GuestLayout>
    <div class="flex items-center justify-center grow bg-center bg-no-repeat page-bg">
    <div class="card max-w-[370px] w-full">
      <form @submit.prevent="submit" class="card-body flex flex-col gap-5 p-10" id="reset_password_enter_email_form">
      <div class="text-center">
        <h3 class="text-lg font-medium text-gray-900">
        Your Email
        </h3>
        <span class="text-2sm text-gray-700">
        Enter your email to reset password
        </span>
      </div>
      <div v-if="status" class="flex flex-col gap-1">
        <div class="font-medium text-center text-2sm text-success">
          {{ status }}
        </div>
      </div>
      <div class="flex flex-col gap-1">
        <InputLabel for="email">Email</InputLabel>
        <input class="input" placeholder="email@email.com" type="text" v-model="form.email" autocomplete="on" />
        <InputError :message="form.errors.email" />
      </div>
      <PrimaryButton class="flex justify-center grow" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
        Continue
        <i class="ki-filled ki-black-right">
        </i>
      </PrimaryButton>
      </form>
    </div>
    </div>
  </GuestLayout>
</template>
