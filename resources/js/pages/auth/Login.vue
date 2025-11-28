<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { store } from '@/routes/login';
import { request } from '@/routes/password';
import { Form, Head } from '@inertiajs/vue3';
import { Wallet, Lock, Mail, Sparkles } from 'lucide-vue-next';
import { ref, onMounted } from 'vue';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const isLoaded = ref(false);

onMounted(() => {
    setTimeout(() => {
        isLoaded.value = true;
    }, 100);
});
</script>

<template>
    <Head title="Masuk">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="" />
        <link
            href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
            rel="stylesheet"
        />
    </Head>

    <div
        class="login-container relative flex min-h-svh flex-col items-center justify-center overflow-hidden bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900 p-6"
    >
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 overflow-hidden">
            <!-- Gradient Orbs -->
            <div
                class="animate-float-slow absolute -left-20 -top-20 h-96 w-96 rounded-full bg-gradient-to-br from-blue-500/30 to-cyan-500/20 blur-3xl"
            />
            <div
                class="animate-float-medium absolute -bottom-32 -right-32 h-[500px] w-[500px] rounded-full bg-gradient-to-br from-indigo-500/25 to-purple-500/20 blur-3xl"
            />
            <div
                class="animate-float-fast absolute left-1/3 top-1/4 h-64 w-64 rounded-full bg-gradient-to-br from-cyan-400/20 to-blue-400/15 blur-2xl"
            />
            <!-- Grid Pattern -->
            <div
                class="absolute inset-0 bg-[linear-gradient(rgba(255,255,255,0.02)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.02)_1px,transparent_1px)] bg-[size:64px_64px]"
            />
        </div>

        <!-- Main Card -->
        <div
            class="relative z-10 w-full max-w-md"
            :class="{
                'translate-y-0 scale-100 opacity-100': isLoaded,
                'translate-y-8 scale-95 opacity-0': !isLoaded,
            }"
            style="transition: all 0.8s cubic-bezier(0.34, 1.56, 0.64, 1)"
        >
            <!-- Glass Card -->
            <div
                class="glass-card overflow-hidden rounded-3xl border border-white/10 bg-white/5 p-8 shadow-2xl backdrop-blur-2xl"
            >
                <!-- Logo & Header -->
                <div class="mb-8 text-center">
                    <div
                        class="relative mx-auto mb-4 flex h-20 w-20 items-center justify-center"
                        :class="{
                            'scale-100 opacity-100': isLoaded,
                            'scale-50 opacity-0': !isLoaded,
                        }"
                        style="transition: all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) 0.2s"
                    >
                        <!-- Glow Ring -->
                        <div
                            class="absolute inset-0 animate-pulse rounded-2xl bg-gradient-to-br from-blue-400/40 to-cyan-400/40 blur-xl"
                        />
                        <!-- Icon Container -->
                        <div
                            class="relative flex h-full w-full items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-cyan-500 shadow-lg shadow-blue-500/30"
                        >
                            <Wallet class="h-10 w-10 text-white" />
                        </div>
                        <!-- Sparkle -->
                        <Sparkles
                            class="absolute -right-1 -top-1 h-5 w-5 animate-pulse text-yellow-400"
                        />
                    </div>

                    <h1
                        class="text-2xl font-bold tracking-tight text-white"
                        :class="{
                            'translate-y-0 opacity-100': isLoaded,
                            'translate-y-4 opacity-0': !isLoaded,
                        }"
                        style="transition: all 0.5s ease-out 0.3s"
                    >
                        Petty Cash
                    </h1>
                    <p
                        class="mt-1 text-sm text-blue-200/70"
                        :class="{
                            'translate-y-0 opacity-100': isLoaded,
                            'translate-y-4 opacity-0': !isLoaded,
                        }"
                        style="transition: all 0.5s ease-out 0.4s"
                    >
                        Kelola kas kecil dengan mudah
                    </p>
                </div>

                <!-- Status Message -->
                <div
                    v-if="status"
                    class="mb-6 rounded-xl border border-green-500/20 bg-green-500/10 px-4 py-3 text-center text-sm font-medium text-green-400"
                >
                    {{ status }}
                </div>

                <!-- Login Form -->
                <Form
                    v-bind="store.form()"
                    :reset-on-success="['password']"
                    v-slot="{ errors, processing }"
                    class="flex flex-col gap-5"
                >
                    <!-- Email Field -->
                    <div
                        class="space-y-2"
                        :class="{
                            'translate-y-0 opacity-100': isLoaded,
                            'translate-y-4 opacity-0': !isLoaded,
                        }"
                        style="transition: all 0.5s ease-out 0.5s"
                    >
                        <Label for="email" class="text-sm font-medium text-blue-200/90">
                            Email
                        </Label>
                        <div class="relative">
                            <div
                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4"
                            >
                                <Mail class="h-4 w-4 text-blue-300/50" />
                            </div>
                            <Input
                                id="email"
                                type="email"
                                name="email"
                                required
                                autofocus
                                :tabindex="1"
                                autocomplete="email"
                                placeholder="email@example.com"
                                class="h-12 border-white/10 bg-white/5 pl-11 text-white placeholder:text-blue-200/40 focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20"
                            />
                        </div>
                        <InputError :message="errors.email" class="text-red-400" />
                    </div>

                    <!-- Password Field -->
                    <div
                        class="space-y-2"
                        :class="{
                            'translate-y-0 opacity-100': isLoaded,
                            'translate-y-4 opacity-0': !isLoaded,
                        }"
                        style="transition: all 0.5s ease-out 0.6s"
                    >
                        <div class="flex items-center justify-between">
                            <Label for="password" class="text-sm font-medium text-blue-200/90">
                                Password
                            </Label>
                            <TextLink
                                v-if="canResetPassword"
                                :href="request()"
                                class="text-xs text-blue-400 transition-colors hover:text-blue-300"
                                :tabindex="5"
                            >
                                Lupa password?
                            </TextLink>
                        </div>
                        <div class="relative">
                            <div
                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4"
                            >
                                <Lock class="h-4 w-4 text-blue-300/50" />
                            </div>
                            <Input
                                id="password"
                                type="password"
                                name="password"
                                required
                                :tabindex="2"
                                autocomplete="current-password"
                                placeholder="••••••••"
                                class="h-12 border-white/10 bg-white/5 pl-11 text-white placeholder:text-blue-200/40 focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20"
                            />
                        </div>
                        <InputError :message="errors.password" class="text-red-400" />
                    </div>

                    <!-- Remember Me -->
                    <div
                        class="flex items-center"
                        :class="{
                            'translate-y-0 opacity-100': isLoaded,
                            'translate-y-4 opacity-0': !isLoaded,
                        }"
                        style="transition: all 0.5s ease-out 0.7s"
                    >
                        <Label
                            for="remember"
                            class="flex cursor-pointer items-center space-x-3 text-blue-200/80"
                        >
                            <Checkbox
                                id="remember"
                                name="remember"
                                :tabindex="3"
                                class="border-white/20 data-[state=checked]:bg-blue-500 data-[state=checked]:text-white"
                            />
                            <span class="text-sm">Ingat saya</span>
                        </Label>
                    </div>

                    <!-- Submit Button -->
                    <Button
                        type="submit"
                        :tabindex="4"
                        :disabled="processing"
                        data-test="login-button"
                        class="btn-primary relative mt-2 h-12 w-full overflow-hidden rounded-xl text-base font-semibold shadow-lg transition-all duration-300 hover:scale-[0.98] active:scale-[0.96]"
                        :class="{
                            'translate-y-0 opacity-100': isLoaded,
                            'translate-y-4 opacity-0': !isLoaded,
                        }"
                        style="transition: all 0.5s ease-out 0.8s, transform 0.2s ease"
                    >
                        <span
                            class="absolute inset-0 bg-gradient-to-r from-blue-500 via-blue-600 to-cyan-500"
                        />
                        <span
                            class="absolute inset-0 bg-gradient-to-r from-blue-600 via-cyan-500 to-blue-500 opacity-0 transition-opacity duration-300 hover:opacity-100"
                        />
                        <span class="relative flex items-center justify-center gap-2">
                            <Spinner v-if="processing" class="text-white" />
                            <span>{{ processing ? 'Memproses...' : 'Masuk' }}</span>
                        </span>
                    </Button>
                </Form>
            </div>

            <!-- Footer -->
            <p
                class="mt-6 text-center text-xs text-blue-200/40"
                :class="{
                    'translate-y-0 opacity-100': isLoaded,
                    'translate-y-4 opacity-0': !isLoaded,
                }"
                style="transition: all 0.5s ease-out 0.9s"
            >
                &copy; {{ new Date().getFullYear() }} Petty Cash App
                <span class="mx-2">•</span>
                Dibuat oleh Zulfikar Hidayatullah
            </p>
        </div>
    </div>
