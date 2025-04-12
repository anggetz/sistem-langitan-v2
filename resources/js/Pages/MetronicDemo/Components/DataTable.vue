<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ContainerFixed from '@/LayoutComponents/ContainerFixed.vue';
import { onMounted, useTemplateRef } from 'vue';
import BaseDataTable from '@/BaseComponents/BaseDataTable.vue';

const apiUrl = 'http://127.0.0.1:8000/api/metronic-demo/data-table';
const columns = {
  status: {
    title: 'Status',
    render: (item) => `<span class="badge badge-dot size-2 ${item}"></span>`,
    createdCell(cell) {
      cell.classList.add('text-center')
    }
  },
  ipAddress: {
    title: 'IP Address'
  },
  lastSession: {
    title: 'Last Session'
  },
  label: {
    title: 'Label'
  },
  method: {
    title: 'Method'
  },
  action :{
    title: 'Action',
    render: (item,data) => `<a href="${data.id}" class="btn btn-sm btn-info">View</a>`,
    createdCell(cell) {
      cell.classList.add('text-center')
    }
  }
}


</script>

<template>
    <AuthenticatedLayout>

        <ContainerFixed>
            <div class="flex flex-col gap-1.5 mb-5">
                <h1 class="text-2xl font-bold text-gray-900">Table</h1>
                <BaseDataTable :api-endpoint="apiUrl" :columns="columns" :page-size="5" title="Remote Data Source" />
            </div>
        </ContainerFixed>

    </AuthenticatedLayout>
</template>
