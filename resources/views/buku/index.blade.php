<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl sm:text-2xl font-semibold text-gray-900">
            {{ __('Daftar Buku') }}
        </h2>
    </x-slot>

    <div class="mb-8">
        <div class="block sm:flex items-center md:divide-x md:divide-gray-100 mb-4">
            <form class="sm:pr-3 mb-4 sm:mb-0" action="" method="get">
                <label for="buku-search" class="sr-only">Search</label>
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
                        <option value="{{ secure_url(route('buku.index', ['limit' => 10 ])) }}" <?= ($per_page == 10) ? 'selected' : '' ?>>10</option>
                        <option value="{{ secure_url(route('buku.index', ['limit' => 20 ])) }}" <?= ($per_page == 20) ? 'selected' : '' ?>>20</option>
                        <option value="{{ secure_url(route('buku.index', ['limit' => 50 ])) }}" <?= ($per_page == 50) ? 'selected' : '' ?>>50</option>
                        <option value="{{ secure_url(route('buku.index', ['limit' => 100 ])) }}" <?= ($per_page == 100) ? 'selected' : '' ?>>100</option>
                    </select>
                </div>
                @if($is_administrator)
                <a href="{{ route('buku.create') }}" class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-200 font-medium inline-flex items-center rounded-lg text-sm px-3 py-2 text-center sm:ml-auto">
                    <svg class="-ml-1 mr-2 h-6 w-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path></svg>
                    Tambah Data Buku
                </a>
                @endif
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
                                    <th class="p-4 text-left text-xs font-medium text-gray-500 uppercase">Kode Buku</th>
                                    <th class="p-4 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                                    <th class="p-4 text-left text-xs font-medium text-gray-500 uppercase">Klasifikasi Buku</th>
                                    <th class="p-4 text-left text-xs font-medium text-gray-500 uppercase">Posisi Rak</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($data as $buku)
                                <tr class="buku-item hover:bg-gray-100">
                                    <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-500">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-500">
                                        <a class="hover:underline cursor-pointer" href="{{ route('buku.show', ['buku' => $buku['id']]) }}">{{ $buku['kode'] }}</a>
                                    </td>
                                    <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-500">
                                        {{ $buku['judul'] }}
                                    </td>
                                    <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-500">
                                        {{ $buku['ddc']['kode'] }} - {{ $buku['ddc']['klasifikasi'] }}
                                    </td>
                                    <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-500">
                                        {{ $buku['rak']['kode'] }} - {{ $buku['rak']['alias'] }}
                                    </td>
                                    @if($is_administrator)
                                    <td class="p-4 whitespace-nowrap" x-data="{ deleteOpen: false }">
                                        <div class="space-x-2 justify-end flex">
                                            <button class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm inline-flex items-center px-3 py-2 text-center" @click="addToBookList" data-buku-id="{{ $buku['id'] }}">
                                                Pinjam Buku
                                            </button>
                                            <a href="{{ route('buku.edit', ['buku' => $buku['id']]) }}" class="text-white bg-yellow-600 hover:bg-yellow-700 focus:ring-4 focus:ring-yellow-200 font-medium rounded-lg text-sm inline-flex items-center px-3 py-2 text-center">
                                                Edit Item
                                            </a>
                                            <button class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-3 py-2 text-center" @click="deleteOpen = true"> 
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
                                                        <h3 class="text-xl font-normal text-gray-500 mt-5 mb-6">Anda Yakin ingin menghapus buku ini ?</h3>
                                                        <button data-buku-id="{{ $buku['id'] }}" @click="deleteBukuItem" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-base inline-flex items-center px-3 py-2.5 text-center mr-2">
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
                                    @endif
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
    <script type="text/javascript">
        function deleteBukuItem(e) {
            const method = "DELETE";   
            const bukuId = e.target.dataset.bukuId;
            const bukuItemRowElement = e.target.closest('.buku-item');

            e.target.innerHTML = "Menghapus...";
            axios.post(`/admin/buku/${bukuId}/`, { "_method": method })
                .then(response => {
                    bukuItemRowElement.remove()
                    renderAlert("Item berhasil dihapus.")
                }).catch(error => {
                    renderAlert("Terjadi kesalahan ! Gagal menghapus data, silahkan coba lagi.", 'red');
                    e.target.innerHTML = "Ya, Saya Yakin"
                })
        }

        function addToBookList(e) {
            const bukuId = e.target.dataset.bukuId;
            e.target.innerHTML = "Menambahkan...";
            e.target.classList.add('cursor-not-allowed', 'italic', 'opacity-50');

            axios.post('/admin/keranjang', { "_token": "{{ csrf_token() }}", "buku" : bukuId, "jumlah" : 1 })
                .then(response => {
                    if(renderToCart(response.data)) renderAlert("Buku Berhasil ditambahkan ke keranjang pinjam.");
                    e.target.innerHTML = "Ditambahkan";
                }).catch(error => {
                    renderAlert("Terjadi kesalahan ! Buku gagal ditambahakan ke keranjang pinjam, silahkan coba lagi.", 'red');
                    e.target.classList.remove('cursor-not-allowed', 'italic', 'opacity-50');
                });
        }

        function renderToCart(item) {
            const listBookCart = document.getElementById("list-book-cart");

            if (listBookCart.children[0].classList.contains('no-data')) {
                listBookCart.innerHTML = null;
            }

            if (listBookCart.children.length > 0) {
                for (let i = 0; i < listBookCart.children.length; i++) { 
                    if (listBookCart.children[i].dataset.bukuKode == item.data.kode) {
                        renderAlert("Info ! Buku sudah ada di dalam keranjang pinjam.", 'blue');
                        return false;
                    };
                }
            }

            const itemWrapper = document.createElement('div');
            itemWrapper.classList.add("border-b", "py-2", "mb-2", "cart-item");
            itemWrapper.setAttribute('data-buku-kode', item.data.kode);
            itemWrapper.setAttribute('data-buku-id', item.data.id);

            const itemTitle = document.createElement("h4");
            itemTitle.classList.add("font-bold", "mb-2");
            itemTitle.innerHTML = item.data.details.judul;

            const itemDetails = document.createElement("div");
            itemDetails.classList.add("flex", "justify-between");

            const itemDetailsLeftWrapper = document.createElement("div");
            const itemDetailsPenulis = document.createElement("p");
            itemDetailsPenulis.classList.add("italic", "font-light", "text-sm", "text-gray-400");
            itemDetailsPenulis.innerHTML = item.data.details.pengarang;
            const itemDetailsHapusBtn = document.createElement("button");
            itemDetailsHapusBtn.classList.add("text-red-700", "underline", "pointer");
            itemDetailsHapusBtn.setAttribute('x-on:click', 'removeFromBookList');
            itemDetailsHapusBtn.innerHTML = "Hapus";

            const itemDetailsRightWrapper = document.createElement("div");
            itemDetailsRightWrapper.setAttribute('x-data', '{ total: 1 }')
            const itemDetailsDecrementBtn = document.createElement("button");
            itemDetailsDecrementBtn.classList.add("w-12", "border", "p-2");
            itemDetailsDecrementBtn.setAttribute('x-on:click', 'total--');
            itemDetailsDecrementBtn.innerHTML = "-";

            const itemDetailsTotalInput = document.createElement("input");
            itemDetailsTotalInput.classList.add("text-center", "w-16", "border", "p-2");
            itemDetailsTotalInput.setAttribute('x-bind:value', 'total');
            itemDetailsTotalInput.value = 1;

            const itemDetailsIncrementBtn = document.createElement("button");
            itemDetailsIncrementBtn.classList.add("w-12", "border", "p-2");
            itemDetailsIncrementBtn.setAttribute('x-on:click', 'total++');
            itemDetailsIncrementBtn.innerHTML = "+";

            itemDetailsLeftWrapper.append(itemDetailsPenulis);
            itemDetailsLeftWrapper.append(itemDetailsHapusBtn);

            itemDetailsRightWrapper.append(itemDetailsDecrementBtn);
            itemDetailsRightWrapper.append(itemDetailsTotalInput);
            itemDetailsRightWrapper.append(itemDetailsIncrementBtn);

            itemDetails.append(itemDetailsLeftWrapper);
            itemDetails.append(itemDetailsRightWrapper);

            itemWrapper.append(itemTitle);
            itemWrapper.append(itemDetails);

            listBookCart.append(itemWrapper);

            return true;
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
