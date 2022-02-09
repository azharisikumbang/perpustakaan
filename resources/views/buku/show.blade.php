<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl sm:text-2xl font-semibold text-gray-900">
            {{ __('Detail - Data Buku') }}
        </h2>
    </x-slot>

    <div class="mb-8">
        <div class="bg-white shadow rounded-lg p-4">
            <div class="my-4">
                <span class="text-sm font-normal text-gray-500">Kode Buku : {{ $kode }}</span>
                <h2 class="text-2xl font-bold text-gray-900 mb-2 uppercase">{{ $judul }}</h2>
            </div>
            <div class="mb-4 grid grid-cols-3 gap-4">
                <div class="w-full">
                    @if($sampul)
                        <img src="{{ asset('storage/images/sampul/' . $sampul) }}" class="w-full max-w-full">
                    @else
                        <x-no-cover size="large" />
                    @endif
                </div>
                <div>
                    <div class="mb-2">
                        <div class="text-sm font-normal text-gray-500">ISBN</div>
                        <div class="text-base font-semibold text-gray-900">{{ $isbn }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="text-sm font-normal text-gray-500">Pengarang</div>
                        <div class="text-base font-semibold text-gray-900">{{ $pengarang }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="text-sm font-normal text-gray-500">Penerbit</div>
                        <div class="text-base font-semibold text-gray-900">{{ $penerbit }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="text-sm font-normal text-gray-500">Tahun Terbit</div>
                        <div class="text-base font-semibold text-gray-900">{{ $tahun_terbit }}</div>
                    </div>
                </div>
                <div>
                    <div class="mb-2">
                        <div class="text-sm font-normal text-gray-500">Nomor Rak</div>
                        <div class="text-base font-semibold text-gray-900">{{ $rak['kode'] }} - {{ $rak['alias'] }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="text-sm font-normal text-gray-500">Stok</div>
                        <div class="text-base font-semibold text-gray-900">{{ $stok }} unit</div>
                    </div>
                    <!-- <div class="mb-2">
                        <div class="text-sm font-normal text-gray-500">Catatan Peminjaman</div>
                        <div class="text-base font-semibold text-gray-900">3 kali</div>
                    </div> -->
                    <div class="mb-2">
                        <div class="text-sm font-normal text-gray-500">Tanggal Masuk</div>
                        <div class="text-base font-semibold text-gray-900">{{ date('d/m/Y', strtotime($tanggal_masuk)) }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="text-sm font-normal text-gray-500">Dimasukkan Oleh</div>
                        <div class="text-base font-semibold text-gray-900">Administrator</div>
                    </div>
                </div>
            </div>
            @role('administrator')
            <div class="mb-4" x-data="{}">
                <!-- @TODO implementasi peminjaman lewat tombol berikut -->
                <button type="button" class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-200 font-medium inline-flex items-center rounded-lg text-sm px-3 py-2 text-center sm:ml-auto" @click="addToBookList" data-buku-id="{{ $id }}">
                    Pinjam Buku Ini
                </button>
            </div>
            @endif
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
