<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Spinner } from '@/components/ui/spinner'
import AppLayout from '@/layouts/AppLayout.vue'
import libraryRoutes from '@/routes/library'
import type { BreadcrumbItem } from '@/types'
import { Head, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { toast } from 'vue-sonner'
import { useConfirmDialog } from '@/composables/useConfirmDialog'

const { confirm } = useConfirmDialog()

const props = defineProps<{
  book: {
    id: number
    title: string
    author: string
    subject: string
    description?: string
    grade_level: number
    competency?: string
    type: string
    file_path?: string
  }
}>()

// form (pre-filled)
const form = ref({ ...props.book })

const errors = ref<Record<string, string>>({})

const submit = async () => {
  const ok = await confirm({
    title: 'Save changes?',
    description: 'This will update the book information.',
    confirmText: 'Save',
  })

  if (!ok) return

  errors.value = {}

  router.put(libraryRoutes.update(form.value.id), form.value, {
    onError: (errs) => {
      errors.value = errs
      toast.error('Validation failed')
    },
    onSuccess: () => {
      toast.success('Book updated successfully')
    },
  })
}

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Library', href: libraryRoutes.index().url },
  { title: 'Edit Book', href: libraryRoutes.edit(props.book.id).url },
]
</script>

<template>
  <Head title="Edit Book" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-4 p-6">
      <h2 class="text-center text-xl font-bold">Edit Book</h2>

      <div class="mx-auto grid max-w-lg gap-4">
        <div>
          <Label class="mb-2" for="title">Title</Label>
          <Input v-model="form.title" id="title" />
          <div class="text-sm text-red-600">{{ errors.title }}</div>
        </div>

        <div>
          <Label class="mb-2" for="author">Author</Label>
          <Input v-model="form.author" id="author" />
          <div class="text-sm text-red-600">{{ errors.author }}</div>
        </div>

        <div>
          <Label class="mb-2" for="subject">Book Subject</Label>
          <Input v-model="form.subject" id="subject" />
          <div class="text-sm text-red-600">{{ errors.subject }}</div>
        </div>

        <div>
          <Label class="mb-2" for="description">Description</Label>
          <Input v-model="form.description" id="description" />
          <div class="text-sm text-red-600">{{ errors.description }}</div>
        </div>

        <div>
          <Label class="mb-2" for="grade_level">Grade Level</Label>
          <select
            v-model="form.grade_level"
            id="grade_level"
            class="rounded border px-2 py-1"
          >
            <option v-for="n in 6" :key="n" :value="n + 6">
              Grade {{ n + 6 }}
            </option>
          </select>
          <div class="text-sm text-red-600">{{ errors.grade_level }}</div>
        </div>

        <div>
          <Label class="mb-2" for="competency">Competency</Label>
          <Input v-model="form.competency" id="competency" />
          <div class="text-sm text-red-600">{{ errors.competency }}</div>
        </div>

        <div>
          <Label class="mb-2" for="type">Type</Label>
          <select
            v-model="form.type"
            id="type"
            class="rounded border px-2 py-1"
          >
            <option value="pdf">PDF</option>
            <option value="link">Link</option>
            <option value="physical">Physical</option>
          </select>
          <div class="text-sm text-red-600">{{ errors.type }}</div>
        </div>

        <div>
          <Label class="mb-2" for="file_path">File Path</Label>
          <Input v-model="form.file_path" id="file_path" />
          <div class="text-sm text-red-600">{{ errors.file_path }}</div>
        </div>

        <Button class="mt-4" @click="submit">
          <Spinner v-if="false" />
          Save Changes
        </Button>
      </div>
    </div>
  </AppLayout>
</template>
