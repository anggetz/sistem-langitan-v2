<script setup>
import MobileLogo from './MobileLogo.vue';
import MegamenuContainer from './MegamenuContainer.vue';
import Breadcrumbs from './Breadcrumbs.vue';
import Topbar from './Topbar.vue';
</script>
<template>
    <!-- Header -->
    <header class="header fixed top-0 z-10 start-0 end-0 flex items-stretch shrink-0 bg-[--tw-page-bg] dark:bg-[--tw-page-bg-dark]" data-sticky="true" data-sticky-class="shadow-sm" data-sticky-name="header" id="header">
        <!-- Container -->
        <div class="container-fixed flex justify-between items-stretch lg:gap-4" id="header_container">
            <MobileLogo />
            <MegamenuContainer />
            <Topbar />
        </div>
        <!-- End of Container -->
    </header>
    <!-- End of Header -->
</template>
