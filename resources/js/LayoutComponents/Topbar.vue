<script setup>
import SearchButton from './Topbar/SearchButton.vue';
import SearchModal from './Topbar/SearchModal.vue';
import Chat from './Topbar/Chat.vue';
import Apps from './Topbar/Apps.vue';
import Notifications from './Topbar/Notifications.vue';
import ProfileMenu from './Topbar/ProfileMenu.vue';
</script>

<template>
      <!-- Topbar -->
      <div class="flex items-center gap-2 lg:gap-3.5">
       <SearchButton />
       <Chat />
       <Apps />
       <Notifications />
       <ProfileMenu />
      </div>
      <!-- End of Topbar -->
      <!-- Search Modal -->
      <SearchModal /> 
      <!-- End of Search Modal -->
</template>