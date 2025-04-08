<script setup>
import MobileLogo from '@/Layouts/MobileLogo.vue';
import Breadcrumbs from '@/Layouts/Breadcrumbs.vue';
import TopBar from '@/Layouts/TopBar.vue';
</script>

<template>
  <!-- Header -->
  <header class="header fixed top-0 z-10 start-0 end-0 flex items-stretch shrink-0 bg-[--tw-page-bg] dark:bg-[--tw-page-bg-dark]" data-sticky="true" data-sticky-class="shadow-sm" data-sticky-name="header" id="header">
    <!-- Container -->
    <div class="container-fixed flex justify-between items-stretch lg:gap-4" id="header_container">

      <MobileLogo />

      <Breadcrumbs />

      <TopBar />

    </div>
    <!-- End of Container -->
  </header>
  <!-- End of Header -->
</template>
