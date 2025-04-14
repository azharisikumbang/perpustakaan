<?php

namespace Database\Seeders;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\DDC;
use App\Models\Pembayaran;
use App\Models\Peminjaman;
use App\Models\Pengaturan;
use App\Models\Rak;
use App\Models\Role;
use App\Models\User;
use App\Services\HitungKeterlambatanService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // pengaturan 
        $pengaturan = Pengaturan::factory()->create();

        // role 
        Role::factory()->createMany([
            ['name' => Role::ANGGOTA],
            ['name' => Role::ADMINISTRATOR],
            ['name' => Role::KEPALA],
        ]);

        // pengguna
        // administrator
        $admin = User::factory()->create([
            'name' => 'Administrator',
            'email' => 'administrator@demo.test',
            'password' => Hash::make('admin123')
        ]);
        $admin->assignRole(Role::ADMINISTRATOR);

        // kepala
        $kepala = User::factory()->create([
            'name' => 'Kepala',
            'email' => 'kepala@demo.test',
            'password' => Hash::make('kepala123')
        ]);
        $kepala->assignRole(Role::KEPALA);

        // daftar rak
        $listRak = [];
        for ($i = 1; $i <= 27; $i++)
        {
            for ($j = 1; $j <= 4; $j++)
            {
                $listRak[] = [
                    'kode' => sprintf('Lemari %s', $i),
                    'alias' => sprintf('Rak 00%s', $j)
                ];
            }
        }

        Rak::factory()->createMany($listRak);

        // daftar ddc
        $listDDC = $this->daftarDDC();

        DDC::factory()->createMany($listDDC);

        // buku
        $listBukuFromArray = $this->daftarBuku();
        $listBuku = Buku::factory()->createMany($listBukuFromArray);

        User::factory(20)->create()->each(function ($user) {
            $user->anggota()->save(Anggota::factory()->make(['nama' => $user->name]));
            $user->assignRole(Role::ANGGOTA);
        });

        $listAnggota = Anggota::all();

        // peminjaman dikembalikan tepat waktu
        $listAnggota->each(function ($anggota) use ($pengaturan, $listBuku) {
            $peminjaman = $this->generatePeminjaman(
                $pengaturan,
                new \DateTime(),
                (new \DateTime())->modify('+1 days')
            );

            $anggota->peminjaman()->save($peminjaman);

            for ($i = 0; $i < rand(1, 3); $i++)
            {
                $peminjaman->buku()->attach(
                    $listBuku->random(1)->pluck('id')->toArray(),
                    ['jumlah' => rand(1, 3)]
                );
            }
            $this->generatePengembalian($peminjaman, new HitungKeterlambatanService());
        });

        // peminjaman dikembalikan terlambat
        $listAnggota->each(function ($anggota) use ($pengaturan, $listBuku) {
            $peminjaman = $this->generatePeminjaman(
                $pengaturan,
                new \DateTime(sprintf("-%s days", rand(10, 50))),
                new \DateTime()
            );

            $anggota->peminjaman()->save($peminjaman);

            for ($i = 0; $i < rand(1, 3); $i++)
            {
                $peminjaman->buku()->attach(
                    $listBuku->random(1)->pluck('id')->toArray(),
                    ['jumlah' => rand(1, 3)]
                );
            }
            $this->generatePengembalian($peminjaman, new HitungKeterlambatanService());
        });

        // peminjaman belum dikembalikan tapi tidak terlambat
        $listAnggota->each(function ($anggota) use ($pengaturan, $listBuku) {
            $peminjaman = $this->generatePeminjaman(
                $pengaturan,
                new \DateTime()
            );

            $anggota->peminjaman()->save($peminjaman);

            for ($i = 0; $i < rand(1, 3); $i++)
            {
                $peminjaman->buku()->attach(
                    $listBuku->random(1)->pluck('id')->toArray(),
                    ['jumlah' => rand(1, 3)]
                );
            }
            $this->generatePengembalian($peminjaman, new HitungKeterlambatanService());
        });

        // peminjaman belum dikembalikan tapi telah terlambat
        $listAnggota->each(function ($anggota) use ($pengaturan, $listBuku) {
            $peminjaman = $this->generatePeminjaman(
                $pengaturan,
                new \DateTime(sprintf("-%s days", rand(10, 50)))
            );

            $anggota->peminjaman()->save($peminjaman);

            for ($i = 0; $i < rand(1, 3); $i++)
            {
                $peminjaman->buku()->attach(
                    $listBuku->random(1)->pluck('id')->toArray(),
                    ['jumlah' => rand(1, 3)]
                );
            }
            $this->generatePengembalian($peminjaman, new HitungKeterlambatanService());
        });
    }

    private function generatePeminjaman(
        Pengaturan $pengaturan,
        ?\DateTime $tanggal_peminjaman = null,
        ?\DateTime $tanggal_pengembalian = null
    ) {
        $peminjaman = Peminjaman::factory()->make([
            'tanggal_peminjaman' => $tanggal_peminjaman ? $tanggal_peminjaman->format('Y-m-d H:i:s') : date('Y-m-d H:i:s'),
            'lama_peminjaman' => $pengaturan->lama_pinjaman,
            'nominal_denda' => $pengaturan->nominal_denda,
            'tanggal_pengembalian' => $tanggal_pengembalian ? $tanggal_pengembalian->format('Y-m-d H:i:s') : null
        ]);

        return $peminjaman;
    }

    private function generatePengembalian(
        Peminjaman $peminjaman,
        HitungKeterlambatanService $service
    ) {
        if (is_null($peminjaman->tanggal_pengembalian))
            return;

        $keterlambatan = $service->hitung($peminjaman, new \DateTime($peminjaman->tanggal_pengembalian));
        if ($keterlambatan < 1)
            return;

        $pembayaran = Pembayaran::factory()->make([
            'nominal' => $peminjaman->nominal_denda * $keterlambatan
        ]);

        $peminjaman->pembayaran()->save($pembayaran);
    }

    private function daftarBuku(int $limit = 0): array
    {
        $buku = [
            [
                "isbn" => "978-979-107-882-5",
                "judul" => "Akuntansi Pengantar 1",
                "penerbit" => "Gava Media",
                "pengarang" => "Supardi",
                "tahun_terbit" => "2009",
                "stok" => "58",
                "rak_id" => "70",
                "ddc_id" => "1",
                "kode" => "000-91181103"
            ],
            [
                "isbn" => "978-979-328-876-5",
                "judul" => "Aplikasi Klinis Induk Ovulasi & Stimulasi Ovariu",
                "penerbit" => "Sagung Seto",
                "pengarang" => "Samsulhadi",
                "tahun_terbit" => "2013",
                "stok" => "49",
                "rak_id" => "91",
                "ddc_id" => "3",
                "kode" => "200-73441612"
            ],
            [
                "isbn" => "978-602-867-404-1",
                "judul" => "Aplikasi Praktis Asuhan Keperawatan Keluarga",
                "penerbit" => "Sagung Seto",
                "pengarang" => "Komang Ayu Heni",
                "tahun_terbit" => "2012",
                "stok" => "17",
                "rak_id" => "94",
                "ddc_id" => "7",
                "kode" => "600-51034727"
            ],
            [
                "isbn" => "978-979-293-215-7",
                "judul" => "A-Z Psikologi : Berbagai kumpulan topik Psikologi",
                "penerbit" => "Andi Offset",
                "pengarang" => "Zainul Anwar",
                "tahun_terbit" => "2012",
                "stok" => "97",
                "rak_id" => "45",
                "ddc_id" => "6",
                "kode" => "500-93821035"
            ],
            [
                "isbn" => "978-979-128-365-6",
                "judul" => "Bangsa gagal ; Mencari identitas kebangsaan",
                "penerbit" => "LKiS",
                "pengarang" => "Nasruddin Anshoriy",
                "tahun_terbit" => "2008",
                "stok" => "19",
                "rak_id" => "6",
                "ddc_id" => "10",
                "kode" => "900-68762607"
            ],
            [
                "isbn" => "978-979-338-125-1",
                "judul" => "Biografi Gus Dur ; The Authorized Biography of KH. Abdurrahman Wahid (Soft Cover)",
                "penerbit" => "LKiS",
                "pengarang" => "Greg Barton",
                "tahun_terbit" => "2011",
                "stok" => "88",
                "rak_id" => "99",
                "ddc_id" => "8",
                "kode" => "700-86185787"
            ],
            [
                "isbn" => "979-328-808-6",
                "judul" => "Buku Ajar Tumbuh Kembang Remaja & Permasalahanya",
                "penerbit" => "Sagung Seto",
                "pengarang" => "Soetjiningsih",
                "tahun_terbit" => "2004",
                "stok" => "22",
                "rak_id" => "7",
                "ddc_id" => "4",
                "kode" => "300-18822164"
            ],
            [
                "isbn" => "978-602-867-497-3",
                "judul" => "Cedera Kepala",
                "penerbit" => "Sagung Seto",
                "pengarang" => "M. Z. Arifin",
                "tahun_terbit" => "2013",
                "stok" => "93",
                "rak_id" => "59",
                "ddc_id" => "10",
                "kode" => "900-92399238"
            ],
            [
                "isbn" => "978-602-867-451-5",
                "judul" => "Dasar Dasar Uroginekologi",
                "penerbit" => "Sagung Seto",
                "pengarang" => "Pribakti B",
                "tahun_terbit" => "2011",
                "stok" => "85",
                "rak_id" => "20",
                "ddc_id" => "8",
                "kode" => "700-65311989"
            ],
            [
                "isbn" => "978-602-728-136-3",
                "judul" => "Etnografi Pengobatan; Praktik Budaya peramuan & sugesti komunitas adat Tau Taa Vana",
                "penerbit" => "LKiS",
                "pengarang" => "Alie Humaedi",
                "tahun_terbit" => "2016",
                "stok" => "93",
                "rak_id" => "45",
                "ddc_id" => "4",
                "kode" => "300-74596382"
            ],
            [
                "isbn" => "979-896-678-3",
                "judul" => "Hantu Digoel; Politik Pengamanan Politik Zaman Kolonial",
                "penerbit" => "LKiS",
                "pengarang" => "Takashi Shiraishi",
                "tahun_terbit" => "2001",
                "stok" => "100",
                "rak_id" => "85",
                "ddc_id" => "7",
                "kode" => "600-77499372"
            ],
            [
                "isbn" => "979-338-171-X",
                "judul" => "Islam Agama ramah perempuan; Pembelaan kiai pesantren",
                "penerbit" => "LKiS",
                "pengarang" => "Husein Muhammad",
                "tahun_terbit" => "2013",
                "stok" => "91",
                "rak_id" => "14",
                "ddc_id" => "10",
                "kode" => "900-45328223"
            ],
            [
                "isbn" => "979-896-636-8",
                "judul" => "Islam Jawa; Kesalehan Normatif Versus Kebatinan",
                "penerbit" => "LKiS",
                "pengarang" => "Mark R. Woodward",
                "tahun_terbit" => "2004",
                "stok" => "58",
                "rak_id" => "9",
                "ddc_id" => "8",
                "kode" => "700-11941800"
            ],
            [
                "isbn" => "979-896-679-1",
                "judul" => "Islam Pasar Keadilan; Artikulasi Lokal, Kapitalisme, dan Demokrasi",
                "penerbit" => "LKiS",
                "pengarang" => "Robert W. Hefner",
                "tahun_terbit" => "2013",
                "stok" => "21",
                "rak_id" => "104",
                "ddc_id" => "6",
                "kode" => "500-36325997"
            ],
            [
                "isbn" => "979-896-651-1",
                "judul" => "Islam Sasak ; Wetu telu versus waktu lima",
                "penerbit" => "LKiS",
                "pengarang" => "Erni Budiwanti",
                "tahun_terbit" => "2013",
                "stok" => "83",
                "rak_id" => "102",
                "ddc_id" => "4",
                "kode" => "300-97609947"
            ],
            [
                "isbn" => "978-602-867-468-3",
                "judul" => "Keanekaragaman klinik peny. Trofoblas gestasional",
                "penerbit" => "Sagung Seto",
                "pengarang" => "Djamhoer M",
                "tahun_terbit" => "2011",
                "stok" => "98",
                "rak_id" => "51",
                "ddc_id" => "5",
                "kode" => "400-53135098"
            ],
            [
                "isbn" => "978-979-128-355-7",
                "judul" => "Kesadaran Nasional ; dari kolonialisme sampai kemerdekaan (jilid 1)",
                "penerbit" => "LKiS",
                "pengarang" => "Slamet Muljana",
                "tahun_terbit" => "2008",
                "stok" => "87",
                "rak_id" => "27",
                "ddc_id" => "6",
                "kode" => "500-91028268"
            ],
            [
                "isbn" => "978-979-128-357-1",
                "judul" => "Kesadaran Nasional ; dari kolonialisme sampai kemerdekaan (jilid 2)",
                "penerbit" => "LKiS",
                "pengarang" => "Slamet Muljana",
                "tahun_terbit" => "2008",
                "stok" => "58",
                "rak_id" => "60",
                "ddc_id" => "10",
                "kode" => "900-27718693"
            ],
            [
                "isbn" => "978-979-769-600-9",
                "judul" => "Kesehjateraan Sosial",
                "penerbit" => "Rajagrafindo Persada",
                "pengarang" => "Isbandi Rukminto Adi",
                "tahun_terbit" => "2015",
                "stok" => "25",
                "rak_id" => "39",
                "ddc_id" => "6",
                "kode" => "500-96937829"
            ],
            [
                "isbn" => "978-979-294-344-3",
                "judul" => "Kolaborasi PHP 5 dan Mysql untuk pengembangan website + cd",
                "penerbit" => "Andi Offset",
                "pengarang" => "Eko Priyo Utomo",
                "tahun_terbit" => "2014",
                "stok" => "94",
                "rak_id" => "77",
                "ddc_id" => "6",
                "kode" => "500-20787905"
            ],
            [
                "isbn" => "978-979-255-344-4",
                "judul" => "Kontroversi Hakim Perempuan Pada Peradilan Islam di Negara Negara Muslim",
                "penerbit" => "LKiS",
                "pengarang" => "Djamizah Muqoddas",
                "tahun_terbit" => "2011",
                "stok" => "50",
                "rak_id" => "98",
                "ddc_id" => "6",
                "kode" => "500-14418509"
            ],
            [
                "isbn" => "979-338-159-0",
                "judul" => "Kuasa Wanita Jawa",
                "penerbit" => "LKiS",
                "pengarang" => "Christina S Handayani",
                "tahun_terbit" => "2011",
                "stok" => "12",
                "rak_id" => "38",
                "ddc_id" => "5",
                "kode" => "400-80568581"
            ],
            [
                "isbn" => "979-290-349-2",
                "judul" => "Kumpulan Undang undang Sistem peradilan Pidana",
                "penerbit" => "Andi Offset",
                "pengarang" => "Lincon Arsyad",
                "tahun_terbit" => "2007",
                "stok" => "33",
                "rak_id" => "48",
                "ddc_id" => "8",
                "kode" => "700-23607709"
            ],
            [
                "isbn" => "978-979-295-172-1",
                "judul" => "Langsung Praktik Komputerisasi Akuntansi Dengan MYOB",
                "penerbit" => "Andi Offset",
                "pengarang" => "Wahana Komputer",
                "tahun_terbit" => "2015",
                "stok" => "62",
                "rak_id" => "96",
                "ddc_id" => "1",
                "kode" => "000-40919218"
            ],
            [
                "isbn" => "978-602-873-012-9",
                "judul" => "Lembaga keuangan Islam",
                "penerbit" => "PRENADA MEDIA GRUP",
                "pengarang" => "Nurul Huda",
                "tahun_terbit" => "2015",
                "stok" => "56",
                "rak_id" => "61",
                "ddc_id" => "3",
                "kode" => "200-78652686"
            ],
            [
                "isbn" => "978-979-128-303-8",
                "judul" => "Makna Budaya Dalam Komunikasi Antar Budaya",
                "penerbit" => "LKiS",
                "pengarang" => "Alo Liliweri",
                "tahun_terbit" => "2009",
                "stok" => "78",
                "rak_id" => "71",
                "ddc_id" => "3",
                "kode" => "200-88598907"
            ],
            [
                "isbn" => "978-602-867-471-3",
                "judul" => "Manajemen Penerbitan Jurnal Ilmiah",
                "penerbit" => "Sagung Seto",
                "pengarang" => "Lukman S",
                "tahun_terbit" => "2012",
                "stok" => "54",
                "rak_id" => "76",
                "ddc_id" => "8",
                "kode" => "700-96739376"
            ],
            [
                "isbn" => "979-845-135-X",
                "judul" => "Menuju Puncak Kemegahan; Sejarah kerajaan Majapahit",
                "penerbit" => "LKiS",
                "pengarang" => "Slamet Muljana",
                "tahun_terbit" => "2012",
                "stok" => "17",
                "rak_id" => "45",
                "ddc_id" => "1",
                "kode" => "000-84738019"
            ],
            [
                "isbn" => "978-979-290-742-1",
                "judul" => "Metode Riset Bisnis Edisi II",
                "penerbit" => "Andi Offset",
                "pengarang" => "Suliyanto",
                "tahun_terbit" => "2009",
                "stok" => "93",
                "rak_id" => "100",
                "ddc_id" => "6",
                "kode" => "500-78419450"
            ],
            [
                "isbn" => "978-602-271-037-0",
                "judul" => "Metodologi Penelitian Bidang Kesehatan ED. 2",
                "penerbit" => "Sagung Seto",
                "pengarang" => "Moch. Imron TA",
                "tahun_terbit" => "2014",
                "stok" => "35",
                "rak_id" => "18",
                "ddc_id" => "9",
                "kode" => "800-48822260"
            ],
            [
                "isbn" => "978-979-128-367-0",
                "judul" => "Neo Patriotisme; Etika kekuasaan dalam kebdayaan jawa",
                "penerbit" => "LKiS",
                "pengarang" => "Nasruddin Anshoriy",
                "tahun_terbit" => "2008",
                "stok" => "54",
                "rak_id" => "69",
                "ddc_id" => "3",
                "kode" => "200-95567243"
            ],
            [
                "isbn" => "979-896-633-3",
                "judul" => "NU vis a vis Negara; Pencarian isi, bentuk dan makna",
                "penerbit" => "LKiS",
                "pengarang" => "Andree Feillard",
                "tahun_terbit" => "2013",
                "stok" => "14",
                "rak_id" => "16",
                "ddc_id" => "6",
                "kode" => "500-86312260"
            ],
            [
                "isbn" => "978-602-149-135-5",
                "judul" => "Otoritarianisme Hukum Islam ; Kritik atas hirearki teks al-kutub as-sittah",
                "penerbit" => "LKiS",
                "pengarang" => "Muhammad Habibi Siregar",
                "tahun_terbit" => "2014",
                "stok" => "36",
                "rak_id" => "52",
                "ddc_id" => "10",
                "kode" => "900-77052336"
            ],
            [
                "isbn" => "978-979-254-840-2",
                "judul" => "Otoritas Sunnah Non-Tasyri’iyyah menurut Yusuf Al-Qaradhawi",
                "penerbit" => "Ar-Ruzz Media",
                "pengarang" => "Tarmizi M. Jakfar",
                "tahun_terbit" => "2016",
                "stok" => "29",
                "rak_id" => "32",
                "ddc_id" => "7",
                "kode" => "600-99709401"
            ],
            [
                "isbn" => "978-602-867-417-1",
                "judul" => "Panduan Penatalaksanaan Kanker Solid Peraboi 2010",
                "penerbit" => "Sagung Seto",
                "pengarang" => "Tjakra Wibawa",
                "tahun_terbit" => "2010",
                "stok" => "47",
                "rak_id" => "78",
                "ddc_id" => "7",
                "kode" => "600-34585227"
            ],
            [
                "isbn" => "979-328-803-5",
                "judul" => "Patologi I (umum)",
                "penerbit" => "Sagung Seto",
                "pengarang" => "Sudarto Pringgoutomo",
                "tahun_terbit" => "2002",
                "stok" => "54",
                "rak_id" => "78",
                "ddc_id" => "8",
                "kode" => "700-27885672"
            ],
            [
                "isbn" => "979-328-803-5",
                "judul" => "Patologi Sosial I",
                "penerbit" => "Sagung Seto",
                "pengarang" => "Kartini Kartono",
                "tahun_terbit" => "2002",
                "stok" => "85",
                "rak_id" => "3",
                "ddc_id" => "7",
                "kode" => "600-16344486"
            ],
            [
                "isbn" => "978-979-254-856-3",
                "judul" => "Pengantar Cultural Studies : Sejarah, Pendekatan, Konseptual, & Isu Menuju Studi Budaya Kapitalisme lanjut",
                "penerbit" => "Ar-Ruzz Media",
                "pengarang" => "Sandi Suwardi Hasan",
                "tahun_terbit" => "2016",
                "stok" => "53",
                "rak_id" => "24",
                "ddc_id" => "2",
                "kode" => "100-53131199"
            ],
            [
                "isbn" => "978-979-255-380-2",
                "judul" => "Pengantar Studi Al-Quran : Teori dan Pendekatan",
                "penerbit" => "LKiS",
                "pengarang" => "Munzir Hitami",
                "tahun_terbit" => "2012",
                "stok" => "92",
                "rak_id" => "25",
                "ddc_id" => "8",
                "kode" => "700-78170565"
            ],
            [
                "isbn" => "979-949-233-5",
                "judul" => "Politik Media dan Pertarungan Wacana",
                "penerbit" => "LKiS",
                "pengarang" => "Agus Sudibyo",
                "tahun_terbit" => "2013",
                "stok" => "62",
                "rak_id" => "34",
                "ddc_id" => "8",
                "kode" => "700-40501528"
            ],
            [
                "isbn" => "978-979-293-499-1",
                "judul" => "Quick Reference Windows 8",
                "penerbit" => "Andi Offset",
                "pengarang" => "Wahana Komputer",
                "tahun_terbit" => "2013",
                "stok" => "91",
                "rak_id" => "31",
                "ddc_id" => "6",
                "kode" => "500-74323892"
            ],
            [
                "isbn" => "978-979-128-361-8",
                "judul" => "Rekam Jejak; Dokter pejuang & Pelopor kebangkitan Nasional",
                "penerbit" => "LKiS",
                "pengarang" => "Nasruddin Anshoriy",
                "tahun_terbit" => "2008",
                "stok" => "23",
                "rak_id" => "48",
                "ddc_id" => "8",
                "kode" => "700-21036491"
            ],
            [
                "isbn" => "979-845-116-3",
                "judul" => "Runtuhnya Kerajaan Hindu Jawa",
                "penerbit" => "LKiS",
                "pengarang" => "Slamet Muljana",
                "tahun_terbit" => "2007",
                "stok" => "45",
                "rak_id" => "4",
                "ddc_id" => "4",
                "kode" => "300-30841739"
            ],
            [
                "isbn" => "978-979-254-823-5",
                "judul" => "Sejarah Pendidikan Nasional : Dari masa klasik hingga modern",
                "penerbit" => "Ar-Ruzz Media",
                "pengarang" => "Muhammad Rifa’i",
                "tahun_terbit" => "2016",
                "stok" => "86",
                "rak_id" => "29",
                "ddc_id" => "8",
                "kode" => "700-95379839"
            ],
            [
                "isbn" => "978-979-128-394-6",
                "judul" => "Serangan Umum 1 Maret 1949 dalam keleidoskop Sejarah Perjuangan Mempertahankan Kemerdekaan Indonesia",
                "penerbit" => "LKiS",
                "pengarang" => "Batara R. Hutagalung",
                "tahun_terbit" => "2010",
                "stok" => "21",
                "rak_id" => "76",
                "ddc_id" => "5",
                "kode" => "400-52593522"
            ],
            [
                "isbn" => "978-979-107-893-1",
                "judul" => "Statistik Sosial; Teori dan aplikasi Program SPSS",
                "penerbit" => "Gava Media",
                "pengarang" => "Nanang Martono",
                "tahun_terbit" => "2010",
                "stok" => "85",
                "rak_id" => "41",
                "ddc_id" => "4",
                "kode" => "300-40695864"
            ],
            [
                "isbn" => "978-602-867-485-0",
                "judul" => "Step by Step Penanganan Kelainan Endokrinologi",
                "penerbit" => "Sagung Seto",
                "pengarang" => "Tono Djuwantono",
                "tahun_terbit" => "2012",
                "stok" => "42",
                "rak_id" => "28",
                "ddc_id" => "6",
                "kode" => "500-27663923"
            ],
            [
                "isbn" => "979-346-964-1",
                "judul" => "Strategi Bahasa Assembler + CD",
                "penerbit" => "Gava Media",
                "pengarang" => "Jason P",
                "tahun_terbit" => "2005",
                "stok" => "75",
                "rak_id" => "88",
                "ddc_id" => "10",
                "kode" => "900-37590023"
            ],
            [
                "isbn" => "978-979-769-632-0",
                "judul" => "Strategic Management",
                "penerbit" => "Rajagrafindo Persada",
                "pengarang" => "Sofjan Assauri",
                "tahun_terbit" => "2016",
                "stok" => "70",
                "rak_id" => "87",
                "ddc_id" => "6",
                "kode" => "500-75544266"
            ],
            [
                "isbn" => "978-602-080-901-4",
                "judul" => "Studi Filsafat 1 : Pembacaan atas tradisi islam kontemporer",
                "penerbit" => "LKiS",
                "pengarang" => "Hassan Hanafi",
                "tahun_terbit" => "2013",
                "stok" => "66",
                "rak_id" => "71",
                "ddc_id" => "6",
                "kode" => "500-33736179"
            ],
            [
                "isbn" => "978-979-254-991-9",
                "judul" => "Tan Malaka: Merajut Masyarakat dan pendidikan Indonesia yang Sosialistis",
                "penerbit" => "Ar-Ruzz Media",
                "pengarang" => "Syaifudin",
                "tahun_terbit" => "2016",
                "stok" => "36",
                "rak_id" => "20",
                "ddc_id" => "10",
                "kode" => "900-59366829"
            ],
            [
                "isbn" => "978-979-167-765-3",
                "judul" => "Tarekat Petani : Fenomena Tarekat Syattariyah Lokal",
                "penerbit" => "LKiS",
                "pengarang" => "Nur Syam",
                "tahun_terbit" => "2013",
                "stok" => "41",
                "rak_id" => "44",
                "ddc_id" => "8",
                "kode" => "700-47258068"
            ],
            [
                "isbn" => "978-979-291-640-9",
                "judul" => "Tata Boga Industri : Materi Kompetensi Untuk SMK, LPK Pariwisata, & LPK Kapal Pesiar yang siap kerja",
                "penerbit" => "Andi Offset",
                "pengarang" => "Bartono",
                "tahun_terbit" => "2010",
                "stok" => "77",
                "rak_id" => "60",
                "ddc_id" => "9",
                "kode" => "800-96408068"
            ],
            [
                "isbn" => "979-255-359-2",
                "judul" => "Teks Otoritas Kebenaran",
                "penerbit" => "LKiS",
                "pengarang" => "Nasr Hamid Abu Zaid",
                "tahun_terbit" => "2012",
                "stok" => "37",
                "rak_id" => "8",
                "ddc_id" => "5",
                "kode" => "400-99324482"
            ],
            [
                "isbn" => "979-338-106-x",
                "judul" => "Teologi Seksual",
                "penerbit" => "LKiS",
                "pengarang" => "Geoffrey Parrinder",
                "tahun_terbit" => "2004",
                "stok" => "46",
                "rak_id" => "40",
                "ddc_id" => "9",
                "kode" => "800-78682134"
            ],
            [
                "isbn" => "979-346-916-1",
                "judul" => "Belajar mikrokontroler AT89C51/52/55",
                "penerbit" => "Gava Media",
                "pengarang" => "Agfianto EP",
                "tahun_terbit" => "2010",
                "stok" => "19",
                "rak_id" => "26",
                "ddc_id" => "10",
                "kode" => "900-13606796"
            ],
            [
                "isbn" => "978-979-294-694-9",
                "judul" => "Shortcourse RPG Maker VX ACE",
                "penerbit" => "Andi Offset",
                "pengarang" => "Wahana Komputer",
                "tahun_terbit" => "2014",
                "stok" => "90",
                "rak_id" => "4",
                "ddc_id" => "7",
                "kode" => "600-27785456"
            ],
            [
                "isbn" => "979-255-247-2",
                "judul" => "Transnasionalisasi Masyarakat Sipil",
                "penerbit" => "LKiS",
                "pengarang" => "Andi Widjajanto",
                "tahun_terbit" => "2006",
                "stok" => "28",
                "rak_id" => "38",
                "ddc_id" => "3",
                "kode" => "200-22438255"
            ],
            [
                "isbn" => "978-979-107-870-2",
                "judul" => "Tuntunan Praktis : Pengembangan Aplikasi Sistem Informasi Geografis berbasis Dekstop dan Web + CD",
                "penerbit" => "Gava Media",
                "pengarang" => "Riyanto",
                "tahun_terbit" => "2009",
                "stok" => "21",
                "rak_id" => "69",
                "ddc_id" => "5",
                "kode" => "400-30440171"
            ],
            [
                "isbn" => "978-979-294-131-9",
                "judul" => "Web Programing Membangun Aplikasi Web Handal dengan J2EE dan MVC",
                "penerbit" => "Andi Offset",
                "pengarang" => "Widodo Budiharto",
                "tahun_terbit" => "2013",
                "stok" => "85",
                "rak_id" => "85",
                "ddc_id" => "6",
                "kode" => "500-15849275"
            ],
            [
                "isbn" => "978-979-128-398-4",
                "judul" => "Ajeg Bali : Gerakan, Identitas Kultural, dan Globalisasi",
                "penerbit" => "LKIS",
                "pengarang" => "Prof. Dr. Nengah Bawa Atmadja, MA",
                "tahun_terbit" => "2013",
                "stok" => "60",
                "rak_id" => "46",
                "ddc_id" => "8",
                "kode" => "700-29258603"
            ],
            [
                "isbn" => "978-979-107-853-5",
                "judul" => "Aplikasi Program Akutansi dengan Visual FoxPro 9.0 Aplikasi Penjualan, Pembelian dan Stok",
                "penerbit" => "Gava Media",
                "pengarang" => "Peter Wanto",
                "tahun_terbit" => "2008",
                "stok" => "52",
                "rak_id" => "76",
                "ddc_id" => "4",
                "kode" => "300-52229407"
            ],
            [
                "isbn" => "978-979-294-169-2",
                "judul" => "Aura dan Rinupa, Berdialog dengan Kayu, Bambu dan Batu.",
                "penerbit" => "Andi Offset",
                "pengarang" => "Christina Johanes, Kristina, Maxy, Priyo",
                "tahun_terbit" => "2014",
                "stok" => "13",
                "rak_id" => "44",
                "ddc_id" => "7",
                "kode" => "600-56070247"
            ],
            [
                "isbn" => "979-815-023-6",
                "judul" => "Buku Acuan Nasional Onkologi Ginekologi",
                "penerbit" => "YBP-SP (TRIDASA)",
                "pengarang" => "Farid – Farid",
                "tahun_terbit" => "2010",
                "stok" => "68",
                "rak_id" => "64",
                "ddc_id" => "5",
                "kode" => "400-32596432"
            ],
            [
                "isbn" => "978-979-842-146-4",
                "judul" => "Buku Ajar Gastroenterologi-Hipatology Thn 2016",
                "penerbit" => "IDA!",
                "pengarang" => "M.Juffrie – M.Juffrie",
                "tahun_terbit" => "2015",
                "stok" => "83",
                "rak_id" => "85",
                "ddc_id" => "10",
                "kode" => "900-74776972"
            ],
            [
                "isbn" => "978-979-293-544-8",
                "judul" => "Buku Pintar Pengelolaan BPR dan Lembaga Keuangan Pembiayaan Mikro",
                "penerbit" => "Andi Offset",
                "pengarang" => "Ali SuyantoHerli",
                "tahun_terbit" => "2013",
                "stok" => "73",
                "rak_id" => "39",
                "ddc_id" => "9",
                "kode" => "800-27853761"
            ],
            [
                "isbn" => "978-979-294-016-9",
                "judul" => "Busines And Personal Development",
                "penerbit" => "Andi Offset",
                "pengarang" => "Josua Taringan dan Swenjiadi Yenawan",
                "tahun_terbit" => "2013",
                "stok" => "15",
                "rak_id" => "72",
                "ddc_id" => "4",
                "kode" => "300-79599848"
            ],
            [
                "isbn" => "979-533-956-7",
                "judul" => "Cara pemeriksaan, Penyetelan dan Perawatan Kelistrikan Mobil",
                "penerbit" => "Andi Offset",
                "pengarang" => "Boentarto",
                "tahun_terbit" => "1995",
                "stok" => "77",
                "rak_id" => "60",
                "ddc_id" => "8",
                "kode" => "700-76668743"
            ],
            [
                "isbn" => "979-442-350-5",
                "judul" => "DAFTAR TAJUK SUBYEK DALAM BAHASA INDONESIA",
                "penerbit" => "SAGUNG SETO",
                "pengarang" => "SULISTYO-BASUKI",
                "tahun_terbit" => "2012",
                "stok" => "16",
                "rak_id" => "52",
                "ddc_id" => "1",
                "kode" => "000-30811724"
            ],
            [
                "isbn" => "978-602-187-851-4",
                "judul" => "DESAIN PEMBELAJARAN BERBASIS PENDIDIKAN KARAKTER",
                "penerbit" => "Ar- Ruzz Media",
                "pengarang" => "Asmaun Sahlan & Angga Teguh Prastyo",
                "tahun_terbit" => "2016",
                "stok" => "31",
                "rak_id" => "5",
                "ddc_id" => "9",
                "kode" => "800-76980309"
            ],
            [
                "isbn" => "978-602-135-310-3",
                "judul" => "Dunia Lebih Indah Tanpa Sekolah",
                "penerbit" => "MITRA WACANA",
                "pengarang" => "Nanang Martono",
                "tahun_terbit" => "2014",
                "stok" => "93",
                "rak_id" => "100",
                "ddc_id" => "5",
                "kode" => "400-73058101"
            ],
            [
                "isbn" => "978-602-873-097-6",
                "judul" => "Fiqh Ekonomi Syariah (Fiqh Muamalah)",
                "penerbit" => "Kencana",
                "pengarang" => "DR. Mardani",
                "tahun_terbit" => "2013",
                "stok" => "63",
                "rak_id" => "37",
                "ddc_id" => "8",
                "kode" => "700-33693701"
            ],
            [
                "isbn" => "978-979-290-403-1",
                "judul" => "Himpunan Undang-Undang Hak Kekayaan Intelektual",
                "penerbit" => "Andi Offset",
                "pengarang" => "Massudilawe & Partner",
                "tahun_terbit" => "2008",
                "stok" => "36",
                "rak_id" => "65",
                "ddc_id" => "1",
                "kode" => "000-91839621"
            ],
            [
                "isbn" => "978-602-873-098-3",
                "judul" => "Hukum Agraria Kajian Komprehensif",
                "penerbit" => "Kencana",
                "pengarang" => "Dr. Urip Santoso, S.H., M.H",
                "tahun_terbit" => "2012",
                "stok" => "74",
                "rak_id" => "84",
                "ddc_id" => "3",
                "kode" => "200-57268953"
            ],
            [
                "isbn" => "978-979-346-559-X",
                "judul" => "Ilmu Dakwah",
                "penerbit" => "Kencana",
                "pengarang" => "Dr. Moh. Ali Aziz, M.AG",
                "tahun_terbit" => "2016",
                "stok" => "77",
                "rak_id" => "76",
                "ddc_id" => "3",
                "kode" => "200-31137283"
            ],
            [
                "isbn" => "978-979-292-342-1",
                "judul" => "IMS (IP Multimedia Subsystem), Framework Dan Arsitektur Jaringan Telekomunikasi Masa Depan",
                "penerbit" => "Andi Offset",
                "pengarang" => "M. Azwir",
                "tahun_terbit" => "2014",
                "stok" => "96",
                "rak_id" => "11",
                "ddc_id" => "1",
                "kode" => "000-89815965"
            ],
            [
                "isbn" => "979-896-682-1",
                "judul" => "Islam dan Permasalahan Sosial",
                "penerbit" => "LKiS",
                "pengarang" => "Dr. A. Qodri A Azizy",
                "tahun_terbit" => "2013",
                "stok" => "36",
                "rak_id" => "80",
                "ddc_id" => "9",
                "kode" => "800-64483575"
            ],
            [
                "isbn" => "978-979-845-114-0",
                "judul" => "Islam Pesisir",
                "penerbit" => "LKiS",
                "pengarang" => "Dr. Nur Syam",
                "tahun_terbit" => "2011",
                "stok" => "47",
                "rak_id" => "98",
                "ddc_id" => "5",
                "kode" => "400-88632677"
            ],
            [
                "isbn" => "978-979-294-506-5",
                "judul" => "Kupas Tuntas : Microsoft Windows 8.1",
                "penerbit" => "Andi Offset",
                "pengarang" => "Madcoms",
                "tahun_terbit" => "2014",
                "stok" => "45",
                "rak_id" => "74",
                "ddc_id" => "2",
                "kode" => "100-67487654"
            ],
            [
                "isbn" => "978-979-291-769-7",
                "judul" => "Langkah Mudah Belajar Kalkulus For IT(Khusus P. Jawa)",
                "penerbit" => "Andi Offset",
                "pengarang" => "Sudaryono, Untung Rahardja, Edi S. Mulyanta",
                "tahun_terbit" => "2012",
                "stok" => "14",
                "rak_id" => "38",
                "ddc_id" => "7",
                "kode" => "600-21523265"
            ],
            [
                "isbn" => "978-602-752-348-7",
                "judul" => "Manajemen Penanganan Barang-Barang Berbahaya Pada Angkatan Udara",
                "penerbit" => "MITRA WACANA",
                "pengarang" => "Wynd Riyaldi & M Rifni",
                "tahun_terbit" => "2013",
                "stok" => "100",
                "rak_id" => "23",
                "ddc_id" => "8",
                "kode" => "700-56558105"
            ],
            [
                "isbn" => "978-979-225-331-4",
                "judul" => "MATA AIR PERADABAN ; Dua Milenium Wonosobo",
                "penerbit" => "LKiS",
                "pengarang" => "H.A. Kholiq Arif",
                "tahun_terbit" => "2010",
                "stok" => "39",
                "rak_id" => "92",
                "ddc_id" => "7",
                "kode" => "600-81565606"
            ],
            [
                "isbn" => "978-602-752-391-3",
                "judul" => "Membuat Aplikasi Sistem Aplikasi Menggunakan VB.NET",
                "penerbit" => "MITRA WACANA",
                "pengarang" => "Yulius Ekaq Agung Saputro",
                "tahun_terbit" => "2013",
                "stok" => "94",
                "rak_id" => "72",
                "ddc_id" => "9",
                "kode" => "800-69405057"
            ],
            [
                "isbn" => "978-979-128-322-9",
                "judul" => "Memuja Mantra ; Sabuk Mangir dan Jaran Goyang Masyarakat Suku Using Banyuwangi",
                "penerbit" => "LKiS",
                "pengarang" => "Heru S.P. Saputra",
                "tahun_terbit" => "2007",
                "stok" => "22",
                "rak_id" => "61",
                "ddc_id" => "10",
                "kode" => "900-65023187"
            ],
            [
                "isbn" => "978-602-118-605-3",
                "judul" => "Mendesain Model Pembelajaran Inovatif Progresif dan Kontekstual",
                "penerbit" => "Kencana",
                "pengarang" => "Trianto, M.Pd",
                "tahun_terbit" => "2015",
                "stok" => "32",
                "rak_id" => "23",
                "ddc_id" => "6",
                "kode" => "500-92623731"
            ],
            [
                "isbn" => "978-979-255-321-5",
                "judul" => "Menulis Itu Mudah ; Panduan Praktis Menjadi Penulis Handal",
                "penerbit" => "LKiS",
                "pengarang" => "Sukino",
                "tahun_terbit" => "2010",
                "stok" => "93",
                "rak_id" => "65",
                "ddc_id" => "8",
                "kode" => "700-56326956"
            ],
            [
                "isbn" => "978-979-392-588-2",
                "judul" => "Penelitian Kualitatif",
                "penerbit" => "PRENADA MEDIA GRUP",
                "pengarang" => "Burhan Bungin",
                "tahun_terbit" => "2007",
                "stok" => "63",
                "rak_id" => "15",
                "ddc_id" => "8",
                "kode" => "700-74378010"
            ],
            [
                "isbn" => "979-338-134-5",
                "judul" => "Mistisisme Hindu Muslim",
                "penerbit" => "LKiS",
                "pengarang" => "R.C Zaehner",
                "tahun_terbit" => "2016",
                "stok" => "49",
                "rak_id" => "72",
                "ddc_id" => "9",
                "kode" => "800-55368934"
            ],
            [
                "isbn" => "978-979-845-160-7",
                "judul" => "Nietzsche",
                "penerbit" => "LKiS",
                "pengarang" => "St. Sunardi",
                "tahun_terbit" => "2011",
                "stok" => "41",
                "rak_id" => "20",
                "ddc_id" => "2",
                "kode" => "100-52799264"
            ],
            [
                "isbn" => "978-979-295-094-6",
                "judul" => "Panduan Aplikasi dan Solusi: Pemodelan Obyek Dengan 3ds Max 2014",
                "penerbit" => "Andi Offset",
                "pengarang" => "Wahana Komputer",
                "tahun_terbit" => "2014",
                "stok" => "30",
                "rak_id" => "86",
                "ddc_id" => "6",
                "kode" => "500-23745911"
            ],
            [
                "isbn" => "978-979-295-109-7",
                "judul" => "Panduan Praktis Sistem Kendali Digital",
                "penerbit" => "Andi Offset",
                "pengarang" => "Azwardi Dan Cekmas Cekdin",
                "tahun_terbit" => "2015",
                "stok" => "24",
                "rak_id" => "48",
                "ddc_id" => "7",
                "kode" => "600-39454442"
            ],
            [
                "isbn" => "978-979-128-392-2",
                "judul" => "Pantun Melayu : Titik Temu Islam dan Budaya Lokal Nusantara",
                "penerbit" => "LKiS",
                "pengarang" => "Abd. Rachman Abror",
                "tahun_terbit" => "2009",
                "stok" => "15",
                "rak_id" => "83",
                "ddc_id" => "8",
                "kode" => "700-74287473"
            ],
            [
                "isbn" => "978-602-786-966-0",
                "judul" => "Pemrograman Web Membuat Sistem Informasi Akademik Sekolah Dengan PHP-MYSQL & Dreamweaver",
                "penerbit" => "Gava Media",
                "pengarang" => "Bunafit Nugroho",
                "tahun_terbit" => "2014",
                "stok" => "28",
                "rak_id" => "85",
                "ddc_id" => "7",
                "kode" => "600-26474837"
            ],
            [
                "isbn" => "978-979-167-767-7",
                "judul" => "PENDIDIKAN PASCAKONFLIK ; Pendidikan Multikultural Berbasis Konseling Budaya Masyarakat Maluku Utara",
                "penerbit" => "LKiS",
                "pengarang" => "Dr. M. Tahir Sapsuha",
                "tahun_terbit" => "2013",
                "stok" => "13",
                "rak_id" => "93",
                "ddc_id" => "8",
                "kode" => "700-74299789"
            ],
            [
                "isbn" => "978-979-346-982-X",
                "judul" => "Pengantar Membuat Robot",
                "penerbit" => "Gava Media",
                "pengarang" => "M. Ibnu Malik",
                "tahun_terbit" => "2006",
                "stok" => "93",
                "rak_id" => "53",
                "ddc_id" => "1",
                "kode" => "000-86116305"
            ],
            [
                "isbn" => "978-979-294-249-1",
                "judul" => "Pengantar Teknologi Informasi",
                "penerbit" => "Andi Offset",
                "pengarang" => "Tata Sutabri",
                "tahun_terbit" => "2014",
                "stok" => "95",
                "rak_id" => "45",
                "ddc_id" => "7",
                "kode" => "600-95326315"
            ],
            [
                "isbn" => "979-970-886-9",
                "judul" => "Pengenalan Dasar-Dasar PLC (Progamable Logic Controler) Disertai Contoh Aplikasinya",
                "penerbit" => "Gava Media",
                "pengarang" => "M. Budiyanto",
                "tahun_terbit" => "2006",
                "stok" => "99",
                "rak_id" => "101",
                "ddc_id" => "6",
                "kode" => "500-46811653"
            ],
            [
                "isbn" => "978-979–293-119-8",
                "judul" => "Pengolahan Citra Digital : Konsep dan Teori",
                "penerbit" => "Andi Offset",
                "pengarang" => "Fajar Astuti Hermawati",
                "tahun_terbit" => "2013",
                "stok" => "79",
                "rak_id" => "33",
                "ddc_id" => "10",
                "kode" => "900-62360436"
            ]
        ];

        return ($limit) ? array_slice($buku, 0, $limit) : $buku;
    }

    private function daftarDDC(int $limit = 0): array
    {
        $ddc = [
            [
                'kode' => '000',
                'klasifikasi' => 'Karya Umum',
                'jumlah' => 400
            ],
            [
                'kode' => '100',
                'klasifikasi' => 'Filsafat dan Psikologi',
                'jumlah' => 500
            ],
            [
                'kode' => '200',
                'klasifikasi' => 'Agama',
                'jumlah' => 800
            ],
            [
                'kode' => '300',
                'klasifikasi' => 'Ilmu - Ilmu Sosial',
                'jumlah' => 500
            ],
            [
                'kode' => '400',
                'klasifikasi' => 'Bahasa',
                'jumlah' => 500
            ],
            [
                'kode' => '500',
                'klasifikasi' => 'Ilmu - Ilmu Alam dan Matematika',
                'jumlah' => 400
            ],
            [
                'kode' => '600',
                'klasifikasi' => 'Teknologi dan Ilmu Terapan',
                'jumlah' => 400
            ],
            [
                'kode' => '700',
                'klasifikasi' => 'Kesenian Hiburan dan Olahraga',
                'jumlah' => 300
            ],
            [
                'kode' => '800',
                'klasifikasi' => 'Kesusastraan',
                'jumlah' => 300
            ],
            [
                'kode' => '900',
                'klasifikasi' => 'Geografi dan Sejarah',
                'jumlah' => 500
            ]
        ];

        return ($limit) ? array_slice($ddc, 0, $limit) : $ddc;
    }
}
