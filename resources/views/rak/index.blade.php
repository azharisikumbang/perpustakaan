<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl sm:text-2xl font-semibold text-gray-900">
            {{ __('Daftar Rak') }}
        </h2>
    </x-slot>

    <div class="mb-8">
    	<div class="block sm:flex items-center md:divide-x md:divide-gray-100 mb-4">
            <form class="sm:pr-3 mb-4 sm:mb-0" action="" method="get">
                <label for="rak-search" class="sr-only">Search</label>
                <div class="mt-1 relative sm:w-64 xl:w-96">
                    <input type="text" name="cari" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-green-600 focus:border-green-600 block w-full p-2.5" placeholder="Cari sesuai dan tekan enter.." id="cari-input"  value="{{ $_GET['cari'] ?? '' }}">
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
                        <option value="{{ secure_url(route('rak.index', ['limit' => 10 ])) }}" {{ ($per_page == 10) ? 'selected' : ''}}>10</option>
                        <option value="{{ secure_url(route('rak.index', ['limit' => 20 ])) }}" {{ ($per_page == 20) ? 'selected' : ''}}>20</option>
                        <option value="{{ secure_url(route('rak.index', ['limit' => 50 ])) }}" {{ ($per_page == 50) ? 'selected' : ''}}>50</option>
                        <option value="{{ secure_url(route('rak.index', ['limit' => 100 ])) }}" {{ ($per_page == 100) ? 'selected' : ''}}>100</option>
                    </select>
                </div>
	    		<a href="{{ route('rak.create') }}" class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-200 font-medium inline-flex items-center rounded-lg text-sm px-3 py-2 text-center sm:ml-auto">
	    			<svg class="-ml-1 mr-2 h-6 w-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path></svg>
	    			Tambah Data Rak
	    		</a>
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
    								<th class="p-4 text-left text-xs font-medium text-gray-500 uppercase">Kode Rak</th>
    								<th class="p-4 text-left text-xs font-medium text-gray-500 uppercase">Alias</th>
    								<th></th>
    							</tr>
    						</thead>
    						<tbody class="bg-white divide-y divide-gray-200">
    							@forelse($data as $rak)
    							<tr class="rak-item hover:bg-gray-100">
    								<td class="p-4 whitespace-nowrap text-sm font-normal text-gray-500">
    									{{ $loop->iteration }}
    								</td>
    								<td class="p-4 whitespace-nowrap text-sm font-normal text-gray-500">
    									{{ $rak['kode'] }}
    								</td>
    								<td class="p-4 whitespace-nowrap text-sm font-normal text-gray-500">
    									{{ $rak['alias'] }}
    								</td>
    								<td class="p-4 whitespace-nowrap" x-data="{ deleteOpen: false }">
                                        <div class="space-x-2 justify-end flex">
                                            <a href="{{ route('rak.edit', ['rak' => $rak['id']]) }}" class="text-white bg-yellow-600 hover:bg-yellow-700 focus:ring-4 focus:ring-yellow-200 font-medium rounded-lg text-sm inline-flex items-center px-3 py-2 text-center">
                                                <svg class="mr-2 h-5 w-5" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" fill="white"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"></path></svg>
                                                Edit Item
                                            </a>
                                            <button class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-3 py-2 text-center" @click="deleteOpen = true">
                                                <svg class="mr-2 h-5 w-5" fill="white" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg> 
                                                Hapus Item
                                            </button>
                                        </div>
                                        <!-- overflow -->
                                        <div x-show="deleteOpen" class="bg-gray-900 bg-opacity-50 fixed inset-0 z-40">
                                            <div class="overflow-x-hidden overflow-y-auto fixed top-4 left-0 right-0 md:inset-0 z-50 justify-center items-center h-modal sm:h-full flex" x-transition>
                                            <div class="relative w-full max-w-md px-4 h-full md:h-auto">
                                                <div class="bg-white rounded-lg shadow relative">
                                                    <div class="flex justify-end p-2">
                                                        <button @click="deleteOpen = false" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
                                                            <svg class="w-5 h-5" fill="gray" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                                        </button>
                                                    </div>
                                                    <div class="p-6 pt-0 text-center">
                                                        <svg class="w-20 h-20 text-red-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        <h3 class="text-xl font-normal text-gray-500 mt-5 mb-6">Anda Yakin ingin menghapus rak ini ?</h3>
                                                        <button data-rak-id="{{ $rak['id'] }}" @click="deleteRakItem" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-base inline-flex items-center px-3 py-2.5 text-center mr-2">
                                                            Ya, Saya Yakin
                                                        </a>
                                                        <button @click="deleteOpen = false" class="text-gray-900 bg-white hover:bg-gray-100 focus:ring-4 focus:ring-cyan-200 border border-gray-200 font-medium inline-flex items-center rounded-lg text-base px-3 py-2.5 text-center">
                                                            Batalkan
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            </div>
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
    <!--  -->
    <script type="text/javascript">
        function deleteRakItem(e) {
            e.target.innerHTML = "Menghapus...";

            const method = "DELETE";   
            const rakId = e.target.dataset.rakId;
            const rakItemRowElement = e.target.closest('.rak-item')

            axios.post(`/admin/rak/${rakId}/`, { "_method": method })
                .then(response => {
                    rakItemRowElement.remove()
                    renderAlert("Item berhasil dihapus.")
                }).catch(error => {
                    e.target.innerHTML = "Ya, Saya Yakin";
                    renderAlert("Terjadi kesalahan ! Gagal menghapus data, silahkan coba lagi.", 'red')
                })
        }

        function renderAlert(message, color = 'green') {
            const el = document.getElementById('alert');
            const alertWrapper = document.createElement('div');
            
            alertWrapper.classList.add(`bg-${color}-100`, 'rounded-lg', 'p-4', 'mb-4', 'text-sm', `text-${color}-700`);
            alertWrapper.textContent = message;
            
            el.innerHTML = alertWrapper.outerHTML
        }
    </script>
</x-app-layout>
