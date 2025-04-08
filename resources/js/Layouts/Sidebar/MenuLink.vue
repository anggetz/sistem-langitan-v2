<script setup>
import { Link } from "@inertiajs/vue3";
defineProps(['href', 'method', 'icon'])
</script>

<template>
  <div class="menu-item">
    <Link class="menu-link cursor-pointer border border-transparent gap-[10px] ps-[10px] pe-[10px] py-[6px]" :href="href" :method="method" tabindex="0">
      <span class="menu-icon items-start text-gray-500 dark:text-gray-400 w-[20px]">
        <i class="text-lg" :class="icon"></i>
      </span>
      <span class="menu-title text-sm font-medium text-gray-800 menu-item-active:text-primary menu-link-hover:!text-primary"><slot /></span>
    </Link>
  </div>
</template>
