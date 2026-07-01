@extends('layouts.app')

@section('title', 'Return Book')
@section('header', 'Library - Return Book')

@section('content')
<div class="max-w-3xl mx-auto">
    <div id="alert-container"></div>

    <div class="card bg-white p-8 rounded-xl shadow-lg border border-gray-100">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-800">Return Book Scanner</h2>
            <p class="text-gray-500 mt-2">Scan the Book Barcode to process the return.</p>
        </div>

        <form id="returnForm" onsubmit="submitReturnForm(event)">
            <div class="space-y-6">
                <!-- Book Barcode -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Book Accession No (Barcode)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                        </div>
                        <input type="text" id="accession_no" name="accession_no" required autofocus autocomplete="off"
                            class="pl-10 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-lg py-3 border bg-gray-50 focus:bg-white transition"
                            placeholder="Scan book barcode...">
                    </div>
                </div>

                <div class="pt-2" id="fine-summary" style="display:none;">
                    <div class="p-4 bg-orange-50 border border-orange-200 rounded-lg text-orange-800 flex justify-between items-center">
                        <div>
                            <span class="font-bold">Overdue Return Detected</span>
                            <div class="text-sm mt-1" id="overdue-text"></div>
                        </div>
                        <div class="text-2xl font-black text-red-600" id="fine-amount"></div>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" id="submitBtn" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-lg font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                        Process Return
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function showAlert(message, type = 'success') {
        const bg = type === 'success' ? 'bg-green-100 border-green-500 text-green-700' : 'bg-red-100 border-red-500 text-red-700';
        document.getElementById('alert-container').innerHTML = `
            <div class="mb-6 p-4 ${bg} border-l-4 rounded shadow-sm text-lg flex justify-between items-center animate-pulse">
                <span><strong>${type === 'success' ? 'Success!' : 'Error:'}</strong> ${message}</span>
                <button onclick="this.parentElement.remove()" class="font-bold ml-4">&times;</button>
            </div>
        `;
        if(type === 'success') {
            setTimeout(() => {
                document.getElementById('alert-container').innerHTML = '';
            }, 3000);
        }
    }

    async function submitReturnForm(e) {
        e.preventDefault();
        
        const accession_no = document.getElementById('accession_no').value.trim();
        const submitBtn = document.getElementById('submitBtn');
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = 'Processing...';
        document.getElementById('fine-summary').style.display = 'none';

        try {
            const res = await fetch('{{ route('admin.library.return.update') }}', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json', 
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ accession_no })
            });
            const result = await res.json();
            
            if (result.success) {
                showAlert(result.message);
                
                if (result.fine > 0) {
                    document.getElementById('fine-summary').style.display = 'block';
                    if (result.book_section === 'Reserved') {
                        document.getElementById('overdue-text').innerText = `Book is ${result.hours_overdue} hour(s) overdue.`;
                    } else {
                        document.getElementById('overdue-text').innerText = `Book is ${result.days_overdue} day(s) overdue.`;
                    }
                    document.getElementById('fine-amount').innerText = `Fine: ₱${result.fine}`;
                }

                document.getElementById('accession_no').value = '';
                document.getElementById('accession_no').focus();
            } else {
                showAlert(result.message || 'Operation failed', 'error');
                document.getElementById('accession_no').select();
            }
        } catch (err) {
            showAlert('A network error occurred.', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Process Return';
        }
    }
</script>
@endsection
