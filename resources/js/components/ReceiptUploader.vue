<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { Camera, ImagePlus, X, RotateCcw, Check } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';

interface Props {
    modelValue: File | null;
    existingUrl?: string | null;
    error?: string;
    disabled?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    existingUrl: null,
    error: '',
    disabled: false,
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: File | null): void;
    (e: 'remove-existing'): void;
}>();

const fileInput = ref<HTMLInputElement | null>(null);
const cameraInput = ref<HTMLInputElement | null>(null);
const previewUrl = ref<string | null>(null);
const isDragging = ref(false);
const showOptions = ref(false);

// Computed preview - either new file preview or existing receipt
const displayUrl = computed(() => {
    return previewUrl.value || props.existingUrl;
});

const hasNewFile = computed(() => {
    return previewUrl.value !== null;
});

// Watch for external file changes
watch(() => props.modelValue, (newFile) => {
    if (!newFile) {
        previewUrl.value = null;
    }
});

const openCamera = () => {
    showOptions.value = false;
    cameraInput.value?.click();
    // Haptic feedback
    if (navigator.vibrate) {
        navigator.vibrate(10);
    }
};

const openGallery = () => {
    showOptions.value = false;
    fileInput.value?.click();
    // Haptic feedback
    if (navigator.vibrate) {
        navigator.vibrate(10);
    }
};

const handleFileSelect = (event: Event) => {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0];

    if (file) {
        processFile(file);
    }

    // Reset input value to allow selecting same file again
    input.value = '';
};

const processFile = (file: File) => {
    // Validate file type
    const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    if (!validTypes.includes(file.type)) {
        return;
    }

    // Validate file size (5MB max)
    if (file.size > 5 * 1024 * 1024) {
        return;
    }

    // Create preview URL
    if (previewUrl.value) {
        URL.revokeObjectURL(previewUrl.value);
    }
    previewUrl.value = URL.createObjectURL(file);

    // Emit file
    emit('update:modelValue', file);

    // Haptic feedback
    if (navigator.vibrate) {
        navigator.vibrate([10, 50, 10]);
    }
};

const removeFile = () => {
    if (previewUrl.value) {
        URL.revokeObjectURL(previewUrl.value);
        previewUrl.value = null;
    }
    emit('update:modelValue', null);

    // Haptic feedback
    if (navigator.vibrate) {
        navigator.vibrate(10);
    }
};

const removeExisting = () => {
    emit('remove-existing');

    // Haptic feedback
    if (navigator.vibrate) {
        navigator.vibrate(10);
    }
};

const handleDragOver = (event: DragEvent) => {
    event.preventDefault();
    isDragging.value = true;
};

const handleDragLeave = () => {
    isDragging.value = false;
};

const handleDrop = (event: DragEvent) => {
    event.preventDefault();
    isDragging.value = false;

    const file = event.dataTransfer?.files?.[0];
    if (file) {
        processFile(file);
    }
};

const toggleOptions = () => {
    showOptions.value = !showOptions.value;

    // Haptic feedback
    if (navigator.vibrate) {
        navigator.vibrate(10);
    }
};
</script>

