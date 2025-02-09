<template>
    <!-- Mobile Logo -->
    <div class="flex gap-1 lg:hidden items-center -ms-1">
        <a class="shrink-0" href="/">
            <img class="max-h-[25px] w-full" src="/assets/media/app/mini-logo.svg" />
        </a>
        <div class="flex items-center">
            <button class="btn btn-icon btn-light btn-clear btn-sm" data-drawer-toggle="#sidebar">
                <i class="ki-filled ki-menu">
                </i>
            </button>
            <button class="btn btn-icon btn-light btn-clear btn-sm" data-drawer-toggle="#mega_menu_wrapper">
                <i class="ki-filled ki-burger-menu-2">
                </i>
            </button>
        </div>
    </div>
    <!-- End of Mobile Logo -->
</template>
