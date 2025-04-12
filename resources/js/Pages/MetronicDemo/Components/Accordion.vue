<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ContainerFixed from '@/LayoutComponents/ContainerFixed.vue';
import Breadcrumbs from "@/LayoutComponents/Breadcrumbs.vue";
</script>

<template>
  <AuthenticatedLayout>

    <Breadcrumbs content="Metronic Demo > Components > Accordion" />

    <ContainerFixed>
      <div class="flex flex-col gap-1.5 mb-5">
        <h1 class="text-2xl font-bold text-gray-900">Accordion</h1>
      </div>
    </ContainerFixed>

    <ContainerFixed>
      <div class="flex flex-col gap-1.5 mb-5">
        <h2 class="text-xl font-semibold text-gray-900 leading-none">Default Accordion</h2>
      </div>
    </ContainerFixed>

    <ContainerFixed>
      <div class="card shadow-none">
        <div class="card-body p-5">
          <!-- Default Accordion -->
          <div data-accordion="true">
            <div class="accordion-item [&:not(:last-child)]:border-b border-b-gray-200" data-accordion-item="true"
              id="accordion_1_item_1">
              <button class="accordion-toggle py-4 group" data-accordion-toggle="#accordion_1_content_1">
                <span class="text-base text-gray-900 font-medium">
                  How is pricing determined for each plan ?
                </span>
                <i class="ki-outline ki-plus text-gray-600 text-2sm accordion-active:hidden block">
                </i>
                <i class="ki-outline ki-minus text-gray-600 text-2sm accordion-active:block hidden">
                </i>
              </button>
              <div class="accordion-content hidden" id="accordion_1_content_1">
                <div class="text-gray-700 text-md pb-4">
                  Metronic embraces flexible licensing options that empower you to choose the perfect fit for your
                  project's
                  needs and budget. Understanding the factors influencing each plan's pricing helps you make an informed
                  decision.
                </div>
              </div>
            </div>
            <div class="accordion-item [&:not(:last-child)]:border-b border-b-gray-200" data-accordion-item="true"
              id="accordion_1_item_2">
              <button class="accordion-toggle py-4 group" data-accordion-toggle="#accordion_1_content_2">
                <span class="text-base text-gray-900 font-medium">
                  What payment methods are accepted for subscriptions ?
                </span>
                <i class="ki-outline ki-plus text-gray-600 text-2sm accordion-active:hidden block">
                </i>
                <i class="ki-outline ki-minus text-gray-600 text-2sm accordion-active:block hidden">
                </i>
              </button>
              <div class="accordion-content hidden" id="accordion_1_content_2">
                <div class="text-gray-700 text-md pb-4">
                  Metronic embraces flexible licensing options that empower you to choose the perfect fit for your
                  project's
                  needs and budget. Understanding the factors influencing each plan's pricing helps you make an informed
                  decision.
                </div>
              </div>
            </div>
            <div class="accordion-item [&:not(:last-child)]:border-b border-b-gray-200" data-accordion-item="true"
              id="accordion_1_item_3">
              <button class="accordion-toggle py-4 group" data-accordion-toggle="#accordion_1_content_3">
                <span class="text-base text-gray-900 font-medium">
                  Are there any hidden fees in the pricing ?
                </span>
                <i class="ki-outline ki-plus text-gray-600 text-2sm accordion-active:hidden block">
                </i>
                <i class="ki-outline ki-minus text-gray-600 text-2sm accordion-active:block hidden">
                </i>
              </button>
              <div class="accordion-content hidden" id="accordion_1_content_3">
                <div class="text-gray-700 text-md pb-4">
                  Metronic embraces flexible licensing options that empower you to choose the perfect fit for your
                  project's
                  needs and budget. Understanding the factors influencing each plan's pricing helps you make an informed
                  decision.
                </div>
              </div>
            </div>
          </div>
          <!-- Default Accordion -->
        </div>
      </div>
    </ContainerFixed>

  </AuthenticatedLayout>
</template>
