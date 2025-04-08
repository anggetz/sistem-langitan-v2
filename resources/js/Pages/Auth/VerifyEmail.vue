<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
  <Head title="Email Verification" />
  <GuestLayout>
    <div class="flex items-center justify-center grow bg-center bg-no-repeat page-bg">
      <div class="card max-w-[440px] w-full">
        <div class="card-body p-10">
          <form @submit.prevent="submit">
            <div class="flex justify-center py-10">
              <img alt="image" class="dark:hidden max-h-[130px]" src="assets/media/illustrations/30.svg"/>
              <img alt="image" class="light:hidden max-h-[130px]" src="assets/media/illustrations/30-dark.svg"/>
            </div>
            <h3 class="text-lg font-medium text-gray-900 text-center mb-3">
              Email Verification
            </h3>
            <div class="text-2sm text-center text-gray-700 mb-7.5">
              Before getting started, could you verify your email address by clicking on the link
              we just emailed to you? If you didn't receive the email, we will gladly send you another.
            </div>
            <div class="text-2sm text-center text-success mb-7.5" v-if="verificationLinkSent">
              A new verification link has been sent to the email address you provided during registration.
            </div>
            <div class="flex items-center justify-center gap-1 mb-5">
              <PrimaryButton class="flex justify-center grow" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Resend Verification Email
              </PrimaryButton>
            </div>
            <div class="flex items-center justify-center gap-1">
              <Link
                :href="route('logout')"
                method="post"
                as="button"
                class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >Log Out</Link>
            </div>
          </form>
        </div>
      </div>
    </div>
  </GuestLayout>
</template>
