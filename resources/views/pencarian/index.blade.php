<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl sm:text-2xl font-semibold text-gray-900">
            {{ __('Pencarian Global') }}
        </h2>
    </x-slot>

    <form action="{{ route('pencarian.index') }}" method="get">
        <div class="mb-8">
            <!-- from biodata peminjam -->
           <div class="border rounded border-gray-300 w-full mb-4 p-8 shadow">
               <div class="grid grid-cols-5 gap-4">
                   <div class="col-span-4">
                       <input type="text" name="q" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5" placeholder="Ketik kata kunci.." required="" value="{{ $_GET['q'] ?? '' }}">
                       <div class="py-2 flex justify-left">
                            <div class="mr-2">Kriteria : </div>
                            @role('administrator')
                            <div class="mr-2">
                                <input type="checkbox" name="kriteria[]" class="appearance-none h-4 w-4 border border-gray-300 rounded-sm bg-white checked:bg-blue-600 checked:border-blue-600 focus:outline-none transition duration-200 mt-1 align-top bg-no-repeat bg-center bg-contain float-left mr-2 cursor-pointer" value="all" <?php echo (count($kriteria) >= 3 || in_array('all', $kriteria)) || (!isset($_GET['kriteria'])) ? 'checked' : '' ?>> Semua
                            </div>
                            <div class="mr-2">
                                <input type="checkbox" name="kriteria[]" class="appearance-none h-4 w-4 border border-gray-300 rounded-sm bg-white checked:bg-blue-600 checked:border-blue-600 focus:outline-none transition duration-200 mt-1 align-top bg-no-repeat bg-center bg-contain float-left mr-2 cursor-pointer" value="peminjaman" <?php echo (in_array('peminjaman', $kriteria) || in_array('all', $kriteria)) ? "checked" : ""; ?>> Data Peminjaman
                            </div>
                            <div class="mr-2">
                                <input type="checkbox" name="kriteria[]" class="appearance-none h-4 w-4 border border-gray-300 rounded-sm bg-white checked:bg-blue-600 checked:border-blue-600 focus:outline-none transition duration-200 mt-1 align-top bg-no-repeat bg-center bg-contain float-left mr-2 cursor-pointer" value="anggota" <?php echo (in_array('anggota', $kriteria) || in_array('all', $kriteria)) ? "checked" : ""; ?>> Data Keanggotaan
                            </div>
                            @endrole
                            <div class="mr-2">
                                <input type="checkbox" name="kriteria[]" class="appearance-none h-4 w-4 border border-gray-300 rounded-sm bg-white checked:bg-blue-600 checked:border-blue-600 focus:outline-none transition duration-200 mt-1 align-top bg-no-repeat bg-center bg-contain float-left mr-2 cursor-pointer" value="ddc" <?php echo (in_array('ddc', $kriteria) || in_array('all', $kriteria)) ? "checked" : ""; ?>> Data DDC
                            </div>
                            <div class="mr-2">
                                <input type="checkbox" name="kriteria[]" class="appearance-none h-4 w-4 border border-gray-300 rounded-sm bg-white checked:bg-blue-600 checked:border-blue-600 focus:outline-none transition duration-200 mt-1 align-top bg-no-repeat bg-center bg-contain float-left mr-2 cursor-pointer" value="buku" <?php echo (in_array('buku', $kriteria) || in_array('all', $kriteria)) ? "checked" : ""; ?>> Data Buku
                            </div>
                       </div>
                   </div>
                   <div class="col-span-1">
                       <button type="submit" class="text-center text-white w-full bg-yellow-500 hover:bg-yellow-600 focus:ring-4 focus:ring-yellow-400 font-medium rounded-lg text-sm p-2.5 text-center h-min">
                           Cari
                       </button>
                   </div>
               </div>
               @if($result)
                  <div>
                    <div class="my-4 font-bold">Hasil pencarian : '{{ $_GET['q'] ?? '' }}'</div>
                    <table class="table-fixed min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="w-4 p-4 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                <th class="p-4 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                                <th class="p-4 text-left text-xs font-medium text-gray-500 uppercase">Tipe Pencarian</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $no = 1; ?>
                            @forelse($result as $item)
                                <tr class="buku-item hover:bg-gray-100">
                                    <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-500">
                                        <?= $no++; ?>
                                    </td>
                                    <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-500">
                                        {{ substr($item->display, 0, 70) }}...
                                    </td>
                                    <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-500">
                                        {{ $item->tipe_display }}
                                    </td>
                                    <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-500">
                                        <a class="underline italic text-red-500" href="{{ route('pencarian.show', array_merge(['kode' => $item->kode], [ 'kriteria' => $kriteria ])) }}">Lihat Detail</a>
                                    </td>
                                </tr>
                            @empty
                            <tr>
                                <td class="text-center p-4 whitespace-nowrap text-sm font-normal text-gray-500" colspan="4">Tidak ada data.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="my-4">
                      {{  $result->links() }}
                    </div>
               </div>
               @endif
           </div>
        </div>
    </form>
</x-app-layout>
