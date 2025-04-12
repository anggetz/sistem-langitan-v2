<script setup>
import Search from '@/Layouts/TopBar/Search.vue';
import Notifications from '@/Layouts/TopBar/Notifications.vue';
import Profile from '@/Layouts/TopBar/Profile.vue';
</script>

<template>
  <!-- Topbar -->
  <div class="flex items-center gap-2 lg:gap-3.5">

    <Search />

    <Notifications />

    <Profile />

  </div>
  <!-- End of Topbar -->
</template>
