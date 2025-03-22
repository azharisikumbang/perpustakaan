<div class="relative delay-show" x-data="{ open: false }">
    <div @click="open = ! open">
        {{ $trigger }}
    </div>

    <div x-show="open" class="bg-gray-900 bg-opacity-50 fixed inset-0 z-40" style="display: :none">
        <div x-show="open" x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="transform opacity-0 scale-100"
            x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-100"
            class="overflow-x-hidden overflow-y-auto fixed top-0 left-0 right-0 md:inset-0 z-50 justify-center items-center h-modal sm:h-full flex"
            style="display: :none">
            <div class="w-2/5 h-screen bg-white shadow-lg inset-y-0 right-0 absolute">
                <h2 class="p-4 text-xl sm:text-2xl font-semibold text-gray-900 border-b">
                    Keranjang Buku
                </h2>
                <div class="p-4 border-b h-3/4 overflow-y-auto" id="list-book-cart">
                    {{ $content }}
                </div>
                <div class="p-4 text-right">
                    <div class="space-x-2 justify-end flex">
                        <button @click="checkoutBookList"
                            class="text-white bg-yellow-600 hover:bg-yellow-700 focus:ring-4 focus:ring-yellow-200 font-medium rounded-lg text-sm inline-flex items-center px-3 py-2 text-center"
                            id="keranjang-ajukan-btn">
                            Ajukan
                        </button>
                        <button
                            class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-3 py-2 text-center"
                            @click="open = false">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function checkoutBookList(e){
            const listBookWrapper = document.getElementById('list-book-cart');
            let body = []

            if (listBookWrapper.children[0].dataset.keranjangTotal < 1) {
                return;
            }

            e.target.innerHTML = "Mengajukan...";
            e.target.classList.add('cursor-not-allowed', 'italic', 'opacity-50');

            for (var i = listBookWrapper.children.length - 1; i >= 0; i--) {
                body[i] = { 
                    'buku' : listBookWrapper.children[i].dataset.bukuId, 
                    'jumlah' : listBookWrapper.children[i].querySelectorAll('input')[0].value 
                }
            }

            axios.post('/admin/keranjang', { '_token': "{{ csrf_token() }}", '_method' : 'PUT', 'list_buku' : body })
                .then(response => {
                    window.location = "{{ route('pengajuan.index') }}";
                })
                .catch(error => {
                    e.target.innerHTML = "Ajukan";
                    e.target.classList.remove('cursor-not-allowed', 'italic', 'opacity-50');
                });
        }
    </script>
</div>