<script setup lang="ts">
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import libraryRoutes from '@/routes/library';
import type { PagePropsWithFlash } from '@/types';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Bell, ArrowUp, ArrowDown } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import {
    Pagination,
    PaginationContent,
    PaginationItem,
    PaginationNext,
    PaginationPrevious,
} from '@/components/ui/pagination';
import { toast } from 'vue-sonner';
import { useConfirmDialog } from '@/composables/useConfirmDialog';

const { confirm } = useConfirmDialog();

interface Book {
    id: number;
    book_code: string;
    title: string;
    author?: string;
    grade_level: number;
    type: string;
    description?: string;
    file_path?: string;
}

const props = defineProps<{
    books: Book[];
    auth: { user: { role: string } };
}>();

// Page info
const page = usePage<PagePropsWithFlash>();
const flashMessage = page.props.flash;

// Breadcrumb
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Library', href: libraryRoutes.index().url },
];

// Filters
const searchQuery = ref('');
const filterGrade = ref<number | ''>('');

// Pagination
const currentPage = ref(1);
const itemsPerPage = 10;

// 🔹 SORT STATE (ADDED)
type SortKey = 'book_code' | 'title' | 'author' | 'grade_level' | 'type';
const sortKey = ref<SortKey>('title');
const sortDirection = ref<'asc' | 'desc'>('asc');

const sortBy = (key: SortKey) => {
    if (sortKey.value === key) {
        sortDirection.value =
            sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortKey.value = key;
        sortDirection.value = 'asc';
    }
};

// Computed: filtered + sorted books
const filteredBooks = computed(() => {
    const books = props.books.filter((book) => {
        const matchesSearch =
            !searchQuery.value ||
            book.title
                .toLowerCase()
                .includes(searchQuery.value.toLowerCase()) ||
            (book.author &&
                book.author
                    .toLowerCase()
                    .includes(searchQuery.value.toLowerCase()));

        const matchesGrade =
            !filterGrade.value || book.grade_level === filterGrade.value;

        return matchesSearch && matchesGrade;
    });

    return books.sort((a, b) => {
        const aVal = a[sortKey.value] ?? '';
        const bVal = b[sortKey.value] ?? '';

        if (aVal < bVal) return sortDirection.value === 'asc' ? -1 : 1;
        if (aVal > bVal) return sortDirection.value === 'asc' ? 1 : -1;
        return 0;
    });
});

// Paginated books
const paginatedBooks = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    return filteredBooks.value.slice(start, start + itemsPerPage);
});

// Delete book
const handleDelete = async (id: number) => {
    const ok = await confirm({
        title: 'Delete book?',
        description: 'This will permanently remove the book.',
        confirmText: 'Delete',
        variant: 'destructive',
    });

    if (!ok) return;

    router.delete(libraryRoutes.destroy(id), {
        onSuccess: () => toast.success('Book deleted'),
    });
};

const columnSizes = {
  book_code: 'w-[140px] min-w-[140px]',
  title: 'w-[260px] min-w-[260px]',
  author: 'w-[220px] min-w-[220px]',
  grade_level: 'w-[120px] min-w-[120px]',
  type: 'w-[120px] min-w-[120px]',
  action: 'w-[180px] min-w-[180px]',
} as const
</script>

