<script setup lang="ts">
import MetronicGuestLayout from '@/Layouts/MetronicGuestLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

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
  <MetronicGuestLayout>

    <Head title="Forgot Password" />

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
        <div class="flex flex-col gap-1">
          <label class="form-label font-normal text-gray-900">
            Email
          </label>
          <input v-model="form.email" class="input" placeholder="email@email.com" type="text" />
          <span v-show="form.errors.email" class="form-info text-danger font-normal">
            {{ form.errors.email }}
          </span>
        </div>
        <button class="btn btn-primary flex justify-center grow">
          Continue
          <i class="ki-filled ki-black-right">
          </i>
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
  </MetronicGuestLayout>
</template>
