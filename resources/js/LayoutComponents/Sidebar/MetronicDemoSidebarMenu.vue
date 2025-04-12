<script setup>
import MenuItem from './MenuItem.vue'
import SubmenuItem from './SubmenuItem.vue'
import GroupMenuItem from './GroupMenuItem.vue'
</script>

<template>
  <!-- Sidebar Menu: Admin -->
  <div class="menu flex flex-col grow gap-0.5" data-menu="true" data-menu-accordion-expand-all="false"
    id="sidebar_menu">

    <MenuItem prefix-url="/metronic-demo/dashboard">
    <template #icon>
      <i class="ki-filled ki-element-11 text-lg"></i>
    </template>
    <template #title>
      Dashboards
    </template>
    <template #submenu-items>
      <SubmenuItem href="/metronic-demo/dashboard">Default Dashboard</SubmenuItem>
    </template>
    </MenuItem>

    <GroupMenuItem title="Examples" />

    <MenuItem prefix-url="/metronic-demo/crud">
    <template #icon>
      <i class="ki-filled ki-menu text-lg"></i>
    </template>
    <template #title>
      CRUD
    </template>
    <template #submenu-items>
      <SubmenuItem href="/metronic-demo/crud">Index</SubmenuItem>
      <SubmenuItem href="/metronic-demo/crud/create">Create</SubmenuItem>
    </template>
    </MenuItem>

    <MenuItem prefix-url="/metronic-demo/templates">
    <template #icon>
      <i class="ki-filled ki-abstract-22 text-lg"></i>
    </template>
    <template #title>
      Templates
    </template>
    <template #submenu-items>
      <SubmenuItem href="/metronic-demo/templates/blank">Blank Page</SubmenuItem>
      <SubmenuItem href="/metronic-demo/templates/table">Basic Table</SubmenuItem>
      <SubmenuItem href="/metronic-demo/templates/filter-table">Table with Filter</SubmenuItem>
      <SubmenuItem href="/metronic-demo/templates/upload">Upload</SubmenuItem>
    </template>
    </MenuItem>

    <MenuItem prefix-url="/metronic-demo/components">
    <template #icon>
      <i class="ki-filled ki-abstract-25 text-lg"></i>
    </template>
    <template #title>
      Components
    </template>
    <template #submenu-items>
      <SubmenuItem href="/metronic-demo/components/accordion">Accordion</SubmenuItem>
      <SubmenuItem href="/metronic-demo/components/avatar">Avatar</SubmenuItem>
      <SubmenuItem href="/metronic-demo/components/badge">Badge</SubmenuItem>
      <SubmenuItem href="/metronic-demo/components/button">Button</SubmenuItem>
      <SubmenuItem href="/metronic-demo/components/button-group">Button Group</SubmenuItem>
      <SubmenuItem href="/metronic-demo/components/card">Card</SubmenuItem>
      <SubmenuItem href="/metronic-demo/components/collapse">Collapse</SubmenuItem>
      <SubmenuItem href="/metronic-demo/components/container">Container</SubmenuItem>
      <SubmenuItem href="/metronic-demo/components/dismiss">Dismiss</SubmenuItem>
      <SubmenuItem href="/metronic-demo/components/drawer">Drawer</SubmenuItem>
      <SubmenuItem href="/metronic-demo/components/dropdown">Dropdown</SubmenuItem>
      <SubmenuItem href="/metronic-demo/components/modal">Modal</SubmenuItem>
      <SubmenuItem href="/metronic-demo/components/progress">Progress</SubmenuItem>
      <SubmenuItem href="/metronic-demo/components/rating">Rating</SubmenuItem>
      <SubmenuItem href="/metronic-demo/components/reparent">Reparent</SubmenuItem>
      <SubmenuItem href="/metronic-demo/components/scrollable">Scrollable</SubmenuItem>
      <SubmenuItem href="/metronic-demo/components/table">Table</SubmenuItem>
      <SubmenuItem href="/metronic-demo/components/theme">Theme</SubmenuItem>
      <SubmenuItem href="/metronic-demo/components/toggle">Toggle</SubmenuItem>
      <SubmenuItem href="/metronic-demo/components/tooltip">Tooltip</SubmenuItem>
    </template>
    </MenuItem>

  </div>
  <!-- End of Sidebar Menu -->

</template>
