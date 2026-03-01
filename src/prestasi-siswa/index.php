<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prestasi - SMA Negeri 6 Cimahi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1e40af',
                        secondary: '#3b82f6',
                        accent: '#f59e0b'
                    }
                }
            }
        }
    </script>
    <style>
        .hero-gradient {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .filter-btn.active {
            background-color: #1e40af;
            color: white;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navbar -->
    <nav class="bg-white shadow-lg fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <i class="fas fa-trophy text-2xl text-primary mr-2"></i>
                    <span class="font-bold text-xl text-gray-800">Prestasi SMA Negeri 6 Cimahi</span>
                </div>
                <div class="hidden md:flex space-x-8">
                    <a href="#home" class="text-gray-700 hover:text-primary">Beranda</a>
                    <a href="#prestasi-siswa" class="text-gray-700 hover:text-primary">Siswa</a>
                    <a href="#prestasi-guru" class="text-gray-700 hover:text-primary">Guru</a>
                    <a href="#prestasi-sekolah" class="text-gray-700 hover:text-primary">Sekolah</a>
                    <a href="#alumni-ptn" class="text-gray-700 hover:text-primary">Alumni PTN</a>
                    <a href="#rekapan" class="text-gray-700 hover:text-primary">Rekapan</a>
                    <a href="admin/login.php" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        <i class="fas fa-sign-in-alt mr-1"></i> Login
                    </a>
                </div>
                <button class="md:hidden" onclick="toggleMenu()">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
        <div id="mobileMenu" class="hidden md:hidden bg-white border-t">
            <a href="#home" class="block px-4 py-2 text-gray-700">Beranda</a>
            <a href="#prestasis" class="block px-4 py-2 text-gray-700">Prestasi</a>
            <a href="#rekapan" class="block px-4 py-2 text-gray-700">Rekapan</a>
            <a href="#tentang" class="block px-4 py-2 text-gray-700">Tentang</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-gradient pt-24 pb-16">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center text-white">
                <h1 class="text-4xl md:text-6xl font-bold mb-4">Prestasi SMA Negeri 6 Cimahi</h1>
                <p class="text-xl md:text-2xl mb-8 opacity-90">Meraih Masa Depan Melalui Prestasi</p>
                <div class="flex justify-center gap-8 flex-wrap">
                    <div class="bg-white/20 backdrop-blur rounded-xl p-6 min-w-32">
                        <div class="text-4xl font-bold" id="totalPrestasi">0</div>
                        <div class="text-sm">Total Prestasi</div>
                    </div>
                    <div class="bg-white/20 backdrop-blur rounded-xl p-6 min-w-32">
                        <div class="text-4xl font-bold" id="totalSiswa">0</div>
                        <div class="text-sm">Siswa Berprestrasi</div>
                    </div>
                    <div class="bg-white/20 backdrop-blur rounded-xl p-6 min-w-32">
                        <div class="text-4xl font-bold" id="totalJuara">0</div>
                        <div class="text-sm">Juara 1-3</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Prestasi Siswa Section -->
    <section id="prestasi-siswa" class="py-16">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Prestasi Siswa</h2>
                <p class="text-gray-600 mt-2">Kumpulan prestasi terbaik siswa kami</p>
            </div>
            <div class="bg-white shadow-md mb-6 mx-4 rounded-xl p-4">
                <div class="flex flex-wrap gap-4 items-center">
                    <div class="flex-1 min-w-64">
                        <input type="text" id="searchInput" placeholder="Cari nama siswa atau lomba..." 
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:outline-none">
                    </div>
                    <select id="filterJenis" class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary">
                        <option value="">Semua Jenis</option>
                        <option value="akademik">Akademik</option>
                        <option value="non-akademik">Non-Akademik</option>
                    </select>
                <select id="filterTingkat" class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary">
                    <option value="">Semua Tingkat</option>
                    <option value="internasional">Internasional</option>
                    <option value="nasional">Nasional</option>
                    <option value="provinsi">Provinsi</option>
                    <option value="kota">Kota</option>
                    <option value="kecamatan">Kecamatan</option>
                    <option value="sekolah">Sekolah</option>
                </select>
                <select id="filterTahun" class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary">
                    <option value="">Semua Tahun</option>
                </select>
                <button onclick="applyFilters()" class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                        <i class="fas fa-search mr-1"></i> Cari
                    </button>
                </div>
            </div>
            <div id="prestasiGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Cards will be loaded via JS -->
            </div>
            <div class="text-center mt-8">
                <button id="loadMoreBtn" onclick="loadMore()" class="bg-white border-2 border-primary text-primary px-8 py-3 rounded-lg hover:bg-primary hover:text-white transition">
                    Load More
                </button>
            </div>
        </div>
    </section>

    <!-- Prestasi Guru Section -->
    <section id="prestasi-guru" class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Prestasi Guru</h2>
                <p class="text-gray-600 mt-2">Prestasi dan pencapaian guru-guru kami</p>
            </div>
            <div id="prestasGuruGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="col-span-full text-center text-gray-500 py-8">
                    <i class="fas fa-chalkboard-teacher text-4xl mb-4"></i>
                    <p>Loading...</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Prestasi Sekolah Section -->
    <section id="prestasi-sekolah" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Prestasi Sekolah</h2>
                <p class="text-gray-600 mt-2">Pencapaian dan prestise sekolah</p>
            </div>
            <div id="prestasSekolahGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="col-span-full text-center text-gray-500 py-8">
                    <i class="fas fa-school text-4xl mb-4"></i>
                    <p>Loading...</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Alumni PTN Section -->
    <section id="alumni-ptn" class="py-16 bg-teal-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Alumni ke PTN</h2>
                <p class="text-gray-600 mt-2">Siswa yang melanjutkan ke Perguruan Tinggi Negeri</p>
            </div>
            <div id="alumniGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="col-span-full text-center text-gray-500 py-8">
                    <i class="fas fa-graduation-cap text-4xl mb-4"></i>
                    <p>Loading...</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Rekapan Section -->
    <section id="rekapan" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Rekapan Prestasi</h2>
                <p class="text-gray-600 mt-2">Statistik lengkap seluruh prestasi</p>
            </div>
            
            <!-- Total Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white">
                    <div class="text-3xl font-bold" id="totalPrestasiSiswa">0</div>
                    <div class="text-sm opacity-90">Prestasi Siswa</div>
                </div>
                <div class="bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl p-6 text-white">
                    <div class="text-3xl font-bold" id="totalPrestasiGuru">0</div>
                    <div class="text-sm opacity-90">Prestasi Guru</div>
                </div>
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white">
                    <div class="text-3xl font-bold" id="totalPrestasiSekolah">0</div>
                    <div class="text-sm opacity-90">Prestasi Sekolah</div>
                </div>
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white">
                    <div class="text-3xl font-bold" id="totalAlumniPTN">0</div>
                    <div class="text-sm opacity-90">Alumni PTN</div>
                </div>
            </div>

            <!-- Tabs for different categories -->
            <div class="bg-gray-50 rounded-xl mb-8">
                <div class="border-b">
                    <nav class="flex" id="rekapanTabs">
                        <button onclick="showRekapanTab('rekapan-siswa')" class="rekapan-tab-btn px-6 py-4 text-primary border-b-2 border-primary font-medium" data-tab="rekapan-siswa">
                            <i class="fas fa-user-graduate mr-2"></i> Siswa
                        </button>
                        <button onclick="showRekapanTab('rekapan-guru')" class="rekapan-tab-btn px-6 py-4 text-gray-500 hover:text-primary" data-tab="rekapan-guru">
                            <i class="fas fa-chalkboard-teacher mr-2"></i> Guru
                        </button>
                        <button onclick="showRekapanTab('rekapan-sekolah')" class="rekapan-tab-btn px-6 py-4 text-gray-500 hover:text-primary" data-tab="rekapan-sekolah">
                            <i class="fas fa-school mr-2"></i> Sekolah
                        </button>
                        <button onclick="showRekapanTab('rekapan-alumni')" class="rekapan-tab-btn px-6 py-4 text-gray-500 hover:text-primary" data-tab="rekapan-alumni">
                            <i class="fas fa-graduation-cap mr-2"></i> Alumni PTN
                        </button>
                    </nav>
                </div>

                <!-- Rekapan Siswa -->
                <div id="rekapan-siswa" class="rekapan-content p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-bold mb-4 flex items-center"><i class="fas fa-chart-bar mr-2 text-blue-500"></i>Per Tingkat</h4>
                            <div id="chartTingkatSiswa" class="space-y-3"></div>
                        </div>
                        <div>
                            <h4 class="font-bold mb-4 flex items-center"><i class="fas fa-chart-pie mr-2 text-blue-500"></i>Per Jenis</h4>
                            <div id="chartJenisSiswa" class="space-y-3"></div>
                        </div>
                    </div>
                    <div class="mt-6">
                        <h4 class="font-bold mb-4 flex items-center"><i class="fas fa-calendar mr-2 text-blue-500"></i>Prestasi per Tahun</h4>
                        <div id="chartTahunSiswa" class="grid grid-cols-3 md:grid-cols-6 gap-3"></div>
                    </div>
                </div>

                <!-- Rekapan Guru -->
                <div id="rekapan-guru" class="rekapan-content p-6 hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-bold mb-4 flex items-center"><i class="fas fa-chart-bar mr-2 text-teal-500"></i>Per Tingkat</h4>
                            <div id="chartTingkatGuru" class="space-y-3"></div>
                        </div>
                        <div>
                            <h4 class="font-bold mb-4 flex items-center"><i class="fas fa-chart-pie mr-2 text-teal-500"></i>Per Jenis</h4>
                            <div id="chartJenisGuru" class="space-y-3"></div>
                        </div>
                    </div>
                </div>

                <!-- Rekapan Sekolah -->
                <div id="rekapan-sekolah" class="rekapan-content p-6 hidden">
                    <div>
                        <h4 class="font-bold mb-4 flex items-center"><i class="fas fa-chart-bar mr-2 text-purple-500"></i>Prestasi Sekolah per Tingkat</h4>
                        <div id="chartTingkatSekolah" class="space-y-3"></div>
                    </div>
                </div>

                <!-- Rekapan Alumni -->
                <div id="rekapan-alumni" class="rekapan-content p-6 hidden">
                    <div>
                        <h4 class="font-bold mb-4 flex items-center"><i class="fas fa-university mr-2 text-green-500"></i>PTN Favorit</h4>
                        <div id="chartPTNFavorit" class="space-y-3"></div>
                    </div>
                </div>
            </div>
            
            <!-- Ranking -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4 flex items-center"><i class="fas fa-trophy text-yellow-500 mr-2"></i>Top 10 Siswa</h3>
                    <div class="bg-gray-50 rounded-xl overflow-hidden">
                        <table class="w-full">
                            <thead class="bg-blue-600 text-white">
                                <tr>
                                    <th class="py-3 px-4 text-center">#</th>
                                    <th class="py-3 px-4 text-left">Nama</th>
                                    <th class="py-3 px-4 text-center">Prestasi</th>
                                </tr>
                            </thead>
                            <tbody id="rankingSiswaTable"></tbody>
                        </table>
                    </div>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4 flex items-center"><i class="fas fa-medal text-teal-500 mr-2"></i>Top 10 Guru</h3>
                    <div class="bg-gray-50 rounded-xl overflow-hidden">
                        <table class="w-full">
                            <thead class="bg-teal-600 text-white">
                                <tr>
                                    <th class="py-3 px-4 text-center">#</th>
                                    <th class="py-3 px-4 text-left">Nama</th>
                                    <th class="py-3 px-4 text-center">Prestasi</th>
                                </tr>
                            </thead>
                            <tbody id="rankingGuruTable"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="mt-8 text-center">
                <button onclick="exportExcel()" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700">
                    <i class="fas fa-file-excel mr-2"></i> Export ke Excel
                </button>
            </div>
        </div>
    </section>

    <!-- Tentang Section -->
    <section id="tentang" class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Tentang Kami</h2>
            </div>
            <div class="max-w-3xl mx-auto text-center">
                <p class="text-gray-600 text-lg">
                    Website Prestasi Siswa SMA Negeri 6 Cimahi dibuat untuk mendokumentasikan dan memperkenalkan 
                    berbagai prestasi yang telah diraih oleh siswa-siswa kami. Kami percaya bahwa 
                    setiap prestasi, baik akademik maupun non-akademik, merupakan langkah penting 
                    dalam perjalanan pendidikan dan pengembangan karakter siswa.
                </p>
                <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div class="text-center">
                        <i class="fas fa-star text-4xl text-accent mb-2"></i>
                        <div class="font-bold">Berbagai Tingkat</div>
                        <div class="text-sm text-gray-500">Internasional - Sekolah</div>
                    </div>
                    <div class="text-center">
                        <i class="fas fa-users text-4xl text-accent mb-2"></i>
                        <div class="font-bold">Banyak Siswa</div>
                        <div class="text-sm text-gray-500">Aktif Berprestrasi</div>
                    </div>
                    <div class="text-center">
                        <i class="fas fa-trophy text-4xl text-accent mb-2"></i>
                        <div class="font-bold">Juara</div>
                        <div class="text-sm text-gray-500">1, 2, 3 & Finalis</div>
                    </div>
                    <div class="text-center">
                        <i class="fas fa-calendar text-4xl text-accent mb-2"></i>
                        <div class="font-bold">Terupdate</div>
                        <div class="text-sm text-gray-500">Data Terbaru</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p>&copy; 2026 Prestasi SMA Negeri 6 Cimahi. All rights reserved.</p>
            <p class="text-gray-400 mt-2">Dibuat dengan <i class="fas fa-heart text-red-500"></i> untuk pendidikan</p>
        </div>
    </footer>

    <!-- Lightbox Modal -->
    <div id="lightbox" class="fixed inset-0 bg-black/90 z-50 hidden flex items-center justify-center" onclick="closeLightbox()">
        <button class="absolute top-4 right-4 text-white text-3xl">&times;</button>
        <img id="lightboxImg" src="" alt="Foto" class="max-w-4xl max-h-screen">
    </div>

    <script>
        let page = 1;
        let allData = [];
        
        function toggleMenu() {
            document.getElementById('mobileMenu').classList.toggle('hidden');
        }

        function openLightbox(src) {
            document.getElementById('lightboxImg').src = src;
            document.getElementById('lightbox').classList.remove('hidden');
        }

        function closeLightbox() {
            document.getElementById('lightbox').classList.add('hidden');
        }

        async function fetchStats() {
            const res = await fetch('api/stats.php');
            const data = await res.json();
            
            document.getElementById('totalPrestasi').textContent = data.totalPrestasi;
            document.getElementById('totalSiswa').textContent = data.totalSiswa;
            document.getElementById('totalJuara').textContent = data.totalJuara;
        }

        async function fetchPrestasi(reset = false) {
            if (reset) {
                page = 1;
                allData = [];
            }
            
            const search = document.getElementById('searchInput').value;
            const jenis = document.getElementById('filterJenis').value;
            const tingkat = document.getElementById('filterTingkat').value;
            const tahunEl = document.getElementById('filterTahun');
            const tahun = tahunEl ? tahunEl.value : '';
            
            const params = new URLSearchParams({
                page, limit: 9, search, jenis, tingkat, tahun
            });
            
            const res = await fetch(`api/prestasi.php?${params}`);
            const data = await res.json();
            
            if (reset) {
                allData = data;
            } else {
                allData = [...allData, ...data];
            }
            
            renderPrestasi(allData);
            
            if (data.length < 9) {
                document.getElementById('loadMoreBtn').style.display = 'none';
            } else {
                document.getElementById('loadMoreBtn').style.display = 'inline-block';
            }
        }

        function renderPrestasi(data) {
            const grid = document.getElementById('prestasiGrid');
            
            const tingkatColors = {
                'internasional': 'bg-purple-100 text-purple-800',
                'nasional': 'bg-red-100 text-red-800',
                'provinsi': 'bg-orange-100 text-orange-800',
                'kota': 'bg-blue-100 text-blue-800',
                'kecamatan': 'bg-green-100 text-green-800',
                'sekolah': 'bg-gray-100 text-gray-800'
            };
            
            const jenisIcons = {
                'akademik': 'fa-book',
                'non-akademik': 'fa-palette'
            };
            
            const poin = { 'internasional': 5, 'nasional': 4, 'provinsi': 3, 'kota': 2, 'kecamatan': 1, 'sekolah': 0.5 };
            
            grid.innerHTML = data.map(p => `
                <div class="bg-white rounded-xl shadow-lg overflow-hidden card-hover transition duration-300">
                    <div class="h-48 bg-gradient-to-br from-primary to-secondary flex items-center justify-center relative overflow-hidden">
                        ${p.foto_sertifikat 
                            ? `<img src="uploads/${p.foto_sertifikat}" alt="Foto" class="w-full h-full object-contain" onclick="openLightbox('uploads/${p.foto_sertifikat}')">`
                            : `<i class="fas fa-trophy text-6xl text-white/30"></i>`
                        }
                        <span class="absolute top-2 right-2 ${tingkatColors[p.tingkat]} px-3 py-1 rounded-full text-xs font-bold">
                            ${p.tingkat.toUpperCase()}
                        </span>
                    </div>
                    <div class="p-5">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="bg-primary/10 text-primary px-2 py-1 rounded text-xs">
                                <i class="fas ${jenisIcons[p.jenis_prestasi]} mr-1"></i>
                                ${p.jenis_prestasi.toUpperCase()}
                            </span>
                            <span class="bg-gray-200 text-gray-700 px-2 py-1 rounded text-xs">
                                <i class="fas ${p.jenis_peserta === 'kelompok' ? 'fa-users' : 'fa-user'} mr-1"></i>
                                ${p.jenis_peserta === 'kelompok' ? 'TIM' : 'Perorangan'}
                            </span>
                            <span class="bg-accent text-white px-2 py-1 rounded text-xs font-bold">
                                JUARA ${p.peringkat}
                            </span>
                        </div>
                        <h3 class="font-bold text-lg text-gray-800 mb-1">${p.nama_lomba}</h3>
                        <p class="text-primary font-semibold">${p.jenis_peserta === 'kelompok' && p.nama_tim ? p.nama_tim : p.nama_siswa}</p>
                        <p class="text-gray-500 text-sm">${p.jenis_peserta === 'kelompok' ? (p.nama_tim + ' | ') : (p.kelas + ' | ')} ${formatDate(p.tanggal)}</p>
                        <p class="text-gray-600 text-sm mt-2">${p.penyelenggara || ''}</p>
                        <div class="mt-4 flex justify-between items-center">
                            <span class="text-xs text-gray-400">${poin[p.tingkat]} poin</span>
                            <button onclick="showDetail(${p.id})" class="text-primary hover:underline text-sm">
                                Lihat Detail <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        async function loadMore() {
            page++;
            await fetchPrestasi(false);
        }

        function applyFilters() {
            fetchPrestasi(true);
        }

        async function fetchRekapan() {
            const res = await fetch('api/rekapan.php');
            const data = await res.json();
            
            // Total cards
            document.getElementById('totalPrestasiSiswa').textContent = data.totalSiswa || 0;
            document.getElementById('totalPrestasiGuru').textContent = data.totalGuru || 0;
            document.getElementById('totalPrestasiSekolah').textContent = data.totalSekolah || 0;
            document.getElementById('totalAlumniPTN').textContent = data.totalAlumni || 0;
            
            const totalSiswa = data.totalSiswa || 1;
            const totalGuru = data.totalGuru || 1;
            const totalSekolah = data.totalSekolah || 1;
            const totalAlumni = data.totalAlumni || 1;
            
            // Chart Tingkat - Siswa
            const tingkatColorsSiswa = { 'internasional': '#8b5cf6', 'nasional': '#ef4444', 'provinsi': '#f97316', 'kota': '#3b82f6', 'kecamatan': '#22c55e', 'sekolah': '#6b7280' };
            const tingkatSiswaHtml = Object.entries(data.perTingkatSiswa || {}).map(([key, val]) => `
                <div class="flex items-center justify-between">
                    <span class="capitalize">${key}</span>
                    <div class="flex items-center">
                        <div class="w-32 h-5 bg-gray-200 rounded-full overflow-hidden mr-2">
                            <div class="h-full bg-blue-500" style="width: ${(val / totalSiswa) * 100}%"></div>
                        </div>
                        <span class="font-bold text-blue-600">${val}</span>
                    </div>
                </div>
            `).join('') || '<p class="text-gray-500 text-sm">Belum ada data</p>';
            document.getElementById('chartTingkatSiswa').innerHTML = tingkatSiswaHtml;
            
            // Chart Jenis - Siswa
            const warnaJenis = { 'akademik': 'bg-blue-500', 'non-akademik': 'bg-purple-500' };
            const jenisSiswaHtml = Object.entries(data.perJenisSiswa || {}).map(([key, val]) => `
                <div class="flex items-center justify-between">
                    <span class="capitalize">${key}</span>
                    <div class="flex items-center">
                        <div class="w-32 h-5 bg-gray-200 rounded-full overflow-hidden mr-2">
                            <div class="h-full ${warnaJenis[key] || 'bg-gray-500'}" style="width: ${(val / totalSiswa) * 100}%"></div>
                        </div>
                        <span class="font-bold">${val}</span>
                    </div>
                </div>
            `).join('') || '<p class="text-gray-500 text-sm">Belum ada data</p>';
            document.getElementById('chartJenisSiswa').innerHTML = jenisSiswaHtml;
            
            // Chart Tahun - Siswa
            const tahunSiswaHtml = Object.entries(data.perTahunSiswa || {}).map(([key, val]) => `
                <div class="bg-white p-3 rounded-lg text-center shadow-sm">
                    <div class="text-xl font-bold text-blue-600">${val}</div>
                    <div class="text-xs text-gray-500">${key}</div>
                </div>
            `).join('') || '<p class="text-gray-500 text-sm">Belum ada data</p>';
            document.getElementById('chartTahunSiswa').innerHTML = tahunSiswaHtml;
            
            // Chart Tingkat - Guru
            const tingkatGuruHtml = Object.entries(data.perTingkatGuru || {}).map(([key, val]) => `
                <div class="flex items-center justify-between">
                    <span class="capitalize">${key}</span>
                    <div class="flex items-center">
                        <div class="w-32 h-5 bg-gray-200 rounded-full overflow-hidden mr-2">
                            <div class="h-full bg-teal-500" style="width: ${(val / totalGuru) * 100}%"></div>
                        </div>
                        <span class="font-bold text-teal-600">${val}</span>
                    </div>
                </div>
            `).join('') || '<p class="text-gray-500 text-sm">Belum ada data</p>';
            document.getElementById('chartTingkatGuru').innerHTML = tingkatGuruHtml;
            
            // Chart Jenis - Guru
            const warnaJenisGuru = { 'akademik': 'bg-teal-500', 'non-akademik': 'bg-cyan-500', 'penelitian': 'bg-indigo-500', 'kompetisi': 'bg-blue-500' };
            const jenisGuruHtml = Object.entries(data.perJenisGuru || {}).map(([key, val]) => `
                <div class="flex items-center justify-between">
                    <span class="capitalize">${key}</span>
                    <div class="flex items-center">
                        <div class="w-32 h-5 bg-gray-200 rounded-full overflow-hidden mr-2">
                            <div class="h-full ${warnaJenisGuru[key] || 'bg-gray-500'}" style="width: ${(val / totalGuru) * 100}%"></div>
                        </div>
                        <span class="font-bold">${val}</span>
                    </div>
                </div>
            `).join('') || '<p class="text-gray-500 text-sm">Belum ada data</p>';
            document.getElementById('chartJenisGuru').innerHTML = jenisGuruHtml;
            
            // Chart Tingkat - Sekolah
            const tingkatSekolahHtml = Object.entries(data.perTingkatSekolah || {}).map(([key, val]) => `
                <div class="flex items-center justify-between">
                    <span class="capitalize">${key}</span>
                    <div class="flex items-center">
                        <div class="w-32 h-5 bg-gray-200 rounded-full overflow-hidden mr-2">
                            <div class="h-full bg-purple-500" style="width: ${(val / totalSekolah) * 100}%"></div>
                        </div>
                        <span class="font-bold text-purple-600">${val}</span>
                    </div>
                </div>
            `).join('') || '<p class="text-gray-500 text-sm">Belum ada data</p>';
            document.getElementById('chartTingkatSekolah').innerHTML = tingkatSekolahHtml;
            
            // PTN Favorit
            const ptnHtml = Object.entries(data.ptnFavorit || {}).map(([key, val]) => `
                <div class="flex items-center justify-between">
                    <span>${key}</span>
                    <div class="flex items-center">
                        <div class="w-32 h-5 bg-gray-200 rounded-full overflow-hidden mr-2">
                            <div class="h-full bg-green-500" style="width: ${(val / totalAlumni) * 100}%"></div>
                        </div>
                        <span class="font-bold text-green-600">${val}</span>
                    </div>
                </div>
            `).join('') || '<p class="text-gray-500 text-sm">Belum ada data</p>';
            document.getElementById('chartPTNFavorit').innerHTML = ptnHtml;
            
            // Ranking Siswa
            const rankingSiswaHtml = (data.rankingSiswa || []).map((s, i) => `
                <tr class="${i < 3 ? 'bg-yellow-50' : ''} border-b">
                    <td class="py-3 px-4 text-center">
                        ${i === 0 ? '<i class="fas fa-crown text-yellow-500 text-xl"></i>' : i + 1}
                    </td>
                    <td class="py-3 px-4 font-medium">${s.nama_siswa}</td>
                    <td class="py-3 px-4 text-center">${s.total}</td>
                </tr>
            `).join('') || '<tr><td colspan="3" class="text-center py-4 text-gray-500">Belum ada data</td></tr>';
            document.getElementById('rankingSiswaTable').innerHTML = rankingSiswaHtml;
            
            // Ranking Guru
            const rankingGuruHtml = (data.rankingGuru || []).map((g, i) => `
                <tr class="${i < 3 ? 'bg-teal-50' : ''} border-b">
                    <td class="py-3 px-4 text-center">
                        ${i === 0 ? '<i class="fas fa-crown text-teal-500 text-xl"></i>' : i + 1}
                    </td>
                    <td class="py-3 px-4 font-medium">${g.nama_guru}</td>
                    <td class="py-3 px-4 text-center">${g.total}</td>
                </tr>
            `).join('') || '<tr><td colspan="3" class="text-center py-4 text-gray-500">Belum ada data</td></tr>';
            document.getElementById('rankingGuruTable').innerHTML = rankingGuruHtml;
        }

        function showRekapanTab(tabName) {
            document.querySelectorAll('.rekapan-content').forEach(el => el.classList.add('hidden'));
            document.getElementById('rekapan-' + tabName.split('-')[1]).classList.remove('hidden');
            
            document.querySelectorAll('.rekapan-tab-btn').forEach(el => {
                el.classList.remove('text-primary', 'border-b-2', 'border-primary');
                el.classList.add('text-gray-500');
            });
            document.querySelector(`[data-tab="${tabName}"]`).classList.add('text-primary', 'border-b-2', 'border-primary');
            document.querySelector(`[data-tab="${tabName}"]`).classList.remove('text-gray-500');
        }

        async function loadTahunOptions() {
            const res = await fetch('api/tahun.php');
            const tahun = await res.json();
            
            const select = document.getElementById('filterTahun');
            tahun.forEach(t => {
                const option = document.createElement('option');
                option.value = t.tahun;
                option.textContent = t.tahun;
                select.appendChild(option);
            });
        }

        function showDetail(id) {
            const p = allData.find(x => x.id === id);
            if (!p) return;
            
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4';
            modal.innerHTML = `
                <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <h2 class="text-2xl font-bold">${p.nama_lomba}</h2>
                            <button onclick="this.closest('.fixed').remove()" class="text-2xl">&times;</button>
                        </div>
                        ${p.foto_sertifikat ? `
                            <div class="mb-4">
                                <img src="uploads/${p.foto_sertifikat}" alt="Foto" class="w-full max-h-64 object-contain rounded-lg cursor-pointer" onclick="openLightbox('uploads/${p.foto_sertifikat}')">
                                <p class="text-center text-sm text-gray-500 mt-1">Klik foto untuk memperbesar</p>
                            </div>
                        ` : ''}
                        <div class="space-y-3">
                            <p><strong>Jenis Peserta:</strong> ${p.jenis_peserta === 'kelompok' ? 'Kelompok/Tim' : 'Perorangan'}</p>
                            ${p.jenis_peserta === 'kelompok' && p.nama_tim 
                                ? `<p><strong>Nama Tim:</strong> ${p.nama_tim}</p>` 
                                : `<p><strong>Siswa:</strong> ${p.nama_siswa}</p>
                                   <p><strong>Kelas:</strong> ${p.kelas}</p>
                                   <p><strong>NIS:</strong> ${p.nis}</p>`}
                            <p><strong>Lomba:</strong> ${p.nama_lomba}</p>
                            <p><strong>Jenis:</strong> ${p.jenis_prestasi}</p>
                            <p><strong>Tingkat:</strong> ${p.tingkat}</p>
                            <p><strong>Peringkat:</strong> ${p.peringkat}</p>
                            <p><strong>Tanggal:</strong> ${formatDate(p.tanggal)}</p>
                            <p><strong>Penyelenggara:</strong> ${p.penyelenggara || '-'}</p>
                            ${p.deskripsi ? `<p><strong>Deskripsi:</strong> ${p.deskripsi}</p>` : ''}
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }

        function formatDate(dateStr) {
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(dateStr).toLocaleDateString('id-ID', options);
        }

        function exportExcel() {
            window.location.href = 'api/export.php';
        }

        async function fetchPrestasiGuru() {
            try {
                const res = await fetch('api/prestasi_guru.php');
                const data = await res.json();
                renderPrestasiGuru(data);
            } catch (e) {
                console.error('Error fetching guru prestasi:', e);
                document.getElementById('prestasGuruGrid').innerHTML = '<div class="col-span-full text-center text-gray-500 py-8"><i class="fas fa-exclamation-triangle text-2xl mb-2"></i><p>Data belum tersedia</p></div>';
            }
        }

        function renderPrestasiGuru(data) {
            const grid = document.getElementById('prestasGuruGrid');
            if (!data || data.length === 0) {
                grid.innerHTML = '<div class="col-span-full text-center text-gray-500 py-8"><i class="fas fa-inbox text-4xl mb-2"></i><p>Belum ada data prestasi guru</p></div>';
                return;
            }
            
            const tingkatColors = {
                'internasional': 'bg-purple-100 text-purple-800',
                'nasional': 'bg-red-100 text-red-800',
                'provinsi': 'bg-orange-100 text-orange-800',
                'kota': 'bg-blue-100 text-blue-800',
                'kecamatan': 'bg-green-100 text-green-800',
                'sekolah': 'bg-gray-100 text-gray-800'
            };
            
            grid.innerHTML = data.map(p => `
                <div class="bg-white rounded-xl shadow-lg overflow-hidden card-hover transition duration-300">
                    <div class="h-40 bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center relative overflow-hidden">
                        ${p.foto_sertifikat 
                            ? `<img src="uploads/${p.foto_sertifikat}" alt="Foto" class="w-full h-full object-contain" onclick="openLightbox('uploads/${p.foto_sertifikat}')">`
                            : `<i class="fas fa-chalkboard-teacher text-5xl text-white/30"></i>`
                        }
                        <span class="absolute top-2 right-2 ${tingkatColors[p.tingkat]} px-3 py-1 rounded-full text-xs font-bold">
                            ${p.tingkat.toUpperCase()}
                        </span>
                    </div>
                    <div class="p-5">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">
                                ${p.jenis_prestasi?.toUpperCase() || 'PRESTASI'}
                            </span>
                            <span class="bg-accent text-white px-2 py-1 rounded text-xs font-bold">
                                JUARA ${p.peringkat}
                            </span>
                        </div>
                        <h3 class="font-bold text-lg text-gray-800 mb-1">${p.nama_lomba}</h3>
                        <p class="text-blue-600 font-semibold">${p.nama_guru}</p>
                        <p class="text-gray-500 text-sm">${p.mapel || 'Guru'} | ${formatDate(p.tanggal)}</p>
                        <p class="text-gray-600 text-sm mt-2">${p.penyelenggara || ''}</p>
                    </div>
                </div>
            `).join('');
        }

        async function fetchPrestasiSekolah() {
            try {
                const res = await fetch('api/prestasi_sekolah.php');
                const data = await res.json();
                renderPrestasiSekolah(data);
            } catch (e) {
                console.error('Error fetching sekolah prestasi:', e);
                document.getElementById('prestasSekolahGrid').innerHTML = '<div class="col-span-full text-center text-gray-500 py-8"><i class="fas fa-exclamation-triangle text-2xl mb-2"></i><p>Data belum tersedia</p></div>';
            }
        }

        function renderPrestasiSekolah(data) {
            const grid = document.getElementById('prastasSekolahGrid');
            if (!data || data.length === 0) {
                grid.innerHTML = '<div class="col-span-full text-center text-gray-500 py-8"><i class="fas fa-inbox text-4xl mb-2"></i><p>Belum ada data prestasi sekolah</p></div>';
                return;
            }
            
            const tingkatColors = {
                'internasional': 'bg-purple-100 text-purple-800',
                'nasional': 'bg-red-100 text-red-800',
                'provinsi': 'bg-orange-100 text-orange-800',
                'kota': 'bg-blue-100 text-blue-800',
                'kecamatan': 'bg-green-100 text-green-800',
                'sekolah': 'bg-gray-100 text-gray-800'
            };
            
            grid.innerHTML = data.map(p => `
                <div class="bg-white rounded-xl shadow-lg overflow-hidden card-hover transition duration-300">
                    <div class="h-40 bg-gradient-to-br from-purple-500 to-purple-700 flex items-center justify-center relative overflow-hidden">
                        ${p.foto_bukti 
                            ? `<img src="uploads/${p.foto_bukti}" alt="Foto" class="w-full h-full object-contain" onclick="openLightbox('uploads/${p.foto_bukti}')">`
                            : `<i class="fas fa-school text-5xl text-white/30"></i>`
                        }
                        <span class="absolute top-2 right-2 ${tingkatColors[p.tingkat]} px-3 py-1 rounded-full text-xs font-bold">
                            ${p.tingkat.toUpperCase()}
                        </span>
                    </div>
                    <div class="p-5">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded text-xs">
                                ${p.kategori?.toUpperCase() || 'PRESTASI'}
                            </span>
                            <span class="bg-accent text-white px-2 py-1 rounded text-xs font-bold">
                                ${p.peringkat === 'akreditasi' || p.peringkat === 'sertifikasi' ? p.peringkat.toUpperCase() : 'JUARA ' + p.peringkat}
                            </span>
                        </div>
                        <h3 class="font-bold text-lg text-gray-800 mb-1">${p.nama_prestasi}</h3>
                        <p class="text-gray-500 text-sm">${formatDate(p.tanggal)}</p>
                        <p class="text-gray-600 text-sm mt-2">${p.penyelenggara || ''}</p>
                    </div>
                </div>
            `).join('');
        }

        // Close lightbox with Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeLightbox();
            }
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            fetchStats();
            fetchPrestasi(true);
            fetchPrestasiGuru();
            fetchPrestasiSekolah();
            fetchAlumniPTN();
            fetchRekapan();
            loadTahunOptions();
        });

        async function fetchAlumniPTN() {
            try {
                const res = await fetch('api/alumni_ptn.php');
                const data = await res.json();
                renderAlumniPTN(data);
            } catch (e) {
                console.error('Error fetching alumni PTN:', e);
                document.getElementById('alumniGrid').innerHTML = '<div class="col-span-full text-center text-gray-500 py-8"><i class="fas fa-exclamation-triangle text-2xl mb-2"></i><p>Data belum tersedia</p></div>';
            }
        }

        function renderAlumniPTN(data) {
            const grid = document.getElementById('alumniGrid');
            if (!data || data.length === 0) {
                grid.innerHTML = '<div class="col-span-full text-center text-gray-500 py-8"><i class="fas fa-inbox text-4xl mb-2"></i><p>Belum ada data alumni</p></div>';
                return;
            }
            
            const jenisColors = { 'ptn': 'bg-blue-500', 'pts': 'bg-purple-500', 'kerja': 'bg-green-500' };
            const jenisLabels = { 'ptn': 'PTN', 'pts': 'PTS', 'kerja': 'Bekerja' };
            
            grid.innerHTML = data.map(a => {
                const jenis = a.jenis || 'ptn';
                const warna = jenisColors[jenis] || 'bg-gray-500';
                const label = jenisLabels[jenis] || jenis;
                const namaTujuan = jenis === 'kerja' ? (a.nama_perusahaan || '-') : (a.nama_perguruan || '-');
                const subtitle = jenis === 'kerja' ? (a.fakultas || 'Pegawai') : ((a.fakultas || '') + (a.prodi ? ' - ' + a.prodi : ''));
                
                return `
                <div class="bg-white rounded-xl shadow-lg overflow-hidden card-hover transition duration-300">
                    <div class="h-40 bg-gradient-to-br from-teal-500 to-teal-700 flex items-center justify-center relative overflow-hidden">
                        ${a.foto 
                            ? `<img src="uploads/${a.foto}" alt="Foto" class="w-full h-full object-contain" onclick="openLightbox('uploads/${a.foto}')">`
                            : `<i class="fas fa-${jenis === 'kerja' ? 'briefcase' : 'graduation-cap'} text-5xl text-white/30"></i>`
                        }
                        <span class="absolute top-2 right-2 ${warna} px-3 py-1 rounded-full text-xs font-bold text-white">
                            ${label.toUpperCase()}
                        </span>
                    </div>
                    <div class="p-5">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="bg-teal-100 text-teal-800 px-2 py-1 rounded text-xs font-bold">
                                ${a.tahun_ajaran}
                            </span>
                        </div>
                        <h3 class="font-bold text-lg text-gray-800 mb-1">${a.nama_siswa}</h3>
                        <p class="text-teal-600 font-semibold">${namaTujuan}</p>
                        <p class="text-gray-500 text-sm">${subtitle}</p>
                        <p class="text-gray-400 text-sm">${a.kelas}</p>
                    </div>
                </div>
            `}).join('');
        }
    </script>
</body>
</html>
