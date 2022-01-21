<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl sm:text-2xl font-semibold text-gray-900">
            {{ __('Daftar Buku Yang Diajukan') }}
        </h2>
    </x-slot>

    <form action="{{ route('pengajuan.store') }}" method="post">
        @csrf
        <div class="mb-8" x-data="initData()">
            <!-- from biodata peminjam -->
           <div class="border rounded border-gray-300 w-full mb-4 p-8 shadow" id="checkout-user-form" >
               <div class="grid grid-cols-12 gap-4">
                   <div class="col-span-9">
                       <input type="text" name="user" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5" placeholder="Ketik nomor keanggotaan" x-model="user" :value="user" required="">
                   </div>
                   <div class="flex w-full col-span-3">
                       <button type="button" @click="getUserData($data)" class="text-center text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm inline-flex items-center px-3 py-2 text-center">
                           Cek Keanggotaan
                       </button>
                       <!-- @TODO : create registration feature -->
                       <a href="{{ route('keanggotaan.create') }}" class="text-center text-white bg-yellow-500 hover:bg-yellow-800 focus:ring-4 focus:ring-yellow-300 font-medium rounded-lg text-sm inline-flex items-center px-3 py-2 text-center ml-2">
                           Daftar Baru
                       </a>
                   </div>
               </div>
               <div x-show="showUserDetails">
                    <div x-show="userNotFound" class="mt-4 px-2 text-red-500">
                        <i>* Anggota yang anda maksud tidak ditemukan. Silahkan coba lagi atau <a class="underline" href="{{ route('keanggotaan.create') }}">Daftarkan</a>.</i>
                    </div>
                    <div x-show="!userNotFound">
                        <div class="mt-4 px-2 text-muted">
                            Hasil pencarian :
                        </div>
                        <div class="grid grid-cols-2 gap-4 mt-4 px-2" >
                            <!-- Pribadi -->
                            <div>
                                <div class="mb-2">
                                    <div class="text-sm font-normal text-gray-500">Nama</div>
                                    <div class="text-base font-semibold text-gray-900" x-text="fetchedUser['nama']"></div>
                                </div>
                                <div class="mb-2">
                                    <div class="text-sm font-normal text-gray-500">No. Telepon</div>
                                    <div class="text-base font-semibold text-gray-900" x-text="fetchedUser['kontak']"></div>
                                </div>
                            </div>
                            <!-- Institusi -->
                            <div>
                                <div class="mb-2">
                                    <div class="text-sm font-normal text-gray-500">No. Keanggotaan</div>
                                    <div class="text-base font-semibold text-gray-900" x-text="fetchedUser['nomor_identitas']"></div>
                                </div>
                                <div class="mb-2">
                                    <div class="text-sm font-normal text-gray-500">Institusi</div>
                                    <div class="text-base font-semibold text-gray-900" x-text="fetchedUser['institusi']"></div>
                                </div>
                            </div>
                        </div>
                    </div>
               </div>
           </div>
           <div class="border rounded border-gray-300 w-full mb-4 p-8 shadow mb-4 flex justify-between">
                <div class="w-4/6 mr-4">
                    @if(session('keranjang-pinjam'))
                        @forelse(session('keranjang-pinjam')['list-buku'] as $buku)
                            <div x-data='{ total: <?= $buku["jumlah"] ?> }' class="py-8 border-b border-gray-300 flex justify-between checkout-item"  data-buku-id="{{ $buku['id'] }}" data-buku-kode="{{ $buku['kode'] }}" :data-buku-total="total">
                                <div class="w-1/4">
                                    <x-no-cover size="large" />
                                </div>
                                <div class="w-3/4">
                                    <div class="flex flex-wrap content-between h-full">
                                        <div class="w-full mb-4">
                                            <div class="font-light text-sm text-gray-400">
                                                {{ $buku['kode'] }}
                                            </div>
                                            <div class="font-semibold mb-2 text-xl">
                                                {{ $buku['details']['judul'] }}
                                            </div>
                                            <div class="font-light text-sm mb-2 text-gray-400">
                                                ISBN: 978-602-8519-93-9 &#124; 
                                                Penulis: {{ $buku['details']['pengarang'] }} &#124; 
                                                Stok: {{ $buku['details']['stok'] }}
                                            </div>
                                            <div class="font-light text-sm mb-2 text-red-400 italic">
                                                <button type="button" class="text-red-700 underline pointer" @click="removeFromListCheckout">Hapus</button>
                                            </div>
                                        </div>
                                        @if($buku['details']['stok'] > 1)
                                        <div>
                                            <button type="button" class="w-12 text-sm border px-2 py-1 border-gray-300" @click="total--; decrement()">-</button>
                                            <input type="text" name="buku_item_total[{{ $buku['kode'] }}]" :value='total' class="text-center w-12 text-sm border border-gray-300 py-1" min="0">
                                            <button type="button" class="w-12 text-sm border px-2 py-1 border-gray-300" @click="total++; increment()">+</button>
                                        </div>
                                        @else
                                        <div class="text-red-600 italic">
                                            *Stok tidak tersedia untuk buku ini.
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center my-4 no-data">Tidak ada data.</div>
                        @endforelse
                    @else
                        <div class="text-center my-4 no-data">Tidak ada data.</div>
                    @endif
                </div>
                <div class="w-2/6 mt-8">
                    <div class="text-xl font-bold mb-2">Informasi Peminjaman</div>
                    @if(session('keranjang-pinjam'))
                    <div class="py-4 border-b border-gray-300 flex justify-between">
                        <div class="font-light text-gray-400">Jumlah Dipinjam</div>
                        <div class="font-semibold"><span x-text="jumlah"></span> buku</div>
                        <input type="hidden" name="buku_total" :value="jumlah" x-model="jumlah">
                    </div>
                    @endif
                    <div class="py-4 border-b border-gray-300 flex justify-between">
                        <div class="font-light text-gray-400">Lama Peminjaman</div>
                        <div class="font-semibold">{{ $pengaturan['lama_pinjaman'] }} hari</div>
                    </div>
                    <div class="py-4 border-b border-gray-300 flex justify-between">
                        <div class="font-light text-gray-400">Tanggal Pengembalian</div>
                        <div class="font-semibold">{{ date('d/m/Y', strtotime("+{$pengaturan['lama_pinjaman']} days")) }}</div>
                    </div>
                    <div class="py-4 border-b border-gray-300 flex justify-between">
                        <div class="font-light text-gray-400">Denda Keterlambatan</div>
                        <div class="font-semibold">Rp. {{ number_format($pengaturan['nominal_denda'], 0, 2, ".") }} / hari</div>
                    </div>
                    <div class="py-4">
                        <!-- @TODO implement this -->
                        <button type="submit" class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg w-full px-3 py-2 text-center">Ajukan Pinjaman</button>
                    </div>
                </div>    
           </div>
        </div>
    </form>
    <script type="text/javascript">
        function initData() {
            return {
                user: '',
                showUserDetails : false,
                userNotFound : false,
                fetchedUser: {
                    nomor_identitas: '',
                    nama: '',
                    institusi: '',
                    kontak: ''
                },
                jumlah: "<?= $jumlah_buku ?>",
                increment() {
                    this.jumlah++;
                }, 
                decrement() {
                    this.jumlah--;
                },
                removeFromListCheckout(e) {
                    const parent = e.target.closest('.checkout-item');
                    const bukuId = parent.dataset.bukuId;
                    e.target.innerHTML = "Menghapus...";

                    axios.post('/admin/keranjang/' + bukuId, { "_token": "{{ csrf_token() }}", "_method" : "DELETE" })
                        .then(response => {
                            parent.closest('.checkout-item').remove(); 
                            e.target.innerHTML = "Hapus";
                            this.jumlah -= parent.dataset.bukuTotal 
                        }).catch(error => {
                            e.target.innerHTML = "Hapus";
                        });

                },
                getUserData(e) {
                    axios.post('/admin/anggota/cek', {'nomor_identitas': this.user})
                        .then(response => {
                            this.showUserDetails = true;
                            this.userNotFound = false;
                            this.fetchedUser.nomor_identitas = response.data.data.nomor_identitas;
                            this.fetchedUser.nama = response.data.data.nama;
                            this.fetchedUser.institusi = response.data.data.institusi;
                            this.fetchedUser.kontak = response.data.data.kontak;
                        })
                        .catch(error => {
                            this.showUserDetails = true;
                            this.userNotFound = true;
                        });
                }
            }
        }
    </script>
</x-app-layout>
