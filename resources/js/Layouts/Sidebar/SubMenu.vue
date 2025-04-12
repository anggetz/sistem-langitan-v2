<script setup>
import { Link, usePage } from "@inertiajs/vue3";

const props = defineProps(['subMenu', 'menu', 'role'])
const subMenuShow = usePage().url.startsWith('/' + props.role.prefix_url + '/' + props.menu.nm_modul + '/' + props.subMenu.nm_menu)
const subMenuUrl = '/' + props.role.prefix_url + '/' + props.menu.nm_modul + '/' + props.subMenu.nm_menu
</script>

<template>
  <!-- SubMenu -->
  <div class="menu-item" :class="{ 'active': subMenuShow }">
    <span class="menu-link border border-transparent items-center grow menu-item-active:bg-secondary-active dark:menu-item-active:bg-coal-300 dark:menu-item-active:border-gray-100 menu-item-active:rounded-lg hover:bg-secondary-active dark:hover:bg-coal-300 dark:hover:border-gray-100 hover:rounded-lg gap-[14px] ps-[10px] pe-[10px] py-[8px]" tabindex="0">
      <span class="menu-bullet flex w-[6px] -start-[3px] rtl:start-0 relative before:absolute before:top-0 before:size-[6px] before:rounded-full rtl:before:translate-x-1/2 before:-translate-y-1/2 menu-item-active:before:bg-primary menu-item-hover:before:bg-primary"></span>
      <Link class="menu-title text-2sm font-normal text-gray-800 menu-item-active:text-primary menu-item-active:font-semibold menu-link-hover:!text-primary" :href="subMenuUrl">
        {{ subMenu.title }}
      </Link>
    </span>
  </div>
  <!-- End of SubMenu -->
</template>
