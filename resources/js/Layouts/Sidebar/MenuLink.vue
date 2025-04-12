<script setup>
import { Link } from "@inertiajs/vue3";

const props = defineProps(['href', 'method', 'icon', 'textColor'])
const textColorDefined = props.textColor !== undefined
</script>

<template>
  <div class="menu-item">
    <Link class="menu-link cursor-pointer border border-transparent gap-[10px] ps-[10px] pe-[10px] py-[6px]" :href="href" :method="method" tabindex="0">
      <span v-if="!textColorDefined" class="menu-icon items-start text-gray-500 dark:text-gray-400 w-[20px]">
        <i class="text-lg" :class="icon"></i>
      </span>
      <span v-else class="menu-icon items-start w-[20px]" :class="textColor + '-500 dark:' + textColor + '-400'">
        <i class="text-lg" :class="icon"></i>
      </span>
      <span v-if="!textColorDefined" class="menu-title text-sm font-medium text-gray-800 menu-item-active:text-primary menu-link-hover:!text-primary"><slot /></span>
      <span v-else class="menu-title text-sm font-medium"
            :class="textColor + '-500 menu-link-hover:!' + textColor + '-400'"><slot /></span>
    </Link>
  </div>
</template>
