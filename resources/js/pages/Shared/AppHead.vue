<script setup lang="ts">
import { Head } from '@inertiajs/vue3'

defineProps({ title: String })
</script>

<template>
  <Head :title="title ? `${title} ` : 'My Page in '">
    <meta head-key="description" name="description" content="This is the default description" />
    <slot />
  </Head>
</template>
