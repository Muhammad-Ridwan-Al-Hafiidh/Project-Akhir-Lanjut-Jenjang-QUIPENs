<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Database\Seeder;

class QuestionSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            // 1-10
            [
                'title' => 'Question #1',
                'question_body' => 'Apa singkatan dari CPU?',
                'question_type_id' => '1',
                'difficulty' => 'easy',
                'answer' => json_encode([
                    'answers' => [
                        'Central Processing Unit',
                        'Computer Primary Unit',
                        'Control Processing Unit',
                        'Central Program Unit'
                    ],
                    'correctAnswer' => '0'
                ])
            ],
            [
                'title' => 'Question #2',
                'question_body' => 'Dalam bilangan biner, 1010 adalah desimal...',
                'question_type_id' => '1',
                'difficulty' => 'easy',
                'answer' => json_encode([
                    'answers' => ['10','12','8','11'],
                    'correctAnswer' => '0'
                ])
            ],
            [
                'title' => 'Question #3',
                'question_body' => 'Manakah dari berikut ini adalah sistem operasi open-source?',
                'question_type_id' => '1',
                'difficulty' => 'easy',
                'answer' => json_encode([
                    'answers' => ['Windows','macOS','Linux','iOS'],
                    'correctAnswer' => '2'
                ])
            ],
            [
                'title' => 'Question #4',
                'question_body' => 'Apa fungsi utama dari RAM?',
                'question_type_id' => '1',
                'difficulty' => 'easy',
                'answer' => json_encode([
                    'answers' => ['Penyimpanan permanen','Menyimpan data sementara untuk akses cepat','Mengatur alamat IP','Mengompilasi kode'],
                    'correctAnswer' => '1'
                ])
            ],
            [
                'title' => 'Question #5',
                'question_body' => 'Protokol yang digunakan untuk mengirimkan halaman web adalah...',
                'question_type_id' => '1',
                'difficulty' => 'easy',
                'answer' => json_encode([
                    'answers' => ['FTP','SMTP','HTTP','SSH'],
                    'correctAnswer' => '2'
                ])
            ],
            [
                'title' => 'Question #6',
                'question_body' => 'HTML adalah singkatan dari...',
                'question_type_id' => '1',
                'difficulty' => 'easy',
                'answer' => json_encode([
                    'answers' => ['HyperText Markup Language','HighText Machine Language','Hyperlink Text Markup Language','Hyperlink Transfer Markup Language'],
                    'correctAnswer' => '0'
                ])
            ],
            [
                'title' => 'Question #7',
                'question_body' => 'Manakah struktur data yang bekerja dengan prinsip LIFO?',
                'question_type_id' => '1',
                'difficulty' => 'easy',
                'answer' => json_encode([
                    'answers' => ['Queue','Stack','Tree','Graph'],
                    'correctAnswer' => '1'
                ])
            ],
            [
                'title' => 'Question #8',
                'question_body' => 'SQL digunakan untuk ...',
                'question_type_id' => '1',
                'difficulty' => 'easy',
                'answer' => json_encode([
                    'answers' => ['Pemrograman aplikasi desktop','Manajemen basis data','Desain antarmuka','Pengolahan citra'],
                    'correctAnswer' => '1'
                ])
            ],
            [
                'title' => 'Question #9',
                'question_body' => 'Manakah dari berikut bukan bahasa pemrograman?',
                'question_type_id' => '1',
                'difficulty' => 'easy',
                'answer' => json_encode([
                    'answers' => ['Python','Java','HTML','C++'],
                    'correctAnswer' => '2'
                ])
            ],
            [
                'title' => 'Question #10',
                'question_body' => 'Dalam HTTP, kode status 404 berarti...',
                'question_type_id' => '1',
                'difficulty' => 'easy',
                'answer' => json_encode([
                    'answers' => ['OK','Created','Not Found','Server Error'],
                    'correctAnswer' => '2'
                ])
            ],

            // 11-20
            [
                'title' => 'Question #11',
                'question_body' => 'Algoritma pengurutan O(n log n) pada kasus rata-rata adalah...',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['Bubble Sort','Insertion Sort','Quick Sort','Selection Sort'],
                    'correctAnswer' => '2'
                ])
            ],
            [
                'title' => 'Question #12',
                'question_body' => 'Struktur data yang sering digunakan untuk implementasi rekursi adalah...',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['Tree','Heap','Stack','Queue'],
                    'correctAnswer' => '2'
                ])
            ],
            [
                'title' => 'Question #13',
                'question_body' => 'Manakah protokol yang bersifat connectionless?',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['TCP','UDP','HTTP','TLS'],
                    'correctAnswer' => '1'
                ])
            ],
            [
                'title' => 'Question #14',
                'question_body' => 'Relasi antara tabel yang menggunakan primary key dari tabel lain adalah...',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['Index','View','Foreign Key','Trigger'],
                    'correctAnswer' => '2'
                ])
            ],
            [
                'title' => 'Question #15',
                'question_body' => 'Manakah konsep OOP yang memungkinkan objek berbeda bereaksi berbeda terhadap pesan yang sama?',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['Encapsulation','Inheritance','Polymorphism','Abstraction'],
                    'correctAnswer' => '2'
                ])
            ],
            [
                'title' => 'Question #16',
                'question_body' => 'Hash table menggunakan apa untuk menemukan data dengan cepat?',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['Hash function','Binary search','Linear scan','Tree traversal'],
                    'correctAnswer' => '0'
                ])
            ],
            [
                'title' => 'Question #17',
                'question_body' => 'Manakah sifat ACID yang menjamin transaksi sepenuhnya atau tidak sama sekali?',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['Atomicity','Consistency','Isolation','Durability'],
                    'correctAnswer' => '0'
                ])
            ],
            [
                'title' => 'Question #18',
                'question_body' => 'Transformer model sering digunakan terutama dalam domain...',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['Jaringan','Sistem Operasi','Pemrosesan Bahasa Alami','Basis Data'],
                    'correctAnswer' => '2'
                ])
            ],
            [
                'title' => 'Question #19',
                'question_body' => 'Dalam analisis algoritma, notasi Big-O menggambarkan...',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['Kinerja terbaik','Pertumbuhan waktu/jumlah operasi terhadap input','Penggunaan memori absolut','Jumlah bug'],
                    'correctAnswer' => '1'
                ])
            ],
            [
                'title' => 'Question #20',
                'question_body' => 'Manakah format data yang ringkas untuk pertukaran data dan mudah dibaca mesin?',
                'question_type_id' => '1',
                'difficulty' => 'easy',
                'answer' => json_encode([
                    'answers' => ['Binary','XML','JSON','YAML'],
                    'correctAnswer' => '2'
                ])
            ],

            // 21-30
            [
                'title' => 'Question #21',
                'question_body' => 'Manakah algoritma pencarian yang bekerja pada array terurut?',
                'question_type_id' => '1',
                'difficulty' => 'easy',
                'answer' => json_encode([
                    'answers' => ['Linear search','Binary search','Hash search','DFS'],
                    'correctAnswer' => '1'
                ])
            ],
            [
                'title' => 'Question #22',
                'question_body' => 'Manakah metode penyimpanan basis data NoSQL?',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['Relational','Document','Table','Spreadsheet'],
                    'correctAnswer' => '1'
                ])
            ],
            [
                'title' => 'Question #23',
                'question_body' => 'IPv4 memiliki berapa bit pada alamatnya?',
                'question_type_id' => '1',
                'difficulty' => 'easy',
                'answer' => json_encode([
                    'answers' => ['32 bit','64 bit','128 bit','16 bit'],
                    'correctAnswer' => '0'
                ])
            ],
            [
                'title' => 'Question #24',
                'question_body' => 'Manakah yang bukan termasuk model OSI layer?',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['Physical','Presentation','Transport','Execution'],
                    'correctAnswer' => '3'
                ])
            ],
            [
                'title' => 'Question #25',
                'question_body' => 'Teknik enkripsi asimetris menggunakan...',
                'question_type_id' => '1',
                'difficulty' => 'hard',
                'answer' => json_encode([
                    'answers' => ['Satu kunci untuk enkripsi dan dekripsi','Kombinasi hashing','Kunci publik dan kunci privat','Tidak menggunakan kunci'],
                    'correctAnswer' => '2'
                ])
            ],
            [
                'title' => 'Question #26',
                'question_body' => 'Manakah operasi database yang termasuk CRUD?',
                'question_type_id' => '1',
                'difficulty' => 'easy',
                'answer' => json_encode([
                    'answers' => ['Create, Read, Update, Delete','Compile, Run, Update, Delete','Connect, Read, Upload, Download','Cache, Retrieve, Update, Drop'],
                    'correctAnswer' => '0'
                ])
            ],
            [
                'title' => 'Question #27',
                'question_body' => 'Manakah yang termasuk teknik pencegahan deadlock?',
                'question_type_id' => '1',
                'difficulty' => 'hard',
                'answer' => json_encode([
                    'answers' => ['Banker algorithm','Race condition','Priority inversion','Busy waiting'],
                    'correctAnswer' => '0'
                ])
            ],
            [
                'title' => 'Question #28',
                'question_body' => 'Cache CPU berfungsi untuk ...',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['Menyimpan data permanen','Meningkatkan kecepatan akses data yang sering digunakan','Menurunkan frekuensi CPU','Menangani I/O perangkat'],
                    'correctAnswer' => '1'
                ])
            ],
            [
                'title' => 'Question #29',
                'question_body' => 'Model MVC memisahkan aplikasi menjadi Model, View, dan ...',
                'question_type_id' => '1',
                'difficulty' => 'easy',
                'answer' => json_encode([
                    'answers' => ['Controller','Cache','Client','Container'],
                    'correctAnswer' => '0'
                ])
            ],
            [
                'title' => 'Question #30',
                'question_body' => 'SSL/TLS utama digunakan untuk ...',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['Mengompresi data','Enkripsi komunikasi di jaringan','Mengganti IP address','Mempercepat koneksi'],
                    'correctAnswer' => '1'
                ])
            ],

            // 31-40
            [
                'title' => 'Question #31',
                'question_body' => 'Manakah perintah git untuk membuat branch baru?',
                'question_type_id' => '1',
                'difficulty' => 'easy',
                'answer' => json_encode([
                    'answers' => ['git clone','git branch <name>','git merge','git init'],
                    'correctAnswer' => '1'
                ])
            ],
            [
                'title' => 'Question #32',
                'question_body' => 'Apa tujuan normalisasi dalam basis data?',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['Meningkatkan redundancy','Menurunkan integritas','Mengurangi redundansi dan anomali','Menghapus index'],
                    'correctAnswer' => '2'
                ])
            ],
            [
                'title' => 'Question #33',
                'question_body' => 'Manakah ukuran kompleksitas waktu untuk algoritma linear?',
                'question_type_id' => '1',
                'difficulty' => 'easy',
                'answer' => json_encode([
                    'answers' => ['O(1)','O(n)','O(log n)','O(n^2)'],
                    'correctAnswer' => '1'
                ])
            ],
            [
                'title' => 'Question #34',
                'question_body' => 'Apa itu JSON Web Token (JWT)?',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['Protokol jaringan','Format token terstruktur untuk autentikasi','Bahasa pemrograman','Server basis data'],
                    'correctAnswer' => '1'
                ])
            ],
            [
                'title' => 'Question #35',
                'question_body' => 'Manakah yang bukan metode HTTP idempotent?',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['GET','PUT','DELETE','POST'],
                    'correctAnswer' => '3'
                ])
            ],
            [
                'title' => 'Question #36',
                'question_body' => 'Dalam arsitektur REST, apa itu resource?',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['Endpoint jaringan','Representasi entitas yang dapat diakses lewat URI','Database server','Jenis HTTP header'],
                    'correctAnswer' => '1'
                ])
            ],
            [
                'title' => 'Question #37',
                'question_body' => 'Buffer overflow adalah contoh dari ...',
                'question_type_id' => '1',
                'difficulty' => 'hard',
                'answer' => json_encode([
                    'answers' => ['Vulnerability keamanan','Metode optimasi','Teknik kompresi','Sistem file'],
                    'correctAnswer' => '0'
                ])
            ],
            [
                'title' => 'Question #38',
                'question_body' => 'Manakah algoritma hashing yang sering digunakan untuk integritas file (bukan untuk password)?',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['MD5','bcrypt','PBKDF2','argon2'],
                    'correctAnswer' => '0'
                ])
            ],
            [
                'title' => 'Question #39',
                'question_body' => 'Manakah yang menggambarkan konsep virtual machine?',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['Container ringan','Lingkungan terisolasi yang meniru hardware','Jenis compiler','Protokol jaringan'],
                    'correctAnswer' => '1'
                ])
            ],
            [
                'title' => 'Question #40',
                'question_body' => 'Manakah tingkat RAID yang menawarkan redundansi dengan striping?',
                'question_type_id' => '1',
                'difficulty' => 'hard',
                'answer' => json_encode([
                    'answers' => ['RAID 0','RAID 1','RAID 5','RAID 10'],
                    'correctAnswer' => '2'
                ])
            ],

            // 41-50
            [
                'title' => 'Question #41',
                'question_body' => 'Apa kepanjangan dari DNS?',
                'question_type_id' => '1',
                'difficulty' => 'easy',
                'answer' => json_encode([
                    'answers' => ['Domain Name System','Digital Number Server','Data Network Service','Direct Name Service'],
                    'correctAnswer' => '0'
                ])
            ],
            [
                'title' => 'Question #42',
                'question_body' => 'XSS adalah singkatan dari...',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['Cross-Site Scripting','Cross-Site Sharing','XML Secure Service','External Script'],
                    'correctAnswer' => '0'
                ])
            ],
            [
                'title' => 'Question #43',
                'question_body' => 'Manakah perbedaan utama TCP dan UDP?',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['TCP connectionless, UDP connection-oriented','TCP connection-oriented, UDP connectionless','TCP untuk multimedia, UDP untuk file','Tidak ada perbedaan'],
                    'correctAnswer' => '1'
                ])
            ],
            [
                'title' => 'Question #44',
                'question_body' => 'Manakah tipe NoSQL yang cocok untuk data relasional?',
                'question_type_id' => '1',
                'difficulty' => 'hard',
                'answer' => json_encode([
                    'answers' => ['Document store','Key-Value store','Column family','Graph database'],
                    'correctAnswer' => '2'
                ])
            ],
            [
                'title' => 'Question #45',
                'question_body' => 'Apa itu containerization (mis. Docker)?',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['Mengisolasi proses di kernel','Membuat VM penuh','Menyediakan storage jaringan','Jenis bahasa pemrograman'],
                    'correctAnswer' => '0'
                ])
            ],
            [
                'title' => 'Question #46',
                'question_body' => 'Concurrency issue di mana dua proses mengakses resource yang sama disebut...',
                'question_type_id' => '1',
                'difficulty' => 'hard',
                'answer' => json_encode([
                    'answers' => ['Deadlock','Race condition','Segmentation fault','Memory leak'],
                    'correctAnswer' => '1'
                ])
            ],
            [
                'title' => 'Question #47',
                'question_body' => 'Dalam DevOps, CI dalam CI/CD berarti...',
                'question_type_id' => '1',
                'difficulty' => 'easy',
                'answer' => json_encode([
                    'answers' => ['Continuous Integration','Continuous Inspection','Code Integration','Continuous Improvement'],
                    'correctAnswer' => '0'
                ])
            ],
            [
                'title' => 'Question #48',
                'question_body' => 'Manakah tool container orchestration yang populer?',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['Docker Compose','Kubernetes','Vagrant','Jenkins'],
                    'correctAnswer' => '1'
                ])
            ],
            [
                'title' => 'Question #49',
                'question_body' => 'Manakah statement SQL untuk mengambil semua data dari tabel users?',
                'question_type_id' => '1',
                'difficulty' => 'easy',
                'answer' => json_encode([
                    'answers' => ['SELECT * FROM users;','GET /users','FETCH ALL users','EXTRACT users'],
                    'correctAnswer' => '0'
                ])
            ],
            [
                'title' => 'Question #50',
                'question_body' => 'Saat melakukan backup basis data, salah satu tujuan utama adalah...',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['Menghapus data lama','Memulihkan data bila terjadi kegagalan','Meningkatkan performa','Mengurangi ukuran database'],
                    'correctAnswer' => '1'
                ])
            ],

            // 51-60
            [
                'title' => 'Question #51',
                'question_body' => 'Apa fungsi DNS?',
                'question_type_id' => '1',
                'difficulty' => 'easy',
                'answer' => json_encode([
                    'answers' => ['Mentransfer file','Menerjemahkan nama domain ke alamat IP','Mengirim email','Mengkompresi data'],
                    'correctAnswer' => '1'
                ])
            ],
            [
                'title' => 'Question #52',
                'question_body' => 'OAuth digunakan untuk ...',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['Autentikasi sederhana','Otorisasi akses terdelegasi','Manajemen database','Pengaturan cache'],
                    'correctAnswer' => '1'
                ])
            ],
            [
                'title' => 'Question #53',
                'question_body' => 'Manakah operasi yang mengubah struktur tabel (mis. menambah kolom)?',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['ALTER','SELECT','INSERT','DROP'],
                    'correctAnswer' => '0'
                ])
            ],
            [
                'title' => 'Question #54',
                'question_body' => 'Apa itu firewall?',
                'question_type_id' => '1',
                'difficulty' => 'easy',
                'answer' => json_encode([
                    'answers' => ['Perangkat lunak untuk mempercepat jaringan','Sistem untuk mengontrol lalu lintas jaringan yang diizinkan','Jenis server database','Protokol enkripsi'],
                    'correctAnswer' => '1'
                ])
            ],
            [
                'title' => 'Question #55',
                'question_body' => 'Manakah teknik untuk mencegah SQL injection?',
                'question_type_id' => '1',
                'difficulty' => 'hard',
                'answer' => json_encode([
                    'answers' => ['Concatenating user input ke query','Menggunakan prepared statements / parameterized queries','Menonaktifkan database','Menggunakan plain text password'],
                    'correctAnswer' => '1'
                ])
            ],
            [
                'title' => 'Question #56',
                'question_body' => 'Manakah pernyataan yang tepat tentang garbage collection?',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['Membersihkan file sistem','Mengelola memori secara otomatis dengan menghapus objek yang tidak terpakai','Proses kompilasi','Proses backup otomatis'],
                    'correctAnswer' => '1'
                ])
            ],
            [
                'title' => 'Question #57',
                'question_body' => 'Manakah yang bukan manfaat menggunakan CDN?',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['Mengurangi latency','Meningkatkan ketersediaan konten','Menghapus kebutuhan cache di browser','Mengurangi beban server asal'],
                    'correctAnswer' => '2'
                ])
            ],
            [
                'title' => 'Question #58',
                'question_body' => 'Manakah format gambar yang mendukung transparansi?',
                'question_type_id' => '1',
                'difficulty' => 'easy',
                'answer' => json_encode([
                    'answers' => ['JPEG','BMP','PNG','TIFF'],
                    'correctAnswer' => '2'
                ])
            ],
            [
                'title' => 'Question #59',
                'question_body' => 'Manakah metode untuk menjaga versi dependensi proyek secara otomatis?',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['Manual mendownload setiap library','Menggunakan package manager (npm/composer/pip)','Menulis semua kode sendiri','Menghapus dependensi'],
                    'correctAnswer' => '1'
                ])
            ],
            [
                'title' => 'Question #60',
                'question_body' => 'Manakah yang termasuk tugas unit testing?',
                'question_type_id' => '1',
                'difficulty' => 'easy',
                'answer' => json_encode([
                    'answers' => ['Menguji keseluruhan aplikasi di produksi','Menguji bagian kecil/kode unit secara terisolasi','Melakukan load test','Memantau server'],
                    'correctAnswer' => '1'
                ])
            ],

            // 61-70
            [
                'title' => 'Question #61',
                'question_body' => 'Manakah dari berikut ini biasanya digunakan untuk penyimpanan kunci- nilai cepat?',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['Redis','MySQL','PostgreSQL','SQLite'],
                    'correctAnswer' => '0'
                ])
            ],
            [
                'title' => 'Question #62',
                'question_body' => 'Apa tujuan load balancer?',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['Mengalokasikan bandwidth ke satu server','Mendistribusikan lalu lintas ke beberapa server untuk ketersediaan dan skalabilitas','Mengganti nama domain','Mempercepat kompilasi kode'],
                    'correctAnswer' => '1'
                ])
            ],
            [
                'title' => 'Question #63',
                'question_body' => 'Apa tujuan dari indexing pada database?',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['Memperlambat query','Meningkatkan kecepatan pencarian pada kolom tertentu','Menggandakan data','Menjalankan backup otomatis'],
                    'correctAnswer' => '1'
                ])
            ],
            [
                'title' => 'Question #64',
                'question_body' => 'Manakah yang paling cocok untuk query analitik besar (OLAP)?',
                'question_type_id' => '1',
                'difficulty' => 'hard',
                'answer' => json_encode([
                    'answers' => ['Transactional RDBMS','Data warehouse','Key-value store','In-memory cache'],
                    'correctAnswer' => '1'
                ])
            ],
            [
                'title' => 'Question #65',
                'question_body' => 'Manakah paradigma pemrograman yang menekankan fungsi murni dan tanpa state?',
                'question_type_id' => '1',
                'difficulty' => 'hard',
                'answer' => json_encode([
                    'answers' => ['Imperative','Procedural','Functional','Object-oriented'],
                    'correctAnswer' => '2'
                ])
            ],
            [
                'title' => 'Question #66',
                'question_body' => 'Manakah metode autentikasi dua faktor (2FA)?',
                'question_type_id' => '1',
                'difficulty' => 'easy',
                'answer' => json_encode([
                    'answers' => ['Password saja','Password + SMS code','Username saja','IP address'],
                    'correctAnswer' => '1'
                ])
            ],
            [
                'title' => 'Question #67',
                'question_body' => 'Apa tujuan profiling pada pengembangan perangkat lunak?',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['Menentukan bug logika semata','Mengukur performa dan menemukan bottleneck','Menjalankan unit test','Mengompilasi kode menjadi binary'],
                    'correctAnswer' => '1'
                ])
            ],
            [
                'title' => 'Question #68',
                'question_body' => 'Manakah konsep yang memastikan sistem terdistribusi tetap tersedia meski terjadi partisi jaringan?',
                'question_type_id' => '1',
                'difficulty' => 'hard',
                'answer' => json_encode([
                    'answers' => ['CAP theorem - Consistency','CAP theorem - Availability','ACID','Two-phase commit'],
                    'correctAnswer' => '1'
                ])
            ],
            [
                'title' => 'Question #69',
                'question_body' => 'Manakah yang merupakan cipher simetris?',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['RSA','AES','ECC','DSA'],
                    'correctAnswer' => '1'
                ])
            ],
            [
                'title' => 'Question #70',
                'question_body' => 'Manakah teknik untuk mengurangi latensi pada aplikasi web statis?',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['Menggunakan CDN','Menambah query database','Menonaktifkan cache browser','Meningkatkan ukuran gambar'],
                    'correctAnswer' => '0'
                ])
            ],

            // 71-80
            [
                'title' => 'Question #71',
                'question_body' => 'Apa itu HTTPS?',
                'question_type_id' => '1',
                'difficulty' => 'easy',
                'answer' => json_encode([
                    'answers' => ['HTTP over TLS/SSL','Versi lama HTTP','Protokol FTP','Format file web'],
                    'correctAnswer' => '0'
                ])
            ],
            [
                'title' => 'Question #72',
                'question_body' => 'DNS caching berguna untuk ...',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['Meningkatkan latency DNS lookup','Mengurangi beban server DNS dan mempercepat lookup','Menghapus catatan DNS','Menambah entri DNS secara otomatis'],
                    'correctAnswer' => '1'
                ])
            ],
            [
                'title' => 'Question #73',
                'question_body' => 'Manakah yang paling sesuai untuk penyimpanan dokumen semi-terstruktur?',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['Relational DB','Document DB (mis. MongoDB)','Key-value store','Flat file'],
                    'correctAnswer' => '1'
                ])
            ],
            [
                'title' => 'Question #74',
                'question_body' => 'Manakah yang bukan layer dalam model TCP/IP klasik?',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['Application','Transport','Presentation','Network Interface'],
                    'correctAnswer' => '2'
                ])
            ],
            [
                'title' => 'Question #75',
                'question_body' => 'Manakah teknik enkripsi yang umum untuk komunikasi real-time ringan?',
                'question_type_id' => '1',
                'difficulty' => 'hard',
                'answer' => json_encode([
                    'answers' => ['RSA','AES-GCM','Blowfish','MD5'],
                    'correctAnswer' => '1'
                ])
            ],
            [
                'title' => 'Question #76',
                'question_body' => 'Manakah alat yang umum digunakan untuk continuous integration?',
                'question_type_id' => '1',
                'difficulty' => 'easy',
                'answer' => json_encode([
                    'answers' => ['Photoshop','Jenkins','Excel','Postman'],
                    'correctAnswer' => '1'
                ])
            ],
            [
                'title' => 'Question #77',
                'question_body' => 'Manakah praktik yang baik untuk keamanan API publik?',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['Membuka semua endpoint tanpa batasan','Menggunakan rate limiting dan autentikasi','Menempatkan kredensial dalam URL','Mengizinkan semua origin CORS'],
                    'correctAnswer' => '1'
                ])
            ],
            [
                'title' => 'Question #78',
                'question_body' => 'Manakah yang paling tepat mendeskripsikan microservices?',
                'question_type_id' => '1',
                'difficulty' => 'hard',
                'answer' => json_encode([
                    'answers' => ['Satu aplikasi monolitik besar','Kumpulan layanan kecil yang berdiri sendiri dan berkomunikasi lewat API','Jenis basis data','Framework frontend'],
                    'correctAnswer' => '1'
                ])
            ],
            [
                'title' => 'Question #79',
                'question_body' => 'Manakah yang bukan metode backup yang umum?',
                'question_type_id' => '1',
                'difficulty' => 'easy',
                'answer' => json_encode([
                    'answers' => ['Full backup','Incremental backup','Differential backup','Sequential processing backup'],
                    'correctAnswer' => '3'
                ])
            ],
            [
                'title' => 'Question #80',
                'question_body' => 'Manakah header HTTP yang digunakan untuk otorisasi bearer token?',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['Content-Type','Authorization','Accept','User-Agent'],
                    'correctAnswer' => '1'
                ])
            ],

            // 81-90
            [
                'title' => 'Question #81',
                'question_body' => 'Manakah metode untuk menguji keamanan aplikasi secara otomatis?',
                'question_type_id' => '1',
                'difficulty' => 'hard',
                'answer' => json_encode([
                    'answers' => ['Static Application Security Testing (SAST)','Unit testing manual','Load testing saja','Profiling'],
                    'correctAnswer' => '0'
                ])
            ],
            [
                'title' => 'Question #82',
                'question_body' => 'Manakah yang menggambarkan konsep eventual consistency?',
                'question_type_id' => '1',
                'difficulty' => 'hard',
                'answer' => json_encode([
                    'answers' => ['Semua node selalu konsisten seketika','Node akan konsisten pada akhirnya setelah beberapa waktu','Tidak pernah konsisten','Konsistensi hanya di client'],
                    'correctAnswer' => '1'
                ])
            ],
            [
                'title' => 'Question #83',
                'question_body' => 'Manakah yang bukan praktik baik saat menulis API publik?',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['Versi API','Dokumentasi lengkap','Mengembalikan pesan error yang jelas','Memaparkan kredensial internal di dokumentasi'],
                    'correctAnswer' => '3'
                ])
            ],
            [
                'title' => 'Question #84',
                'question_body' => 'Manakah fungsi utama dari load testing?',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['Mengukur keamanan aplikasi','Mengukur performa di bawah beban pengguna','Mengganti konfigurasi server','Memvalidasi desain UI'],
                    'correctAnswer' => '1'
                ])
            ],
            [
                'title' => 'Question #85',
                'question_body' => 'Manakah pola desain yang memastikan hanya satu instance kelas dibuat?',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['Factory','Observer','Singleton','Decorator'],
                    'correctAnswer' => '2'
                ])
            ],
            [
                'title' => 'Question #86',
                'question_body' => 'Apa itu memcached/redis biasanya digunakan untuk?',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['Cache in-memory untuk mempercepat akses data','Database relasional','Server email','Web server'],
                    'correctAnswer' => '0'
                ])
            ],
            [
                'title' => 'Question #87',
                'question_body' => 'Manakah yang termasuk contoh bahasa scripting di server?',
                'question_type_id' => '1',
                'difficulty' => 'easy',
                'answer' => json_encode([
                    'answers' => ['PHP','HTML','CSS','SVG'],
                    'correctAnswer' => '0'
                ])
            ],
            [
                'title' => 'Question #88',
                'question_body' => 'Manakah status HTTP yang menandakan server mengalami kesalahan internal?',
                'question_type_id' => '1',
                'difficulty' => 'easy',
                'answer' => json_encode([
                    'answers' => ['200','301','404','500'],
                    'correctAnswer' => '3'
                ])
            ],
            [
                'title' => 'Question #89',
                'question_body' => 'Manakah praktik baik dalam manajemen rahasia (secrets)?',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['Menyimpan di repository public','Menyimpan di secret manager/ vault','Menuliskannya di file config yang dikomit','Mengirim via email'],
                    'correctAnswer' => '1'
                ])
            ],
            [
                'title' => 'Question #90',
                'question_body' => 'Manakah yang paling cocok untuk real-time messaging dengan latensi rendah?',
                'question_type_id' => '1',
                'difficulty' => 'hard',
                'answer' => json_encode([
                    'answers' => ['HTTP polling','WebSocket','Plain FTP','SMTP'],
                    'correctAnswer' => '1'
                ])
            ],

            // 91-100
            [
                'title' => 'Question #91',
                'question_body' => 'Manakah yang menjelaskan istilah "scalability"?',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['Kemampuan sistem untuk menangani peningkatan beban','Kemampuan sistem untuk berjalan tanpa bug','Jumlah baris kode','Kapasitas penyimpanan saja'],
                    'correctAnswer' => '0'
                ])
            ],
            [
                'title' => 'Question #92',
                'question_body' => 'Manakah tugas dari package manager seperti Composer/NPM?',
                'question_type_id' => '1',
                'difficulty' => 'easy',
                'answer' => json_encode([
                    'answers' => ['Mengatur dependensi proyek','Menggandakan repository','Menjalankan aplikasi web','Membuat virtual machine'],
                    'correctAnswer' => '0'
                ])
            ],
            [
                'title' => 'Question #93',
                'question_body' => 'Encryption one-way yang sering dipakai untuk menyimpan password adalah...',
                'question_type_id' => '1',
                'difficulty' => 'hard',
                'answer' => json_encode([
                    'answers' => ['AES','SHA family / bcrypt/argon2','RSA','DES'],
                    'correctAnswer' => '1'
                ])
            ],
            [
                'title' => 'Question #94',
                'question_body' => 'Manakah teknologi yang biasanya digunakan untuk container image?',
                'question_type_id' => '1',
                'difficulty' => 'easy',
                'answer' => json_encode([
                    'answers' => ['Docker','VirtualBox','KVM','Ansible'],
                    'correctAnswer' => '0'
                ])
            ],
            [
                'title' => 'Question #95',
                'question_body' => 'Manakah pernyataan yang benar tentang REST?',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['REST adalah protokol','REST adalah arsitektur yang menggunakan HTTP dan resource','REST hanya untuk database','REST mengharuskan SOAP'],
                    'correctAnswer' => '1'
                ])
            ],
            [
                'title' => 'Question #96',
                'question_body' => 'Manakah yang menggambarkan "refactoring"?',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['Mengubah fungsionalitas utama aplikasi','Membersihkan dan memperbaiki struktur kode tanpa mengubah perilaku','Menambah fitur baru besar','Menghapus tests'],
                    'correctAnswer' => '1'
                ])
            ],
            [
                'title' => 'Question #97',
                'question_body' => 'Manakah konsep untuk memecah beban kerja ke beberapa instance agar tahan kegagalan?',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['Sharding/Partitioning','Inlining','Monolithization','Compaction'],
                    'correctAnswer' => '0'
                ])
            ],
            [
                'title' => 'Question #98',
                'question_body' => 'Apa perbedaan primary key dan unique key?',
                'question_type_id' => '1',
                'difficulty' => 'hard',
                'answer' => json_encode([
                    'answers' => ['Tidak ada perbedaan','Primary key unik dan tidak boleh NULL; unique key unik tetapi boleh NULL','Unique key lebih cepat','Primary key untuk indexing saja'],
                    'correctAnswer' => '1'
                ])
            ],
            [
                'title' => 'Question #99',
                'question_body' => 'Manakah yang menjelaskan "rate limiting" pada API?',
                'question_type_id' => '1',
                'difficulty' => 'medium',
                'answer' => json_encode([
                    'answers' => ['Membatasi ukuran payload','Membatasi jumlah permintaan per unit waktu','Meningkatkan timeout','Menghapus header'],
                    'correctAnswer' => '1'
                ])
            ],
            [
                'title' => 'Question #100',
                'question_body' => 'Manakah teknik optimasi basis data untuk mempercepat JOIN antar tabel besar?',
                'question_type_id' => '1',
                'difficulty' => 'hard',
                'answer' => json_encode([
                    'answers' => ['Menambahkan index pada kolom yang digunakan untuk JOIN','Menghapus semua index','Menambah kolom duplikat untuk setiap tabel','Mengganti SQL menjadi NoSQL tanpa analisa'],
                    'correctAnswer' => '0'
                ])
            ],
        ];

        $createdQuestions = [];
        foreach ($items as $item) {
            $createdQuestions[] = \App\Models\Question::factory()->create($item);
        }

        $quizess = Quiz::all();
        $number = 0;
        $totalQuestion = count($createdQuestions);
        foreach ($quizess as $quiz) {
            for ($counter = 1; $counter <= 3; $counter++) {
                $q = $createdQuestions[$number];
                $quiz->Questions()->attach(
                    $q,
                    ['order' => $quiz->Questions()->max('order') + 1]
                );
                $number++;
                if ($number >= $totalQuestion) {
                    $number = 0;
                }
            }
        }
    }
}

