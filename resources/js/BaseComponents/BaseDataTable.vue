<script setup>
import { onMounted, useTemplateRef } from 'vue'

const props = defineProps({
    apiEndpoint: {
        type: String,
        required: true
    },
    columns: {
        type: Object,
        required: true
    },
    pageSize: {
        type: Number,
        default: 10
    },
    title: {
        type: String,
        default: 'Data Table'
    }
})

const element = useTemplateRef('dataTable')

onMounted(() => {
    const KTDataTable = window.KTDataTable
    if (!KTDataTable) {
        console.error('KTDataTable is not defined')
        return
    }

    new KTDataTable(element.value, {
        apiEndpoint: props.apiEndpoint,
        pageSize: props.pageSize,
        columns: props.columns,
        stateSave: true
    })
})
</script>

<template>
    <div class="grid">

        <div class="card card-grid min-w-full">
            <div class="card-header py-5 flex-wrap">
                <h3 class="card-title">{{ title }}</h3>
                <label class="switch switch-sm">
                    <input checked class="order-2" name="check" type="checkbox" value="1" />
                    <span class="switch-label order-1">Push Alerts</span>
                </label>
            </div>
            <div class="card-body">
                <div ref="dataTable" class="table-responsive" id="kt_remote_table">
                    <div class="scrollable-x-auto">
                        <table class="table table-auto table-border align-middle text-gray-700 font-medium text-sm"
                            data-datatable-table="true">
                            <thead>
                                <tr>
                                    <th v-for="(col, key) in columns" :key="key" :data-datatable-column="key"
                                        class="text-center">
                                        <span class="sort">
                                            <span class="sort-label">{{ col.title }}</span>
                                            <span class="sort-icon"></span>
                                        </span>
                                    </th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <div
                        class="card-footer justify-center md:justify-between flex-col md:flex-row gap-3 text-gray-600 text-2sm font-medium">
                        <div class="flex items-center gap-2">
                            Show
                            <select class="select select-sm w-16" data-datatable-size="true" name="perpage"></select>
                            per page
                        </div>
                        <div class="flex items-center gap-4">
                            <span data-datatable-info="true"></span>
                            <div class="pagination" data-datatable-pagination="true"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
