<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\QuestionType;
use Illuminate\Database\Seeder;

class StackBinaryTreeQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $testQuestionTypeId = QuestionType::where('title', 'TestQuestion')->value('id') ?? 1;

        $stackQuestions = [
            ['Operasi yang menghapus elemen teratas stack disebut...', ['push', 'pop', 'peek', 'shift'], '1'],
            ['Jika stack diimplementasikan dengan array, posisi elemen terakhir biasanya berada di...', ['index 0', 'index 1', 'top array', 'bottom array'], '2'],
            ['Struktur data stack mengikuti prinsip...', ['FIFO', 'LIFO', 'Random Access', 'Priority'], '1'],
            ['Fungsi peek pada stack digunakan untuk...', ['menambah data', 'menghapus data', 'melihat elemen teratas tanpa menghapus', 'mengurutkan data'], '2'],
            ['Jika terjadi stack overflow, artinya...', ['stack kosong', 'kapasitas stack penuh', 'stack rusak', 'elemen duplikat'], '1'],
            ['Stack paling sering dipakai untuk menangani...', ['rekursi', 'hashing', 'sorting tabel', 'normalisasi'], '0'],
            ['Dalam ekspresi matematika, stack membantu evaluasi...', ['prefix dan postfix', 'CSV', 'SQL', 'XML'], '0'],
            ['Operasi pop pada stack array umumnya mengubah pointer...', ['ke atas', 'ke bawah', 'ke kiri', 'ke kanan'], '1'],
            ['Dalam parsing kurung, stack berguna untuk memeriksa...', ['warna teks', 'pasangan tanda kurung', 'ukuran file', 'jumlah baris'], '1'],
            ['Struktur data yang mendukung backtracking biasanya adalah...', ['queue', 'stack', 'heap', 'graph'], '1'],
            ['Kompleksitas waktu peek pada stack yang benar adalah...', ['O(n)', 'O(log n)', 'O(1)', 'O(n log n)'], '2'],
            ['Jika stack kosong lalu dilakukan pop, kondisi ini disebut...', ['underflow', 'overflow', 'collision', 'merge'], '0'],
            ['Stack call pada program dipakai untuk menyimpan...', ['data file', 'state pemanggilan fungsi', 'cache browser', 'hasil query'], '1'],
            ['Undo pada editor biasanya diimplementasikan dengan...', ['queue', 'stack', 'tree', 'map'], '1'],
            ['Reverse string paling mudah menggunakan...', ['stack', 'graph', 'heap', 'trie'], '0'],
            ['Jika top stack menunjuk ke elemen B setelah push, berarti elemen B...', ['paling bawah', 'paling atas', 'di luar stack', 'dihapus'], '1'],
            ['Stack yang dibuat dengan linked list biasanya menaruh elemen baru di...', ['head', 'tail', 'tengah', 'luar list'], '0'],
            ['Saat mengecek balanced parentheses, karakter pembuka biasanya di...', ['pop', 'push', 'merge', 'split'], '1'],
            ['Stack cocok untuk algoritma DFS karena sifatnya...', ['queue-like', 'LIFO', 'sorted', 'parallel'], '1'],
            ['Jika elemen A di-push lalu B lalu C, hasil pop pertama adalah...', ['A', 'B', 'C', 'tidak tentu'], '2'],
            ['Stack recursion membantu karena menyimpan...', ['hasil akhir', 'state tiap pemanggilan', 'data acak', 'file cache'], '1'],
            ['Elemen teratas stack sering disebut juga...', ['base', 'top', 'root', 'leaf'], '1'],
            ['Saat implementasi stack, operasi yang paling berisiko menyebabkan underflow adalah...', ['push', 'pop', 'peek', 'isEmpty'], '1'],
            ['Stack digunakan pada algoritma konversi infix ke postfix untuk menyimpan...', ['operator', 'operand', 'variabel global', 'node tree'], '0'],
            ['Ketika menghitung postfix, operator diambil dari stack sesuai urutan...', ['FIFO', 'LIFO', 'alphabetical', 'numeric'], '1'],
        ];

        $binaryTreeQuestions = [
            ['Binary tree adalah struktur data dengan maksimal berapa anak per node?', ['1', '2', '3', '4'], '1'],
            ['Traversal Left-Root-Right disebut...', ['preorder', 'inorder', 'postorder', 'level order'], '1'],
            ['Traversal Root-Left-Right disebut...', ['preorder', 'inorder', 'postorder', 'zigzag'], '0'],
            ['Traversal Left-Right-Root disebut...', ['preorder', 'inorder', 'postorder', 'breadth-first'], '2'],
            ['Node paling atas pada binary tree disebut...', ['leaf', 'root', 'branch', 'edge'], '1'],
            ['Node tanpa child disebut...', ['root', 'internal node', 'leaf', 'sibling'], '2'],
            ['Pada binary search tree, nilai di kiri node harus...', ['lebih besar', 'lebih kecil', 'sama', 'acak'], '1'],
            ['Pada binary search tree, nilai di kanan node harus...', ['lebih kecil', 'lebih besar', 'nol', 'acak'], '1'],
            ['Traversal yang mengunjungi per level disebut...', ['inorder', 'preorder', 'level order', 'postorder'], '2'],
            ['Kedalaman sebuah node dihitung dari...', ['jumlah anak', 'jarak dari root', 'jumlah leaf', 'nilai node'], '1'],
            ['Jika semua level penuh kecuali terakhir, itu mendekati...', ['full tree', 'complete binary tree', 'skew tree', 'general tree'], '1'],
            ['Binary tree yang semua child kiri saja disebut...', ['balanced tree', 'left-skewed tree', 'heap', 'forest'], '1'],
            ['Binary tree yang semua child kanan saja disebut...', ['right-skewed tree', 'balanced tree', 'heap', 'forest'], '0'],
            ['Operasi pencarian pada BST rata-rata lebih cepat karena sifat...', ['ordering', 'stack', 'random', 'hashing'], '0'],
            ['Pada inorder traversal BST, hasil node biasanya tampil...', ['acak', 'urut naik', 'urut turun selalu', 'duplikat'], '1'],
            ['Untuk menemukan leaf node, kita memeriksa node yang...', ['punya dua child', 'tidak punya child', 'punya parent', 'punya sibling'], '1'],
            ['Level of a node adalah posisi node berdasarkan...', ['jumlah edge dari root', 'jumlah leaf', 'jumlah subtree', 'jumlah data'], '0'],
            ['Height sebuah tree umumnya berarti...', ['jumlah node total', 'panjang jalur terpanjang dari root ke leaf', 'jumlah child', 'jumlah akar'], '1'],
            ['Binary tree cocok untuk representasi ekspresi karena...', ['mudah diurutkan', 'struktur hierarkis', 'tidak punya root', 'selalu seimbang'], '1'],
            ['Jika tree sangat miring ke satu sisi, performa BST bisa menjadi...', ['lebih cepat', 'mendekati linked list', 'konstan', 'tidak berubah'], '1'],
            ['Dalam traversal BFS pada tree, struktur bantu yang dipakai biasanya...', ['stack', 'queue', 'heap sort', 'set'], '1'],
            ['Preorder traversal sering berguna untuk...', ['clone tree / serialize tree', 'sorting array', 'mencari hash', 'load balancing'], '0'],
            ['Jika root memiliki dua child, child tersebut disebut...', ['siblings', 'leaves', 'parents', 'ancestors'], '0'],
            ['Binary tree paling dasar adalah pohon dengan derajat maksimum...', ['1', '2', '3', '4'], '1'],
            ['Dalam BST, hasil search pada nilai yang tidak ada akan berakhir saat...', ['menemukan leaf/null child', 'tree penuh', 'root hilang', 'queue kosong'], '0'],
        ];

        $items = [];

        foreach ($stackQuestions as $index => $stackQuestion) {
            $items[] = [
                'title' => 'Stack #' . ($index + 26),
                'question_body' => $stackQuestion[0],
                'topic' => 'Stack',
                'difficulty' => 'hard',
                'answer' => [
                    'answers' => $stackQuestion[1],
                    'correctAnswer' => $stackQuestion[2],
                ],
            ];
        }

        foreach ($binaryTreeQuestions as $index => $binaryTreeQuestion) {
            $items[] = [
                'title' => 'Binary Tree #' . ($index + 26),
                'question_body' => $binaryTreeQuestion[0],
                'topic' => 'Binary Tree',
                'difficulty' => 'hard',
                'answer' => [
                    'answers' => $binaryTreeQuestion[1],
                    'correctAnswer' => $binaryTreeQuestion[2],
                ],
            ];
        }

        foreach ($items as $item) {
            Question::updateOrCreate(
                ['title' => $item['title']],
                [
                    'question_body' => $item['question_body'],
                    'question_type_id' => $testQuestionTypeId,
                    'difficulty' => $item['difficulty'],
                    'topic' => $item['topic'],
                    'answer' => json_encode($item['answer']),
                ]
            );
        }
    }
}