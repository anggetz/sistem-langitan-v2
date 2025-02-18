<script setup>
import { inject } from "vue";

const breadcrumbItems = inject('breadcrumbItems')
</script>

<template>
  <!-- Breadcrumbs -->
  <div class="flex [.header_&]:below-lg:hidden items-center gap-1.25 text-xs lg:text-sm font-medium mb-2.5 lg:mb-0" data-reparent="true" data-reparent-mode="prepend|lg:prepend" data-reparent-target="#content_container|lg:#header_container">
    <span class="text-gray-700">
      {{ breadcrumbItems[0] }}
    </span>
    <template v-for="item in breadcrumbItems.slice(1)" :key="item">
      <i class="ki-filled ki-right text-gray-500 text-3xs"></i>
      <span class="text-gray-700">
        {{ item }}
      </span>
    </template>
  </div>
  <!-- End of Breadcrumbs -->
</template>
