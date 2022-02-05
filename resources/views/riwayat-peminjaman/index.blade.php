<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl sm:text-2xl font-semibold text-gray-900">
            {{ __('Daftar Peminjaman') }}
        </h2>
    </x-slot>

    <div class="mb-8">
    	<div class="block sm:flex items-center md:divide-x md:divide-gray-100 mb-4">
            <form class="sm:pr-3 mb-4 sm:mb-0" action="" method="get">
                <label for="peminjaman-search" class="sr-only">Search</label>
                <div class="mt-1 relative sm:w-64 xl:w-96">
                    <input type="text" name="cari" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-green-600 focus:border-green-600 block w-full p-2.5" placeholder="Cari sesuai dan tekan enter.." id="cari-input" value="{{ $_GET['cari'] ?? '' }}">
                    <button type="submit" id="cari-button" style="display: none">Cari</button>
                </div>
            </form>
    		<div class="flex items-center sm:justify-end w-full">
                <div class="hidden md:flex pl-2 space-x-1">
                    <span class="p-2.5">Tampilkan</span>
                    <select 
                        class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-green-600 focus:border-green-600 block w-20 p-2.5 cursor-pointer w-12"
                        onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value)";
                    >
                        <option value="{{ secure_url(route('peminjaman.index', ['limit' => 10 ])) }}" <?= ($per_page == 10) ? 'selected' : '' ?>>10</option>
                        <option value="{{ secure_url(route('peminjaman.index', ['limit' => 20 ])) }}" <?= ($per_page == 20) ? 'selected' : '' ?>>20</option>
                        <option value="{{ secure_url(route('peminjaman.index', ['limit' => 50 ])) }}" <?= ($per_page == 50) ? 'selected' : '' ?>>50</option>
                        <option value="{{ secure_url(route('peminjaman.index', ['limit' => 100 ])) }}" <?= ($per_page == 100) ? 'selected' : '' ?>>100</option>
                    </select>
                </div>
	    	</div>
    	</div>
    	<div class="flex flex-col">
    		<div class="overflow-x-auto">
    			<div class="align-middle inline-block min-w-full">
    				<div class="shadow overflow-hidden">
    					<table class="table-fixed min-w-full divide-y divide-gray-200">
    						<thead class="bg-gray-100">
    							<tr>
    								<th class="w-4 p-4 text-left text-xs font-medium text-gray-500 uppercase">No</th>
    								<th class="p-4 text-left text-xs font-medium text-gray-500 uppercase">No. Peminjaman</th>
                                    <th class="p-4 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Peminjaman</th>
                                    <th class="p-4 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Pengembalian</th>
                                    <th class="p-4 text-left text-xs font-medium text-gray-500 uppercase">Jumlah Buku</th>
    								<th></th>
    							</tr>
    						</thead>
    						<tbody class="bg-white divide-y divide-gray-200">
    							@forelse($data as $peminjaman)
    							<tr class="peminjaman-item hover:bg-gray-100">
    								<td class="p-4 whitespace-nowrap text-sm font-normal text-gray-500">
    									{{ $loop->iteration }}
    								</td>
    								<td class="p-4 whitespace-nowrap text-sm font-normal text-gray-500">
    									<a class="underline hover:text-red-500" href="{{ route('riwayat-peminjaman.show', ['peminjaman' => $peminjaman['id'] ]) }}">{{ $peminjaman['kode'] }}</a>
    								</td>
    								<td class="p-4 whitespace-nowrap text-sm font-normal text-gray-500">
    									{{ $peminjaman['tanggal_peminjaman'] }}
    								</td>
                                    <td>{{ $peminjaman['tanggal_pengembalian'] ?? '-'  }}</td>
                                    <td class="text-center">{{ $peminjaman['total_buku'] }}</td>
    								<td class="p-4 whitespace-nowrap" x-data="{ deleteOpen: false }">
                                        <div class="space-x-2 justify-end flex">
                                            <a href="{{ route('riwayat-peminjaman.show', ['peminjaman' => $peminjaman['id']]) }}" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 font-medium rounded-lg text-sm inline-flex items-center px-3 py-2 text-center">
                                                Lihat Detail
                                            </a>
                                        </div>
    								</td>
    							</tr>
    							@empty
    							<tr>
    								<td class="text-center p-4 whitespace-nowrap text-sm font-normal text-gray-500" colspan="4">Tidak ada data.</td>
    							</tr>
    							@endforelse
    						</tbody>
    					</table>
    				</div>
    			</div>
    		</div>
    	</div>
    	<div class="bg-white sticky sm:flex items-center w-full sm:justify-between bottom-0 right-0 border-t border-gray-200 p-4">
    		<div class="flex items-center mb-4 sm:mb-0">
    			@if($prev_page_url)
    			<a href="{{ $prev_page_url }}" class="text-gray-500 hover:text-gray-900 cursor-pointer p-1 hover:bg-gray-100 rounded inline-flex justify-center">
    				<svg class="w-7 h-7" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
    			</a>
    			@else
				<span class="cursor-not-allowed">
					<svg class="w-7 h-7" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
				</span>
    			@endif
    			@if($next_page_url)
    			<a href="{{ $next_page_url }}" class="text-gray-500 hover:text-gray-900 cursor-pointer p-1 hover:bg-gray-100 rounded inline-flex justify-center mr-2">
					<svg class="w-7 h-7" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
				</a>
    			@else
				<span class="cursor-not-allowed">
					<svg class="w-7 h-7" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
				</span>
    			@endif
				<span class="text-sm font-normal text-gray-500">Ditampilkan <span class="text-gray-900 font-semibold">{{ $from }}-{{ $to }}</span> dari <span class="text-gray-900 font-semibold">{{ $total }}</span></span>
    		</div>
    	</div>
    </div>

</x-app-layout>
