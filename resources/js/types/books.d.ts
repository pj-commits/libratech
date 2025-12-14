export interface Book {
    id: number;
    book_code: string;
    title: string;
    author?: string;
    subject: string,
    description?: string;
    grade_level: number;
    competency: string;
    type: 'pdf' | 'link' | 'physical';
    file_path?: string;
}
