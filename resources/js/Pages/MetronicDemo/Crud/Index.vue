<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ContainerFixed from '@/LayoutComponents/ContainerFixed.vue';
import Breadcrumbs from "@/LayoutComponents/Breadcrumbs.vue";
import PageHeader from '@/BaseComponents/PageHeader.vue';
</script>

<template>
  <AuthenticatedLayout>

    <Breadcrumbs content="Metronic Demo > CRUD > Index" />

    <ContainerFixed>
      <PageHeader title="CRUD: List data (Index)" />
    </ContainerFixed>

  </AuthenticatedLayout>
</template>
