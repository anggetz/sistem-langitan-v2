<script setup>
import { computed } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'
const breadcrumbs = computed(() => usePage().props.breadcrumbs)
</script>

<template>
  <!-- Breadcrumbs -->
  <div class="flex [.header_&]:below-lg:hidden items-center gap-1.25 text-xs lg:text-sm font-medium mb-2.5 lg:mb-0" data-reparent="true" data-reparent-mode="prepend|lg:prepend" data-reparent-target="#content_container|lg:#header_container">

    <template v-for="(item, index) in breadcrumbs">
      <Link v-if="item[1] !== undefined" :href="item[1]"><span class="text-gray-700">{{ item[0] }}</span></Link>
      <span v-else class="text-gray-700">{{ item[0] }}</span>
      <i v-if="index < breadcrumbs.length - 1" class="ki-filled ki-right text-gray-500 text-3xs"></i>
    </template>
  </div>
  <!-- End of Breadcrumbs -->
</template>
