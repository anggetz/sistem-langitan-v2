<script setup>
import { computed } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'
import Search from '@/Layouts/TopBar/Search.vue';
import Notifications from '@/Layouts/TopBar/Notifications.vue';
import Profile from '@/Layouts/TopBar/Profile.vue';

const user = computed(() => usePage().props.auth.user)
</script>

<template>
  <!-- Topbar -->
  <div class="flex items-center gap-2 lg:gap-3.5">

    <!-- <Search /> -->

    <!-- <Notifications /> -->

    <div class="text-xs lg:text-sm font-medium mb-2.5 lg:mb-0">
      {{ user.nama_lengkap }}
    </div>

    <Profile />

  </div>
  <!-- End of Topbar -->
</template>
