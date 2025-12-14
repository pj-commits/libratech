<script setup lang="ts">
import { dashboard, login, register } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);
</script>

<template>
  <Head title="Welcome" />

  <div class="flex min-h-screen flex-col bg-[#FDFDFC] dark:bg-[#0a0a0a] p-6">

    <!-- Top nav -->
    <nav class="flex justify-end gap-4 mb-8">
      <template v-if="$page.props.auth.user">
        <Link
          :href="dashboard()"
          class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
        >
          Dashboard
        </Link>
      </template>
      <template v-else>
        <Link
          :href="login()"
          class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]"
        >
          Log in
        </Link>
        <Link
          v-if="canRegister"
          :href="register()"
          class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
        >
          Register
        </Link>
      </template>
    </nav>

    <!-- Centered App Name -->
    <div class="flex flex-col items-center justify-center flex-1 text-center">
      <h1 class="text-5xl font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">
        Libratech
      </h1>
      <p class="text-lg text-[#555] dark:text-[#ccc] mb-8">
        <strong><em>Your digital library.</em></strong>
      </p>
    </div>

  </div>
</template>

