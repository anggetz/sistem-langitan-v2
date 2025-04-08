<script setup>
import { nextTick, onMounted } from 'vue';
import Sidebar from '@/Layouts/Sidebar.vue';
import Header from '@/Layouts/Header.vue';
import KTComponent from '../../metronic/core/index'; // Import all components, if will use specific component please use single import
import KTLayout from '../../metronic/app/layouts/demo1.js';

onMounted(() => {
    nextTick(() => {
        KTComponent.init();
        KTLayout.init();
    });
});
</script>

<template>

  <div class="flex grow">

    <Sidebar />

    <!-- Wrappper -->
    <div class="wrapper flex grow flex-col">

      <Header />

      <slot />

    </div>
    <!-- End of Wrapper -->

  </div>

  <portal-target name="modal"></portal-target>

</template>
