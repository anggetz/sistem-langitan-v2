<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import SidebarMenu from './SidebarMenu.vue'
import MetronicDemoSidebarMenu from '@/LayoutComponents/Sidebar/MetronicDemoSidebarMenu.vue'

const useDemoSidebar = computed(() => usePage().props.useDemoSidebar)
</script>

<template>
    <div class="sidebar-content flex grow shrink-0 py-5 pe-2" id="sidebar_content">
     <div class="scrollable-y-hover grow shrink-0 flex ps-2 lg:ps-5 pe-1 lg:pe-3" data-scrollable="true" data-scrollable-dependencies="#sidebar_header" data-scrollable-height="auto" data-scrollable-offset="0px" data-scrollable-wrappers="#sidebar_content" id="sidebar_scrollable">
      <SidebarMenu v-if="!useDemoSidebar" />
      <MetronicDemoSidebarMenu v-else />
     </div>
    </div>
</template>
