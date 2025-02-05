<script setup>
import { Head } from '@inertiajs/vue3';
import SideBar from '@/Components/SideBar.vue';
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';
</script>

<template>
    <SideBar />
    <!-- Wrapper -->
    <div class="wrapper flex grow flex-col">
        <Header />
        <main class="grow content pt-5" id="content" role="content">
            <slot />
        </main>
        <Footer />
    </div>
    <!-- End of Wrapper -->
</template>
