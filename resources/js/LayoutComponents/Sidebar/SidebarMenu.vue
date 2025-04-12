<script setup>
import { computed, ref, onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import MenuItem from './MenuItem.vue'
import SubmenuItem from './SubmenuItem.vue'
import GroupMenuItem from './GroupMenuItem.vue'

// TODO: Sesuaikan jenis role disini sesuai role aplikasi
const user = computed(() => usePage().props.auth.user)
const menus = usePage().props.menu

console.log(menus)
</script>

<template>

    <!-- Sidebar Menu -->
    <div class="menu flex flex-col grow gap-0.5" data-menu="true" data-menu-accordion-expand-all="false"
        id="sidebar_menu">

        <!-- <GroupMenuItem title="Sidebar Role Lain" /> -->

        <MenuItem prefix-url="" v-for="menu in menus" :key="menu.id_modul">
        <template #icon>
            <i class="ki-filled ki-element-11 text-lg"></i>
        </template>
        <template #title>
            {{ menu.title }}
        </template>
        <template #submenu-items >
            <SubmenuItem :href="menu.nm_modul+'/'+submenu.nm_menu" v-for="submenu in menu.menu_aktif" :key="submenu.id_menu">
                {{ submenu.title }}
            </SubmenuItem>
        </template>
        </MenuItem>

    </div>
    <!-- End of Sidebar Menu -->
</template>
