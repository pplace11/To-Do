<template>
    <div
        v-if="message"
        class="fixed top-4 right-4 z-50 max-w-xs px-4 py-2 rounded text-white shadow-lg"
        :class="messageClass"
        :role="props.type === 'error' ? 'alert' : 'status'"
        :aria-live="props.type === 'error' ? 'assertive' : 'polite'"
        aria-atomic="true">
        {{ message }}
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    type: { type: String, default: 'info' },
    text: String,
    duration: { type: Number, default: 3000 }
});

const message = ref('');

const messageClass = {
    success: 'bg-green-600',
    error: 'bg-red-600',
    info: 'bg-blue-600'
};

watch(() => props.text, (newText) => {
    if (newText) {
        message.value = newText;
        setTimeout(() => { message.value = ''; }, props.duration);
    }
});
</script>
