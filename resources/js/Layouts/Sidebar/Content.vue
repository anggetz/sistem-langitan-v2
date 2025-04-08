<script setup>
import { computed } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'
import MenuLink from "@/Layouts/Sidebar/MenuLink.vue";
import Menu from "@/Layouts/Sidebar/Menu.vue";
const auth = computed(() => usePage().props.auth)
</script>

<template>
  <!-- Sidebar Content -->
  <div class="sidebar-content flex grow shrink-0 py-5 pe-2" id="sidebar_content">
    <div class="scrollable-y-hover grow shrink-0 flex ps-2 lg:ps-5 pe-1 lg:pe-3" data-scrollable="true" data-scrollable-dependencies="#sidebar_header" data-scrollable-height="auto" data-scrollable-offset="0px" data-scrollable-wrappers="#sidebar_content" id="sidebar_scrollable">
      <!-- Sidebar Menu -->
      <div class="menu flex flex-col grow gap-0.5" data-menu="true" data-menu-accordion-expand-all="false" id="sidebar_menu">

        <MenuLink :href="auth.home_url" icon="ki-filled ki-home">Home</MenuLink>

        <div class="menu-item pt-2.25 pb-px">
          <span class="menu-heading uppercase text-2sm font-medium text-gray-500 ps-[10px] pe-[10px]">
            {{ auth.role.name }}
          </span>
        </div>

        <Menu v-for="menu in auth.menus" :menu="menu" />

        <div class="menu-separator"></div>

        <MenuLink :href="route('logout')" method="post">Logout</MenuLink>

      </div>
      <!-- End of Sidebar Menu -->
    </div>
  </div>
  <!-- End of Sidebar Content -->
</template>
