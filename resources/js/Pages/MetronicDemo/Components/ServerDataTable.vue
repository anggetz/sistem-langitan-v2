<template>
  <AuthenticatedLayout>
    <ContainerFixed>
      <div class="flex flex-col gap-1.5 mb-5">
        <h1 class="text-2xl font-bold text-gray-900">Table</h1>

        <ServerDataTable
          :headers="headers"
          :filters="filters"
          :data="users"
          :model-filters="filtersData"
          route-name="metronic-demo.data-table.index"
        >
          <!-- Custom badge untuk role -->
          <template #cell-role="{ value }">
            <span
              class="px-2 py-1 text-xs font-semibold rounded-full"
              :class="{
                'bg-green-100 text-green-800': value === 'admin',
                'bg-blue-100 text-blue-800': value === 'user',
              }"
            >
              {{ value }}
            </span>
          </template>

          <!-- Kolom action -->
          <template #cell-action="{ row }">
            <div class="flex gap-2">
              <button
                @click="editUser(row.id)"
                class="text-blue-600 hover:underline text-sm"
              >
                Edit
              </button>
              <button
                @click="deleteUser(row.id)"
                class="text-red-600 hover:underline text-sm"
              >
                Hapus
              </button>
            </div>
          </template>
        </ServerDataTable>
      </div>
    </ContainerFixed>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ContainerFixed from '@/LayoutComponents/ContainerFixed.vue';
import ServerDataTable from "@/BaseComponents/ServerDataTable.vue";

const headers = [
  { key: "username", label: "Nama" },
  { key: "nm_pengguna", label: "Nama" },
  { key: "email", label: "Email" },
  { key: "role", label: "Peran" },
  { key: "action", label: "Aksi" }, // kolom tambahan
];

const filters = [
  {
    key: "role",
    placeholder: "Filter peran",
    options: [
      { value: "admin", label: "Admin" },
      { value: "user", label: "User" },
    ],
  },
];

const props = defineProps({
  users: Object,
  filters: Object,
});

const filtersData = props.filters;

function editUser(id) {
  // buka modal / navigate ke edit
  console.log("Edit user", id);
}

function deleteUser(id) {
  if (confirm("Yakin ingin menghapus?")) {
    console.log("Delete user", id);
    // Kirim request delete ke backend
  }
}
</script>
