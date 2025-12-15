<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import type { BreadcrumbItem } from '@/types'
import { Head, Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import libraryRoutes from '@/routes/library';

interface Book {
  id: number
  book_code: string
  title: string
  author: string
  subject: string
  description?: string
  grade_level: number
  competency?: string
  type: string
  file_path?: string
}

const props = defineProps<{
  book: Book
}>()

const page = usePage()

// current user role
const role = computed(() => page.props.auth.user.role)

// breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Library', href: libraryRoutes.index().url },
  { title: props.book.title, href: libraryRoutes.show(props.book.id).url },
]
</script>

<template>
  <Head :title="book.title" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="max-w-3xl space-y-6 p-6">

      <!-- Title -->
      <div class="space-y-1">
        <h1 class="text-2xl font-bold">{{ book.title }}</h1>
        <p class="text-muted-foreground">
          by {{ book.author }}
        </p>
      </div>

      <!-- Book Info -->
      <div class="grid grid-cols-2 gap-4 text-sm">
        <div><strong>Book Code:</strong> {{ book.book_code }}</div>
        <div><strong>Grade Level:</strong> {{ book.grade_level }}</div>
        <div><strong>Subject:</strong> {{ book.subject }}</div>
        <div><strong>Type:</strong> {{ book.type }}</div>
        <div v-if="book.competency">
          <strong>Competency:</strong> {{ book.competency }}
        </div>
        <div v-if="book.file_path">
          <strong>File Path:</strong> {{ book.file_path }}
        </div>
      </div>

      <!-- Description -->
      <div v-if="book.description">
        <h3 class="font-semibold">Description</h3>
        <p class="text-sm text-muted-foreground">
          {{ book.description }}
        </p>
      </div>

      <!-- Actions -->
      <div class="flex gap-2 pt-4">

        <!-- Borrow (students & teachers only – logic later) -->
        <Button
          v-if="['student', 'teacher'].includes(role)"
          class="bg-green-600"
        >
          Borrow
        </Button>

        <!-- Librarian actions -->
        <template v-if="role === 'librarian'">
          <Link :href="libraryRoutes.edit(book.id)">
            <Button variant="outline">Edit</Button>
          </Link>

          <Button variant="destructive">
            Delete
          </Button>
        </template>
      </div>

      <!-- Borrow History (placeholder for now) -->
      <div class="pt-6">
        <h3 class="font-semibold">Borrow History</h3>
        <p class="text-sm text-muted-foreground">
          No borrow history yet.
        </p>
      </div>

    </div>
  </AppLayout>
</template>
