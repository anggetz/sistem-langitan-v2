<script setup>
import {onMounted, provide, ref} from 'vue';
import Sidebar from '@/LayoutComponents/Sidebar.vue';
import Header from '@/LayoutComponents/Header.vue';
import Footer from '@/LayoutComponents/Footer.vue';
import KTDom from "../../metronic/core/helpers/dom.js";
import KTComponents from '@/../metronic/core/index';
import KTLayout from '@/../metronic/app/layouts/demo1.js';

const breadcrumbItems = ref(['Default Breadcrumb Item'])

const props = defineProps({
  'useMegamenu': {
    type: Boolean,
    default: false,
  },
  'useBreadcrumbs': {
    type: Boolean,
    default: true,
  },
})

provide('useMegamenu', props.useMegamenu)
provide('useBreadcrumbs', props.useBreadcrumbs)
provide('breadcrumbItems', breadcrumbItems)

onMounted(() => {
  KTDom.ready(() => {
    KTComponents.init()
    KTLayout.init()
  })
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
