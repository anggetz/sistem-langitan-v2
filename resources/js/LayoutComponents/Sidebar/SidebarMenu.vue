<script setup>
import MenuItem from './MenuItem.vue'
import SubmenuItem from './SubmenuItem.vue'
import GroupMenuItem from './GroupMenuItem.vue'
</script>

<template>
      <!-- Sidebar Menu -->
      <div class="menu flex flex-col grow gap-0.5" data-menu="true" data-menu-accordion-expand-all="false" id="sidebar_menu">

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

      </div>
      <!-- End of Sidebar Menu -->
</template>
