<script setup>
import { computed } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'
const user = computed(() => usePage().props.auth.user);
</script>

<template>
    <div class="menu" data-menu="true">
        <div class="menu-item" data-menu-item-offset="20px, 10px" data-menu-item-offset-rtl="-20px, 10px"
            data-menu-item-placement="bottom-end" data-menu-item-placement-rtl="bottom-start"
            data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
            <div class="menu-toggle btn btn-icon rounded-full">
                <img alt="" class="size-9 rounded-full shrink-0 object-cover aspect-square object-top" :src="user.foto">
                </img>
            </div>
            <div class="menu-dropdown menu-default light:border-gray-300 w-screen max-w-[250px]">
                <div class="flex items-center justify-between px-5 py-1.5 gap-1.5">
                    <div class="flex items-center gap-2">
                        <img alt="" class="size-9 rounded-full object-cover aspect-square object-top" :src="user.foto">
                        <div class="flex flex-col gap-1.5">
                            <span class="text-sm text-gray-800 font-semibold leading-none">
                                {{ user.nm_pengguna }}
                            </span>
                            <a class="text-xs text-gray-600 hover:text-primary font-medium leading-none">
                                {{ user.username }}
                            </a>
                        </div>
                        </img>
                    </div>
                    <span class="badge badge-xs badge-primary badge-outline">
                        Pro
                    </span>
                </div>
                <div class="menu-separator">
                </div>
                <div class="flex flex-col">
                  
                    <div class="menu-item">
                        <a class="menu-link" href="html/demo1/account/home/user-profile.html">
                            <span class="menu-icon">
                                <i class="ki-filled ki-profile-circle">
                                </i>
                            </span>
                            <span class="menu-title">
                                My Profile
                            </span>
                        </a>
                    </div>
                </div>
                <div class="menu-separator">
                </div>
                <div class="flex flex-col">
                    <div class="menu-item mb-0.5">
                        <div class="menu-link">
                            <span class="menu-icon">
                                <i class="ki-filled ki-moon">
                                </i>
                            </span>
                            <span class="menu-title">
                                Dark Mode
                            </span>
                            <label class="switch switch-sm">
                                <input data-theme-state="dark" data-theme-toggle="true" name="check" type="checkbox"
                                    value="1">
                                </input>
                            </label>
                        </div>
                    </div>
                    <div class="menu-item px-4 py-1.5">
                        <Link class="btn btn-sm btn-light justify-center" :href="route('logout')" method="post">
                        Log out
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
