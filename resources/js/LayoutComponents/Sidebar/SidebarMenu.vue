<script setup>
import { ref, onMounted } from 'vue'
import MenuItem from './MenuItem.vue'
import SubmenuItem from './SubmenuItem.vue'
import GroupMenuItem from './GroupMenuItem.vue'

// Definisi role disini menyesuaikan daftar role aplikasi
const roleAdmin = ref(null)
const roleOther = ref(null)

onMounted(async () => {
  try {
    const response = await fetch('/examples/get-profile')
    const data = await response.json()
    if (data.role_id === 1) {
      roleAdmin.value = true;
    } else if (data.role_id === 2) {
      roleOther.value = true;
    }
  } catch (error) {
    console.error('Failed to fetch profile:', error)
  }
})
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

    </template>
    </MenuItem>

  </div>
  <!-- End of Sidebar Menu -->

  <!-- Sidebar Menu: Other -->
  <div v-if="roleOther" class="menu flex flex-col grow gap-0.5" data-menu="true" data-menu-accordion-expand-all="false" id="sidebar_menu">

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

</template>