<template>
    <Head title="Library" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-4 p-6">
            <!-- Flash message -->
            <div v-if="flashMessage?.message">
                <Alert class="bg-blue-200">
                    <Bell class="h-4 w-4" />
                    <AlertTitle>Notification</AlertTitle>
                    <AlertDescription>
                        {{ flashMessage.message }}
                    </AlertDescription>
                </Alert>
            </div>

            <!-- Librarian Add Book -->
            <div class="mb-4 flex items-center justify-between">
                <div class="flex gap-2">
                    <input
                        type="text"
                        placeholder="Search title/author..."
                        v-model="searchQuery"
                        class="w-64 rounded border px-2 py-1"
                    />
                    <select
                        v-model="filterGrade"
                        class="rounded border px-2 py-1"
                    >
                        <option value="">All Grades</option>
                        <option v-for="n in 6" :key="n" :value="n + 6">
                            Grade {{ n + 6 }}
                        </option>
                    </select>
                </div>

                <div v-if="props.auth.user.role === 'librarian'">
                    <Link :href="libraryRoutes.create()">
                        <Button>Add Book</Button>
                    </Link>
                </div>
            </div>

            <!-- Books Table -->
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead
                            :class="['cursor-pointer', columnSizes.book_code]"
                            @click="sortBy('book_code')"
                        >
                            <div class="flex items-center gap-1">
                                Book Code
                                <ArrowUp
                                    v-if="sortKey === 'book_code' && sortDirection === 'asc'"
                                    class="h-4 w-4"
                                />
                                <ArrowDown
                                    v-if="sortKey === 'book_code' && sortDirection === 'desc'"
                                    class="h-4 w-4"
                                />
                            </div>
                        </TableHead>

                        <TableHead
                            :class="['cursor-pointer', columnSizes.title]"
                            @click="sortBy('title')"
                        >
                            <div class="flex items-center gap-1">
                                Title
                                <ArrowUp
                                    v-if="sortKey === 'title' && sortDirection === 'asc'"
                                    class="h-4 w-4"
                                />
                                <ArrowDown
                                    v-if="sortKey === 'title' && sortDirection === 'desc'"
                                    class="h-4 w-4"
                                />
                            </div>
                        </TableHead>

                        <TableHead
                            :class="['cursor-pointer', columnSizes.author]"
                            @click="sortBy('author')"
                        >
                            <div class="flex items-center gap-1">
                                Author
                                <ArrowUp
                                    v-if="sortKey === 'author' && sortDirection === 'asc'"
                                    class="h-4 w-4"
                                />
                                <ArrowDown
                                    v-if="sortKey === 'author' && sortDirection === 'desc'"
                                    class="h-4 w-4"
                                />
                            </div>
                        </TableHead>

                        <TableHead
                            :class="['cursor-pointer', columnSizes.grade_level]"
                            @click="sortBy('grade_level')"
                        >
                            <div class="flex items-center gap-1">
                                Grade Level
                                <ArrowUp
                                    v-if="sortKey === 'grade_level' && sortDirection === 'asc'"
                                    class="h-4 w-4"
                                />
                                <ArrowDown
                                    v-if="sortKey === 'grade_level' && sortDirection === 'desc'"
                                    class="h-4 w-4"
                                />
                            </div>
                        </TableHead>

                        <TableHead
                            :class="['cursor-pointer', columnSizes.type]"
                            @click="sortBy('type')"
                        >
                            <div class="flex items-center gap-1">
                                Type
                                <ArrowUp
                                    v-if="sortKey === 'type' && sortDirection === 'asc'"
                                    class="h-4 w-4"
                                />
                                <ArrowDown
                                    v-if="sortKey === 'type' && sortDirection === 'desc'"
                                    class="h-4 w-4"
                                />
                            </div>
                        </TableHead>

                        <TableHead
                            :class="['text-center', columnSizes.type]"
                        >Action</TableHead>
                    </TableRow>
                </TableHeader>

                <TableBody>
                    <TableRow v-for="book in paginatedBooks" :key="book.id">
                        <TableCell>{{ book.book_code }}</TableCell>
                        <TableCell>{{ book.title }}</TableCell>
                        <TableCell>{{ book.author }}</TableCell>
                        <TableCell>{{ book.grade_level }}</TableCell>
                        <TableCell>{{ book.type }}</TableCell>
                        <TableCell class="text-center space-x-2">
                            <div
                                v-if="props.auth.user.role === 'librarian'"
                                class="inline-flex gap-2"
                            >
                                <Link :href="libraryRoutes.edit(book.id)">
                                    <Button class="bg-slate-600">Edit</Button>
                                </Link>
                                <Button
                                    class="bg-red-600"
                                    @click="handleDelete(book.id)"
                                >
                                    Delete
                                </Button>
                            </div>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>

            <!-- Pagination -->
            <Pagination
                v-model:page="currentPage"
                :items-per-page="itemsPerPage"
                :total="filteredBooks.length"
            >
                <PaginationContent v-slot="{ items }">
                    <PaginationPrevious />
                    <template v-for="(item, index) in items" :key="index">
                        <PaginationItem
                            v-if="item.type === 'page'"
                            :value="item.value"
                            :is-active="item.value === currentPage"
                        >
                            {{ item.value }}
                        </PaginationItem>
                    </template>
                    <PaginationNext />
                </PaginationContent>
            </Pagination>
        </div>
    </AppLayout>
</template>
