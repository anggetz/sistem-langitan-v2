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

    <MenuItem prefix-url="/examples/dashboard">
    <template #icon>
      <i class="ki-filled ki-element-11 text-lg"></i>
    </template>
    <template #title>
      Dashboards
    </template>
    <template #submenu-items>
      <SubmenuItem href="/examples/dashboard">Default Dashboard</SubmenuItem>
    </template>
    </MenuItem>

    <GroupMenuItem title="Examples" />

    <MenuItem prefix-url="/examples/crud">
    <template #icon>
      <i class="ki-filled ki-menu text-lg"></i>
    </template>
    <template #title>
      CRUD
    </template>
    <template #submenu-items>
      <SubmenuItem href="/examples/crud/index">Index</SubmenuItem>
      <SubmenuItem href="/examples/crud/create">Create</SubmenuItem>
    </template>
    </MenuItem>

    <MenuItem prefix-url="/examples/templates">
    <template #icon>
      <i class="ki-filled ki-abstract-22 text-lg"></i>
    </template>
    <template #title>
      Templates
    </template>
    <template #submenu-items>
      <SubmenuItem href="/examples/templates/blank">Blank Page</SubmenuItem>
      <SubmenuItem href="/examples/templates/table">Basic Table</SubmenuItem>
      <SubmenuItem href="/examples/templates/filter-table">Table with Filter</SubmenuItem>
      <SubmenuItem href="/examples/templates/upload">Upload</SubmenuItem>
    </template>
    </MenuItem>

    <MenuItem prefix-url="/examples/components">
    <template #icon>
      <i class="ki-filled ki-abstract-25 text-lg"></i>
    </template>
    <template #title>
      Components
    </template>
    <template #submenu-items>
      <SubmenuItem href="/examples/components/accordion">Accordion</SubmenuItem>
      <SubmenuItem href="/examples/components/accordion">Avatar</SubmenuItem>
      <SubmenuItem href="/examples/components/badge">Badge</SubmenuItem>
      <SubmenuItem href="/examples/components/button">Button</SubmenuItem>
      <SubmenuItem href="/examples/components/button-group">Button Group</SubmenuItem>
      <SubmenuItem href="/examples/components/card">Card</SubmenuItem>
      <SubmenuItem href="/examples/components/collapse">Collapse</SubmenuItem>
      <SubmenuItem href="/examples/components/container">Container</SubmenuItem>
      <SubmenuItem href="/examples/components/dismiss">Dismiss</SubmenuItem>
      <SubmenuItem href="/examples/components/drawer">Drawer</SubmenuItem>
      <SubmenuItem href="/examples/components/dropdown">Dropdown</SubmenuItem>
      <SubmenuItem href="/examples/components/modal">Modal</SubmenuItem>
      <SubmenuItem href="/examples/components/progress">Progress</SubmenuItem>
      <SubmenuItem href="/examples/components/rating">Rating</SubmenuItem>
      <SubmenuItem href="/examples/components/reparent">Reparent</SubmenuItem>
      <SubmenuItem href="/examples/components/scrollable">Scrollable</SubmenuItem>
      <SubmenuItem href="/examples/components/table">Table</SubmenuItem>
      <SubmenuItem href="/examples/components/theme">Theme</SubmenuItem>
      <SubmenuItem href="/examples/components/toggle">Toggle</SubmenuItem>
      <SubmenuItem href="/examples/components/tooltip">Tooltip</SubmenuItem>
    </template>
    </MenuItem>

  </div>
  <!-- End of Sidebar Menu -->

  <!-- Sidebar Menu: User -->
  <div v-else-if="roleUser" class="menu flex flex-col grow gap-0.5" data-menu="true" data-menu-accordion-expand-all="false" id="sidebar_menu">

    <GroupMenuItem title="Sidebar Role User" />

    <MenuItem prefix-url="/examples/dashboard">
    <template #icon>
      <i class="ki-filled ki-element-11 text-lg"></i>
    </template>
    <template #title>
      Dashboards
    </template>
    <template #submenu-items>
      <SubmenuItem href="/examples/dashboard">Default Dashboard</SubmenuItem>
    </template>
    </MenuItem>

    <GroupMenuItem title="Examples" />

    <MenuItem prefix-url="/examples/crud">
    <template #icon>
      <i class="ki-filled ki-menu text-lg"></i>
    </template>
    <template #title>
      CRUD
    </template>
    <template #submenu-items>
      <SubmenuItem href="/examples/crud/index">Index</SubmenuItem>
      <SubmenuItem href="/examples/crud/create">Create</SubmenuItem>
    </template>
    </MenuItem>

  </div>
  <!-- End of Sidebar Menu -->

  <!-- Sidebar Menu: Lain -->
  <div v-else class="menu flex flex-col grow gap-0.5" data-menu="true" data-menu-accordion-expand-all="false" id="sidebar_menu">

    <GroupMenuItem title="Sidebar Role Lain" />

    <MenuItem prefix-url="/examples/dashboard">
    <template #icon>
      <i class="ki-filled ki-element-11 text-lg"></i>
    </template>
    <template #title>
      Dashboards
    </template>
    <template #submenu-items>
      <SubmenuItem href="/examples/dashboard">Default Dashboard</SubmenuItem>
    </template>
    </MenuItem>

  </div>
  <!-- End of Sidebar Menu -->
</template>
