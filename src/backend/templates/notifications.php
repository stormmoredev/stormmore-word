@if (Flash::exist('success'))
<div id="success-message" class="absolute right-0 border border-gray-100 shadow-lg
                rounded-md bg-white min-w-64 min-h-11 flex flex-row justify-between items-center px-2">
    <svg xmlns="http://www.w3.org/2000/svg" class="text-green-500 w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
    <div class="px-2 text-neutral-700">{{ Flash::get('success') }}</div>
    <div id="success-message-close" class="w-4 h-4 cursor-pointer">
        <svg xmlns="http://www.w3.org/2000/svg" class="text-gray-300" viewBox="0 0 20 20" fill="currentColor" stroke="currentColor">
            <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10
                            11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94
                            6.28 5.22z">
            </path>
        </svg>
    </div>
</div>
<script type="text/javascript">
    document.getElementById('success-message-close').onclick = function () {
        document.getElementById('success-message').remove();
    }
</script>
@end