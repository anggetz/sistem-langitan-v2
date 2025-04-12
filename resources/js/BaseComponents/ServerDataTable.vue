<template>
    <div class="space-y-4">
      <!-- Search & Filter -->
      <div class="flex flex-col md:flex-row gap-4 justify-between">
        <input
          v-model="form.search"
          @input="reload"
          type="text"
          :placeholder="searchPlaceholder"
          class="px-4 py-2 border rounded-lg w-full md:w-1/3"
        />
  
        <div v-for="filter in filters" :key="filter.key" class="w-full md:w-1/4">
          <select
            v-model="form[filter.key]"
            @change="reload"
            class="px-4 py-2 border rounded-lg w-full"
          >
            <option :value="''">{{ filter.placeholder }}</option>
            <option
              v-for="option in filter.options"
              :key="option.value"
              :value="option.value"
            >
              {{ option.label }}
            </option>
          </select>
        </div>
      </div>
  
      <!-- Table -->
      <div class="bg-white shadow-md rounded-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th
                v-for="header in headers"
                :key="header.key"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
              >
                {{ header.label }}
              </th>
            </tr>
          </thead>

          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="row in data.data" :key="row.id">
              <td
                v-for="header in headers"
                :key="header.key"
                class="px-6 py-4 whitespace-nowrap"
              >
              <slot
                    :name="`cell-${header.key}`"
                    :value="row[header.key]"
                    :row="row"
                >
                    {{ row[header.key] }}
                </slot>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
  
      <!-- Pagination -->
      <div class="mt-4 flex justify-center gap-2">
        <Link
          v-for="page in data.links"
          :key="page.label"
          :to="page.url"
          class="px-3 py-1 border rounded"
          :class="{ 'bg-blue-500 text-white': page.active, 'text-gray-600': !page.active }"
          v-html="page.label"
        />
      </div>
    </div>
  </template>
  
  <script setup>
  import { reactive } from 'vue'
  import { router, Link } from '@inertiajs/vue3'
  
  const props = defineProps({
    headers: Array,
    filters: Array,
    data: Object,
    modelFilters: Object,
    searchPlaceholder: {
      type: String,
      default: 'Cari...',
    },
    routeName: String,
  })
  
  const form = reactive({
    search: props.modelFilters?.search || '',
  })
  
  props.filters.forEach((f) => {
    form[f.key] = props.modelFilters?.[f.key] || ''
  })
  
  function reload() {
    router.get(route(props.routeName), form, {
      preserveState: true,
      replace: true,
    })
  }
  </script>
  