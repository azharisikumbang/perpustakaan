<!-- <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100"> -->
<div class="min-h-screen flex flex-col sm:justify-center items-center sm:pt-0">
    <div>
        {{ $logo }}
    </div>

    <div {{ $attributes->merge(['class' => 'w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden
        sm:rounded-lg']) }}>
        {{ $slot }}
    </div>

    {{-- demo account --}}

    <div class="sm:max-w-md shadow-md w-full mt-6 overflow-hidden
        sm:rounded-lg">
        <div class="flex p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400"
            role="alert">
            <div>
                <ul class="mt-1.5 list-disc list-inside">
                    <strong>Perhatian:</strong>
                    <li>Website ini diperuntukkan untuk uji coba.</li>
                    <li>Data dan informasi adalah bukan sebenarnya.</li>
                </ul>
                <ul class="mt-1.5 list-disc list-inside">
                    <strong>Akun akses Administrator:</strong>
                    <li>username = administrator@demo.test</li>
                    <li>password = admin123</li>
                </ul>
                <ul class="mt-1.5 list-disc list-inside">
                    <strong>Akun akses Kepala:</strong>
                    <li>username = kepala@demo.test</li>
                    <li>password = kepala123</li>
                </ul>
                <p class="italic mt-2">Lebih lanjut di <a
                        href="mailto:contact@azharisaputra.web.id">contact@azharisaputra.web.id</a> </p>
            </div>
        </div>
    </div>
</div>