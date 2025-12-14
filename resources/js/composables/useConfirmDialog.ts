import { ref } from 'vue'

type ConfirmOptions = {
  title?: string
  description?: string
  confirmText?: string
  cancelText?: string
  variant?: 'default' | 'destructive'
}

const isOpen = ref(false)
const options = ref<ConfirmOptions>({})
let resolver: ((value: boolean) => void) | null = null

export function useConfirmDialog() {
  const confirm = (opts: ConfirmOptions) => {
    options.value = {
      title: 'Are you sure?',
      description: 'This action cannot be undone.',
      confirmText: 'Confirm',
      cancelText: 'Cancel',
      variant: 'default',
      ...opts,
    }

    isOpen.value = true

    return new Promise<boolean>((resolve) => {
      resolver = resolve
    })
  }

  const close = () => {
    isOpen.value = false
  }

  const accept = () => {
    resolver?.(true)
    close()
  }

  const cancel = () => {
    resolver?.(false)
    close()
  }

  return {
    isOpen,
    options,
    confirm,
    accept,
    cancel,
  }
}
