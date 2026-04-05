<div {{ $attributes }}>
    <div class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50" style="display: none;" x-show="open" @click.self="close()">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="bg-white rounded-lg shadow-xl sm:w-full sm:max-w-lg mx-auto mt-10">
            <div class="px-6 py-4">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
