<script setup>
import {inject, onMounted} from 'vue'
import MobileLogo from './MobileLogo.vue';
import MegamenuContainer from './MegamenuContainer.vue';
import BreadcrumbsContainer from './BreadcrumbsContainer.vue';
import Topbar from './Topbar.vue';

const useMegamenu = inject('useMegamenu')
const useBreadcrumbs = inject('useBreadcrumbs')
</script>

<template>
    <!-- Header -->
    <header class="header fixed top-0 z-10 start-0 end-0 flex items-stretch shrink-0 bg-[--tw-page-bg] dark:bg-[--tw-page-bg-dark]" data-sticky="true" data-sticky-class="shadow-sm" data-sticky-name="header" id="header">
        <!-- Container -->
        <div class="container-fixed flex justify-between items-stretch lg:gap-4" id="header_container">
            <MobileLogo />
            <MegamenuContainer v-if="useMegamenu" />
            <BreadcrumbsContainer v-if="useBreadcrumbs" />
            <Topbar />
        </div>
        <!-- End of Container -->
    </header>
    <!-- End of Header -->
</template>
