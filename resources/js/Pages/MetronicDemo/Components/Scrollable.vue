<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ContainerFixed from '@/LayoutComponents/ContainerFixed.vue';
</script>

<template>
  <AuthenticatedLayout>

    <ContainerFixed>
      <div class="flex flex-col gap-1.5 mb-5">
        <h1 class="text-2xl font-bold text-gray-900">Scrollable</h1>
      </div>
    </ContainerFixed>

  </AuthenticatedLayout>
</template>
