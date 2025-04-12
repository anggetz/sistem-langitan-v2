<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ContainerFixed from '@/LayoutComponents/ContainerFixed.vue';
import Breadcrumbs from "@/LayoutComponents/Breadcrumbs.vue";
</script>

<template>
  <AuthenticatedLayout>

    <Breadcrumbs content="Metronic Demo > Components > Container" />

    <ContainerFixed>
      <div class="flex flex-col gap-1.5 mb-5">
        <h1 class="text-2xl font-bold text-gray-900">Container</h1>
      </div>
    </ContainerFixed>

  </AuthenticatedLayout>
</template>
