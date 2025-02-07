<script setup>
import SidebarHeader from './SidebarHeader.vue';
import SidebarContent from './SidebarContent.vue';
</script>

<template>
   <!-- Sidebar -->
   <div class="sidebar dark:bg-coal-600 bg-light border-e border-e-gray-200 dark:border-e-coal-100 fixed top-0 bottom-0 z-20 hidden lg:flex flex-col items-stretch shrink-0" data-drawer="true" data-drawer-class="drawer drawer-start top-0 bottom-0" data-drawer-enable="true|lg:false" id="sidebar">
      <SidebarHeader />
      <SidebarContent />
   </div>
   <!-- End of Sidebar -->
</template>