<template>
    <div class="space-y-2">
        <!-- Hidden file inputs -->
        <input
            ref="fileInput"
            type="file"
            accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
            class="hidden"
            :disabled="disabled"
            @change="handleFileSelect"
        />
        <input
            ref="cameraInput"
            type="file"
            accept="image/*"
            capture="environment"
            class="hidden"
            :disabled="disabled"
            @change="handleFileSelect"
        />

        <!-- Preview or Upload Area -->
        <div
            v-if="displayUrl"
            class="relative overflow-hidden rounded-2xl border-2 border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900"
        >
            <!-- Image Preview -->
            <div class="relative aspect-[4/3] w-full">
                <img
                    :src="displayUrl"
                    alt="Preview struk"
                    class="h-full w-full object-cover"
                />

                <!-- Overlay with actions -->
                <div class="absolute inset-0 flex items-center justify-center gap-3 bg-black/40 opacity-0 transition-opacity duration-200 hover:opacity-100">
                    <Button
                        v-if="hasNewFile"
                        type="button"
                        variant="secondary"
                        size="icon"
                        class="h-12 w-12 rounded-full bg-white/90 shadow-lg backdrop-blur-sm transition-transform active:scale-95"
                        @click="removeFile"
                    >
                        <RotateCcw class="h-5 w-5 text-gray-700" />
                    </Button>
                    <Button
                        v-else
                        type="button"
                        variant="secondary"
                        size="icon"
                        class="h-12 w-12 rounded-full bg-white/90 shadow-lg backdrop-blur-sm transition-transform active:scale-95"
                        @click="removeExisting"
                    >
                        <X class="h-5 w-5 text-red-600" />
                    </Button>
                </div>

                <!-- Success indicator for new file -->
                <div
                    v-if="hasNewFile"
                    class="absolute right-2 top-2 flex h-8 w-8 items-center justify-center rounded-full bg-green-500 text-white shadow-lg"
                >
                    <Check class="h-4 w-4" />
                </div>
            </div>

            <!-- Replace button -->
            <div class="border-t border-gray-200 p-3 dark:border-gray-700">
                <Button
                    type="button"
                    variant="outline"
                    class="w-full gap-2"
                    :disabled="disabled"
                    @click="toggleOptions"
                >
                    <Camera class="h-4 w-4" />
                    Ganti Foto Struk
                </Button>
            </div>
        </div>

        <!-- Empty Upload Area -->
        <div
            v-else
            class="relative"
            @dragover="handleDragOver"
            @dragleave="handleDragLeave"
            @drop="handleDrop"
        >
            <div
                class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed p-8 transition-all duration-200"
                :class="{
                    'border-blue-500 bg-blue-50 dark:bg-blue-950': isDragging,
                    'border-gray-300 bg-gray-50 hover:border-gray-400 dark:border-gray-700 dark:bg-gray-900': !isDragging && !error,
                    'border-red-500 bg-red-50 dark:bg-red-950': error,
                }"
            >
                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800">
                    <ImagePlus class="h-8 w-8 text-gray-400" />
                </div>
                <p class="mb-1 text-center font-medium text-gray-700 dark:text-gray-300">
                    Tambah Foto Struk
                </p>
                <p class="mb-4 text-center text-sm text-gray-500">
                    Drag & drop atau pilih dari kamera/galeri
                </p>
                <Button
                    type="button"
                    variant="outline"
                    class="gap-2 transition-transform active:scale-95"
                    :disabled="disabled"
                    @click="toggleOptions"
                >
                    <Camera class="h-4 w-4" />
                    Pilih Foto
                </Button>
            </div>
        </div>

        <!-- Options Menu -->
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 scale-95 translate-y-2"
            enter-to-class="opacity-100 scale-100 translate-y-0"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100 scale-100 translate-y-0"
            leave-to-class="opacity-0 scale-95 translate-y-2"
        >
            <div
                v-if="showOptions"
                class="animate-in fade-in slide-in-from-bottom-2 mt-2 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900"
            >
                <button
                    type="button"
                    class="flex w-full items-center gap-3 px-4 py-3 text-left transition-colors hover:bg-gray-50 active:bg-gray-100 dark:hover:bg-gray-800 dark:active:bg-gray-700"
                    @click="openCamera"
                >
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-900">
                        <Camera class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">Ambil Foto</p>
                        <p class="text-sm text-gray-500">Gunakan kamera untuk foto struk</p>
                    </div>
                </button>
                <div class="mx-4 border-t border-gray-100 dark:border-gray-800" />
                <button
                    type="button"
                    class="flex w-full items-center gap-3 px-4 py-3 text-left transition-colors hover:bg-gray-50 active:bg-gray-100 dark:hover:bg-gray-800 dark:active:bg-gray-700"
                    @click="openGallery"
                >
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-purple-100 dark:bg-purple-900">
                        <ImagePlus class="h-5 w-5 text-purple-600 dark:text-purple-400" />
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">Pilih dari Galeri</p>
                        <p class="text-sm text-gray-500">Upload foto yang sudah ada</p>
                    </div>
                </button>
            </div>
        </Transition>

        <!-- Error message -->
        <p v-if="error" class="text-sm text-red-500">
            {{ error }}
        </p>
    </div>
</template>