</template>

<style scoped>
.login-container {
    font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
}

.glass-card {
    box-shadow:
        0 0 0 1px rgba(255, 255, 255, 0.05),
        0 25px 50px -12px rgba(0, 0, 0, 0.5),
        0 0 100px -20px rgba(59, 130, 246, 0.2);
}

.btn-primary:hover {
    box-shadow:
        0 10px 40px -10px rgba(59, 130, 246, 0.5),
        0 0 20px -5px rgba(6, 182, 212, 0.3);
}

@keyframes float-slow {
    0%,
    100% {
        transform: translate(0, 0) rotate(0deg);
    }
    33% {
        transform: translate(30px, -30px) rotate(5deg);
    }
    66% {
        transform: translate(-20px, 20px) rotate(-5deg);
    }
}

@keyframes float-medium {
    0%,
    100% {
        transform: translate(0, 0) rotate(0deg);
    }
    33% {
        transform: translate(-40px, 20px) rotate(-5deg);
    }
    66% {
        transform: translate(30px, -40px) rotate(5deg);
    }
}

@keyframes float-fast {
    0%,
    100% {
        transform: translate(0, 0) scale(1);
    }
    50% {
        transform: translate(20px, -20px) scale(1.1);
    }
}

.animate-float-slow {
    animation: float-slow 20s ease-in-out infinite;
}

.animate-float-medium {
    animation: float-medium 15s ease-in-out infinite;
}

.animate-float-fast {
    animation: float-fast 10s ease-in-out infinite;
}

/* iOS-like input focus */
:deep(input:focus) {
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
}
</style>
