<script setup>
import { onMounted } from 'vue';
import Sidebar from '@/LayoutComponents/Sidebar.vue';
import Header from '@/LayoutComponents/Header.vue';
import Footer from '@/LayoutComponents/Footer.vue';
import KTLayout from '@/../metronic/app/layouts/demo1.js';

onMounted(() => {
    KTLayout.init();
});
</script>

<template>
    <Sidebar />
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
