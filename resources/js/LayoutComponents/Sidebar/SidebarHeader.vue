<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>
    <!-- Sidebar Header -->
    <div class="sidebar-header hidden lg:flex items-center relative justify-between px-3 lg:px-6 shrink-0" id="sidebar_header">
        <Link class="dark:hidden" href="/">
            <img class="default-logo min-h-[22px] max-w-none" src="assets/media/app/default-logo.svg" />
            <img class="small-logo min-h-[22px] max-w-none" src="assets/media/app/mini-logo.svg" />
        </Link>
        <Link class="hidden dark:block" href="/">
            <img class="default-logo min-h-[22px] max-w-none" src="assets/media/app/default-logo-dark.svg" />
            <img class="small-logo min-h-[22px] max-w-none" src="assets/media/app/mini-logo.svg" />
        </Link>
        <button class="btn btn-icon btn-icon-md size-[30px] rounded-lg border border-gray-200 dark:border-gray-300 bg-light text-gray-500 hover:text-gray-700 toggle absolute start-full top-2/4 -translate-x-2/4 -translate-y-2/4 rtl:translate-x-2/4" data-toggle="body" data-toggle-class="sidebar-collapse" id="sidebar_toggle">
            <i class="ki-filled ki-black-left-line toggle-active:rotate-180 transition-all duration-300 rtl:translate rtl:rotate-180 rtl:toggle-active:rotate-0">
            </i>
        </button>
    </div>
    <!-- End of Sidebar Header -->
</template>
