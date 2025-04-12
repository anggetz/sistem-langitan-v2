<script setup>
import SubmenuItem from './SubmenuItem.vue'
defineProps({
  prefixUrl: String
})
</script>

<template>
  <div class="menu-item" data-menu-item-toggle="accordion" data-menu-item-trigger="click"
    :class="{ 'show': $page.url.startsWith(prefixUrl) }">
    <div
      class="menu-link flex items-center grow cursor-pointer border border-transparent gap-[10px] ps-[10px] pe-[10px] py-[6px]"
      tabindex="0">
      <span class="menu-icon items-start text-gray-500 dark:text-gray-400 w-[20px]">
        <slot name="icon">
          <i class="ki-filled ki-element-2 text-lg"></i>
        </slot>
      </span>
      <span
        class="menu-title text-sm font-medium text-gray-800 menu-item-active:text-primary menu-link-hover:!text-primary">
        <slot name="title">[Menu]</slot>
      </span>
      <span class="menu-arrow text-gray-400 w-[20px] shrink-0 justify-end ms-1 me-[-10px]">
        <i class="ki-filled ki-plus text-2xs menu-item-show:hidden">
        </i>
        <i class="ki-filled ki-minus text-2xs hidden menu-item-show:inline-flex">
        </i>
      </span>
    </div>
    <div
      class="menu-accordion gap-0.5 ps-[10px] relative before:absolute before:start-[20px] before:top-0 before:bottom-0 before:border-s before:border-gray-200">
      <slot name="submenu-items"></slot>
    </div>
  </div>
</template>
