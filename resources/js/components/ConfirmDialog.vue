<script setup lang="ts">
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog'
import { Button } from '@/components/ui/button'
import { useConfirmDialog } from '@/composables/useConfirmDialog'

const { isOpen, options, accept, cancel } = useConfirmDialog()
</script>

<template>
  <Dialog :open="isOpen" @update:open="cancel">
    <DialogContent class="sm:max-w-md">
      <DialogHeader>
        <DialogTitle>{{ options.title }}</DialogTitle>
        <DialogDescription>
          {{ options.description }}
        </DialogDescription>
      </DialogHeader>

      <DialogFooter>
        <Button variant="outline" @click="cancel">
          {{ options.cancelText }}
        </Button>
        <Button
            :variant="options.variant"
            @click="accept">
          {{ options.confirmText }}
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
