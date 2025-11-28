<script setup lang="ts">
import { ref, watch, onMounted, onUnmounted } from 'vue';
import { X, ZoomIn, ZoomOut, RotateCw, Download } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

interface Props {
    open: boolean;
    imageUrl: string | null;
    title?: string;
}

const props = withDefaults(defineProps<Props>(), {
    title: 'Bukti Transaksi',
});

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
}>();

const scale = ref(1);
const rotation = ref(0);
const translateX = ref(0);
const translateY = ref(0);
const isDragging = ref(false);
const startX = ref(0);
const startY = ref(0);

const MIN_SCALE = 0.5;
const MAX_SCALE = 3;

// Reset transform when dialog opens
watch(() => props.open, (isOpen) => {
    if (isOpen) {
        resetTransform();
    }
});

const resetTransform = () => {
    scale.value = 1;
    rotation.value = 0;
    translateX.value = 0;
    translateY.value = 0;
};

const zoomIn = () => {
    if (scale.value < MAX_SCALE) {
        scale.value = Math.min(scale.value + 0.25, MAX_SCALE);
        hapticFeedback();
    }
};

const zoomOut = () => {
    if (scale.value > MIN_SCALE) {
        scale.value = Math.max(scale.value - 0.25, MIN_SCALE);
        hapticFeedback();
    }
};

const rotate = () => {
    rotation.value = (rotation.value + 90) % 360;
    hapticFeedback();
};

const download = async () => {
    if (!props.imageUrl) return;

    try {
        const response = await fetch(props.imageUrl);
        const blob = await response.blob();
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `struk-${Date.now()}.jpg`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
        hapticFeedback();
    } catch (error) {
        console.error('Failed to download image:', error);
    }
};

const handleClose = () => {
    emit('update:open', false);
    hapticFeedback();
};

// Touch/Mouse handlers for panning
const handlePointerDown = (event: PointerEvent) => {
    if (scale.value <= 1) return;

    isDragging.value = true;
    startX.value = event.clientX - translateX.value;
    startY.value = event.clientY - translateY.value;
    (event.target as HTMLElement).setPointerCapture(event.pointerId);
};

const handlePointerMove = (event: PointerEvent) => {
    if (!isDragging.value) return;

    translateX.value = event.clientX - startX.value;
    translateY.value = event.clientY - startY.value;
};

const handlePointerUp = (event: PointerEvent) => {
    isDragging.value = false;
    (event.target as HTMLElement).releasePointerCapture(event.pointerId);
};

// Double tap to zoom
let lastTap = 0;
const handleDoubleTap = () => {
    const now = Date.now();
    if (now - lastTap < 300) {
        if (scale.value === 1) {
            scale.value = 2;
        } else {
            resetTransform();
        }
        hapticFeedback();
    }
    lastTap = now;
};

// Wheel zoom
const handleWheel = (event: WheelEvent) => {
    event.preventDefault();
    const delta = event.deltaY > 0 ? -0.1 : 0.1;
    const newScale = Math.max(MIN_SCALE, Math.min(MAX_SCALE, scale.value + delta));
    scale.value = newScale;
};

const hapticFeedback = () => {
    if (navigator.vibrate) {
        navigator.vibrate(10);
    }
};

// Keyboard shortcuts
const handleKeydown = (event: KeyboardEvent) => {
    if (!props.open) return;

    switch (event.key) {
        case 'Escape':
            handleClose();
            break;
        case '+':
        case '=':
            zoomIn();
            break;
        case '-':
            zoomOut();
            break;
        case 'r':
        case 'R':
            rotate();
            break;
        case '0':
            resetTransform();
            break;
    }
};

onMounted(() => {
    window.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeydown);
});
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent
            class="h-[90vh] max-w-4xl overflow-hidden p-0 sm:h-[85vh]"
        >
            <DialogHeader class="absolute left-0 right-0 top-0 z-10 flex flex-row items-center justify-between border-b border-gray-200/50 bg-white/80 px-4 py-3 backdrop-blur-md dark:border-gray-700/50 dark:bg-gray-900/80">
                <DialogTitle class="text-lg font-semibold">
                    {{ title }}
                </DialogTitle>
                <Button
                    variant="ghost"
                    size="icon"
                    class="h-8 w-8 rounded-full transition-transform active:scale-95"
                    @click="handleClose"
                >
                    <X class="h-5 w-5" />
                </Button>
            </DialogHeader>

            <!-- Image Container -->
            <div
                class="flex h-full items-center justify-center overflow-hidden bg-gray-950 pt-14 pb-20"
                @wheel="handleWheel"
            >
                <img
                    v-if="imageUrl"
                    :src="imageUrl"
                    alt="Receipt preview"
                    class="max-h-full max-w-full cursor-grab object-contain transition-transform duration-200"
                    :class="{ 'cursor-grabbing': isDragging }"
                    :style="{
                        transform: `translate(${translateX}px, ${translateY}px) scale(${scale}) rotate(${rotation}deg)`,
                    }"
                    draggable="false"
                    @pointerdown="handlePointerDown"
                    @pointermove="handlePointerMove"
                    @pointerup="handlePointerUp"
                    @click="handleDoubleTap"
                />
                <div v-else class="text-gray-500">
                    Gambar tidak tersedia
                </div>
            </div>

            <!-- Controls -->
            <div class="absolute inset-x-0 bottom-0 flex items-center justify-center gap-2 border-t border-gray-200/50 bg-white/80 px-4 py-3 backdrop-blur-md dark:border-gray-700/50 dark:bg-gray-900/80">
                <Button
                    variant="outline"
                    size="icon"
                    class="h-10 w-10 rounded-full transition-transform active:scale-95"
                    :disabled="scale <= MIN_SCALE"
                    @click="zoomOut"
                >
                    <ZoomOut class="h-4 w-4" />
                </Button>

                <div class="flex h-10 min-w-[60px] items-center justify-center rounded-full bg-gray-100 px-3 text-sm font-medium dark:bg-gray-800">
                    {{ Math.round(scale * 100) }}%
                </div>

                <Button
                    variant="outline"
                    size="icon"
                    class="h-10 w-10 rounded-full transition-transform active:scale-95"
                    :disabled="scale >= MAX_SCALE"
                    @click="zoomIn"
                >
                    <ZoomIn class="h-4 w-4" />
                </Button>

                <div class="mx-2 h-6 w-px bg-gray-200 dark:bg-gray-700" />

                <Button
                    variant="outline"
                    size="icon"
                    class="h-10 w-10 rounded-full transition-transform active:scale-95"
                    @click="rotate"
                >
                    <RotateCw class="h-4 w-4" />
                </Button>

                <Button
                    variant="outline"
                    size="icon"
                    class="h-10 w-10 rounded-full transition-transform active:scale-95"
                    @click="download"
                >
                    <Download class="h-4 w-4" />
                </Button>
            </div>
        </DialogContent>
    </Dialog>
</template>

