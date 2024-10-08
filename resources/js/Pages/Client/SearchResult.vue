<script setup>

import {ref, watch} from "vue";

let props = defineProps({
	errors: Object,
});

let errorMessages = ref(props.errors);

watch(() => props.errors, (newErrors) => {
	errorMessages.value = newErrors;
});
</script>

<template>

</template>

<style scoped>

</style>