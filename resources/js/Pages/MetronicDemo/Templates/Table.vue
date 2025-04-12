<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ContainerFixed from '@/LayoutComponents/ContainerFixed.vue';
import Breadcrumbs from "@/LayoutComponents/Breadcrumbs.vue";
import PageHeader from '@/BaseComponents/PageHeader.vue';
</script>

<template>
  <AuthenticatedLayout>

    <Breadcrumbs content="Metronic Demo > Templates > Table" />

    <ContainerFixed>
      <PageHeader title="Table" sub-title="Contoh template table" />
    </ContainerFixed>

  </AuthenticatedLayout>
</template>
