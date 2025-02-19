<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ContainerFixed from '@/LayoutComponents/ContainerFixed.vue';
import Breadcrumbs from "@/LayoutComponents/Breadcrumbs.vue";
import PageHeader from '@/BaseComponents/PageHeader.vue';
</script>

<template>
  <AuthenticatedLayout>

    <Breadcrumbs content="Metronic Demo > Templates > Blank Page" />

    <ContainerFixed>
      <PageHeader title="Blank Page" sub-title="Contoh template Blank Page untuk memulai halaman baru" />
    </ContainerFixed>

  </AuthenticatedLayout>
</template>
