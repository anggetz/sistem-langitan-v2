<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ContainerFixed from '@/LayoutComponents/ContainerFixed.vue';
import Breadcrumbs from "@/LayoutComponents/Breadcrumbs.vue";
import PageHeader from '@/BaseComponents/PageHeader.vue';
</script>

<template>
  <AuthenticatedLayout>

    <Breadcrumbs content="Metronic Demo > Templates > Upload" />

    <ContainerFixed>
      <PageHeader title="Upload File" sub-title="Contoh template untuk upload file" />
    </ContainerFixed>

  </AuthenticatedLayout>
</template>
