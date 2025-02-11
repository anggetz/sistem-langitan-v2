<template>
  <!-- Page -->
  <div class="flex items-center justify-center grow bg-center bg-no-repeat page-bg">
    <slot />
  </div>
  <!-- End of Page -->
</template>

<style>
  .page-bg {
    background-image: url('assets/media/images/2600x1200/bg-10.png');
  }
  .dark .page-bg {
    background-image: url('assets/media/images/2600x1200/bg-10-dark.png');
  }
</style>
