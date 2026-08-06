<script setup lang="ts">
import { Toaster as VueSonnerToaster } from 'vue-sonner';
</script>

<template>
    <VueSonnerToaster
        theme="dark"
        position="top-right"
        :close-button="true"
        :rich-colors="true"
    />
</template>
