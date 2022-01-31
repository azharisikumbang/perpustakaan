<?php

namespace Database\Seeders;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\DDC;
use App\Models\Rak;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BaseDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$listRak = [];
    	for ($i = 1; $i <= 27; $i++) {
    		for ($j = 1; $j <= 4; $j++) {
    			$listRak[] = [
    				'kode' => sprintf('Lemari %s', $i),
	    			'alias' => sprintf('Rak 00%s', $j) 
	    		];
    		}
    	}

        Rak::factory()->createMany($listRak);

        $listDDC = [
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

        DDC::factory()->createMany($listDDC);

        $listBuku = [
            [
                "kode" => "200-00924",
                "isbn" => "979-896-636-8",
                "judul" => "Islam Jawa; Kesalehan Normatif Versus Kebatinan",
                "sampul" => null,
                "penerbit" => "LKiS",
                "pengarang" => "Mark R. Woodward",
                "tahun_terbit" => 2004,
                "stok" => 20,
                "tanggal_masuk" => "2020-01-31",
                "rak_id" => 27,
                "ddc_id" => 3,
            ],
            [
                "kode" => "200-00923",
                "isbn" => "979-896-679-1",
                "judul" => "Islam Pasar Keadilan; Artikulasi Lokal, Kapitalisme, dan Demokrasi",
                "sampul" => null,
                "penerbit" => "LKiS",
                "pengarang" => "Robert W. Hefner",
                "tahun_terbit" => 2013,
                "stok" => 20,
                "tanggal_masuk" => "2020-01-31",
                "rak_id" => 27,
                "ddc_id" => 3,
            ],
            [
                "kode" => "200-00924",
                "isbn" => "979-338-171-X",
                "judul" => "Islam Agama ramah perempuan; Pembelaan kiai pesantren",
                "sampul" => null,
                "penerbit" => "LKiS",
                "pengarang" => "Husein Muhammad",
                "tahun_terbit" => 2013,
                "stok" => 20,
                "tanggal_masuk" => "2020-01-31",
                "rak_id" => 6,
                "ddc_id" => 3,
            ],
            [
                "kode" => "600-00028",
                "isbn" => "978-979-295-172-1",
                "judul" => "Langsung Praktik Komputerisasi Akuntansi Dengan MYOB",
                "sampul" => null,
                "penerbit" => "Wahana Komputer",
                "pengarang" => "Andi Offset",
                "tahun_terbit" => 2015,
                "stok" => 20,
                "tanggal_masuk" => "2020-01-31",
                "rak_id" => 10,
                "ddc_id" => 7,
            ]
        ];

        Buku::factory()->createMany($listBuku);

        Anggota::factory()->count(20)->create();

        User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@administrator.local',
            'password' => Hash::make('admin')
        ]);
    }
}
