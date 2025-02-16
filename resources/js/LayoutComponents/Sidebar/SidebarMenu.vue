<script setup>
import { computed, ref, onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import MenuItem from './MenuItem.vue'
import SubmenuItem from './SubmenuItem.vue'
import GroupMenuItem from './GroupMenuItem.vue'

// TODO: Sesuaikan jenis role disini sesuai role aplikasi
const user = computed(() => usePage().props.auth.user)
const roleAdmin = ref(user.value.role === 'Admin')
const roleUser = ref(user.value.role === 'User')
</script>

<template>
  <!-- Sidebar Menu: Admin -->
  <div v-if="roleAdmin" class="menu flex flex-col grow gap-0.5" data-menu="true" data-menu-accordion-expand-all="false" id="sidebar_menu">

    <MenuItem prefix-url="/dashboard">
    <template #icon>
      <i class="ki-filled ki-element-11 text-lg"></i>
    </template>
    <template #title>
      Dashboards
    </template>
    <template #submenu-items>
      <SubmenuItem href="/dashboard">Default Dashboard</SubmenuItem>
    </template>
    </MenuItem>

  </div>
  <!-- End of Sidebar Menu -->

  <!-- Sidebar Menu: User -->
  <div v-else-if="roleUser" class="menu flex flex-col grow gap-0.5" data-menu="true" data-menu-accordion-expand-all="false" id="sidebar_menu">

    <GroupMenuItem title="Sidebar Role User" />

    <MenuItem prefix-url="/dashboard">
    <template #icon>
      <i class="ki-filled ki-element-11 text-lg"></i>
    </template>
    <template #title>
      Dashboards
    </template>
    <template #submenu-items>
      <SubmenuItem href="/dashboard">Default Dashboard</SubmenuItem>
    </template>
    </MenuItem>

  </div>
  <!-- End of Sidebar Menu -->

  <!-- Sidebar Menu: Lain -->
  <div v-else class="menu flex flex-col grow gap-0.5" data-menu="true" data-menu-accordion-expand-all="false" id="sidebar_menu">

    <GroupMenuItem title="Sidebar Role Lain" />

    <MenuItem prefix-url="/dashboard">
    <template #icon>
      <i class="ki-filled ki-element-11 text-lg"></i>
    </template>
    <template #title>
      Dashboards
    </template>
    <template #submenu-items>
      <SubmenuItem href="/dashboard">Default Dashboard</SubmenuItem>
    </template>
    </MenuItem>

  </div>
  <!-- End of Sidebar Menu -->
</template>
