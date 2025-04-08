<script setup>
import Search from '@/Layouts/TopBar/Search.vue';
import Chat from '@/Layouts/TopBar/Chat.vue';
import Apps from '@/Layouts/TopBar/Apps.vue';
import Notifications from '@/Layouts/TopBar/Notifications.vue';
import Profile from '@/Layouts/TopBar/Profile.vue';
</script>

<template>
  <!-- Topbar -->
  <div class="flex items-center gap-2 lg:gap-3.5">

    <Search />

    <Chat />

    <Apps />

    <Notifications />

    <Profile />

  </div>
  <!-- End of Topbar -->
</template>
