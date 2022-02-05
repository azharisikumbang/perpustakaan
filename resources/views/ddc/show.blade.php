<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl sm:text-2xl font-semibold text-gray-900">
            {{ __('Detail Klasifikasi DDC') }}
        </h2>
    </x-slot>

    <div class="mb-8">
        <div class="border rounded border-gray-300 w-full mb-4 p-4 shadow" >
            <div class="mb-2 font-semibold text-xl">
                Keterangan Klasifikasi :
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <div class="mb-2">
                        <div class="text-sm font-normal text-gray-500">Kode Klasifikasi</div>
                        <div class="text-base font-semibold text-gray-900">{{ $ddc['kode'] }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="text-sm font-normal text-gray-500">Judul Klasifikasi</div>
                        <div class="text-base font-semibold text-gray-900">{{ $ddc['klasifikasi'] }}</div>
                    </div>
                </div>
                <div>
                    <div class="mb-2">
                        <div class="text-sm font-normal text-gray-500">Jumlah Buku</div>
                        <div class="text-base font-semibold text-gray-900">{{ $ddc['jumlah'] }}</div>
                    </div>
                </div>  
            </div>
        </div>
        <div class="block sm:flex items-center md:divide-x md:divide-gray-100 mb-4">
            <div class="flex items-center sm:justify-between w-full items-center">
                <div class="mt-4 font-semibold text-xl px-2">
                    Daftar Buku dengan klasifikasi '{{ $ddc['kode'] }}' :
                </div>
                <div class="hidden md:flex pl-2 space-x-1">
                    <span class="p-2.5">Tampilkan</span>
                    <select 
                        class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-green-600 focus:border-green-600 block w-20 p-2.5 cursor-pointer w-12"
                        onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value)";
                    >
                        <option value="{{ secure_url(route('ddc.show', ['limit' => 10, 'ddc' => $ddc['id'] ])) }}" <?= ($list_buku['per_page'] == 10) ? 'selected' : '' ?>>10</option>
                        <option value="{{ secure_url(route('ddc.show', ['limit' => 20, 'ddc' => $ddc['id'] ])) }}" <?= ($list_buku['per_page'] == 20) ? 'selected' : '' ?>>20</option>
                        <option value="{{ secure_url(route('ddc.show', ['limit' => 50, 'ddc' => $ddc['id'] ])) }}" <?= ($list_buku['per_page'] == 50) ? 'selected' : '' ?>>50</option>
                        <option value="{{ secure_url(route('ddc.show', ['limit' => 100, 'ddc' => $ddc['id'] ])) }}" <?= ($list_buku['per_page'] == 100) ? 'selected' : '' ?>>100</option>
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
                                    <th class="p-4 text-left text-xs font-medium text-gray-500 uppercase">Kode Buku</th>
                                    <th class="p-4 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($list_buku['data'] as $buku)
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
                                    @if($is_administrator)
                                    <td class="p-4 whitespace-nowrap" x-data="{ deleteOpen: false }">
                                        <div class="space-x-2 justify-end flex">
                                            <button class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm inline-flex items-center px-3 py-2 text-center" @click="addToBookList" data-buku-id="{{ $buku['id'] }}">
                                                Pinjam Buku
                                            </button>
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
                @if($list_buku['prev_page_url'])
                <a href="{{ $list_buku['prev_page_url'] }}" class="text-gray-500 hover:text-gray-900 cursor-pointer p-1 hover:bg-gray-100 rounded inline-flex justify-center">
                    <svg class="w-7 h-7" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                </a>
                @else
                <span class="cursor-not-allowed">
                    <svg class="w-7 h-7" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                </span>
                @endif
                @if($list_buku['next_page_url'])
                <a href="{{ $list_buku['next_page_url'] }}" class="text-gray-500 hover:text-gray-900 cursor-pointer p-1 hover:bg-gray-100 rounded inline-flex justify-center mr-2">
                    <svg class="w-7 h-7" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                </a>
                @else
                <span class="cursor-not-allowed">
                    <svg class="w-7 h-7" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                </span>
                @endif
                <span class="text-sm font-normal text-gray-500">Ditampilkan <span class="text-gray-900 font-semibold">{{ $list_buku['from'] }}-{{ $list_buku['to'] }}</span> dari <span class="text-gray-900 font-semibold">{{ $list_buku['total'] }}</span></span>
            </div>
        </div>
    </div>
    @if($is_administrator)
    <script type="text/javascript">
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
    @endif
</x-app-layout>
