<!-- Create Task Modal -->
<div id="createTaskModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeCreateModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal Panel -->
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <div class="bg-white px-6 pt-6 pb-4 sm:pb-6">
                <div class="sm:flex sm:items-start">
                    <div class="text-center sm:text-left w-full">
                        <h3 class="text-xl leading-6 font-black text-gray-900 mb-6" id="modal-title">
                            Buat Task Baru
                        </h3>
                        <div class="mt-2 text-left">
                            <form id="createTaskForm" class="space-y-4">
                                @csrf
                                <!-- Title -->
                                <div>
                                    <label for="title" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Judul Task <span class="text-red-500">*</span></label>
                                    <input type="text" name="title" id="title" required class="mt-1 focus:ring-2 focus:ring-cuan-green/20 focus:border-cuan-green block w-full shadow-sm text-sm border-gray-300 rounded-lg py-2.5 transition-all outline-none" placeholder="Contoh: Perbaiki bug login">
                                </div>
 
                                <!-- Description -->
                                <div>
                                    <label for="description" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Deskripsi</label>
                                    <textarea name="description" id="description" rows="3" class="mt-1 focus:ring-2 focus:ring-cuan-green/20 focus:border-cuan-green block w-full shadow-sm text-sm border-gray-300 rounded-lg py-2.5 transition-all outline-none"></textarea>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <!-- Status -->
                                    <div>
                                        <label for="status_id" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Status</label>
                                        <select name="status_id" id="status_id" 
                                                class="mt-1 block w-full py-2.5 px-3 border border-gray-300 bg-white rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-cuan-green/20 focus:border-cuan-green text-sm transition-all">
                                            @foreach($statuses as $status)
                                                <option value="{{ $status->id }}">{{ $status->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Priority -->
                                    <div>
                                        <label for="priority" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Prioritas</label>
                                        <select name="priority" id="priority" 
                                                class="mt-1 block w-full py-2.5 px-3 border border-gray-300 bg-white rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-cuan-green/20 focus:border-cuan-green text-sm transition-all">
                                            <option value="low">Rendah</option>
                                            <option value="medium" selected>Sedang</option>
                                            <option value="high">Tinggi</option>
                                        </select>
                                    </div>
                                </div>

                                @can('tasks.assign')
                                <!-- Assignees -->
                                <div>
                                    <label for="assignees" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Assign ke</label>
                                    <select name="assignees[]" id="assignees" multiple 
                                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-cuan-green/20 focus:border-cuan-green text-sm transition-all">
                                        @foreach($assignableUsers as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->roles->first()->name ?? 'User' }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endcan

                                @can('task-labels.manage')
                                <!-- Labels -->
                                <div>
                                    <label for="labels" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Label</label>
                                    <select name="labels[]" id="labels" multiple 
                                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-cuan-green/20 focus:border-cuan-green text-sm transition-all">
                                        @foreach($labels as $label)
                                            <option value="{{ $label->id }}">{{ $label->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endcan

                                <!-- Deadline -->
                                <div>
                                    <label for="deadline" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Deadline</label>
                                    <input type="datetime-local" name="deadline" id="deadline" 
                                           class="mt-1 focus:ring-2 focus:ring-cuan-green/20 focus:border-cuan-green block w-full shadow-sm text-sm border-gray-300 rounded-lg py-2.5 transition-all">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50/50 px-6 py-4 sm:flex sm:flex-row-reverse gap-3">
                <button type="button" id="btnCreateTask" onclick="submitCreateTask()" 
                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-5 py-2.5 bg-cuan-green text-sm font-black text-white hover:bg-cuan-dark focus:outline-none transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                    Simpan Task
                </button>
                <button type="button" onclick="closeCreateModal()" 
                        class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-200 shadow-sm px-5 py-2.5 bg-white text-sm font-black text-gray-700 hover:bg-gray-50 focus:outline-none transition-all sm:mt-0">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let createAssigneesChoice, createLabelsChoice;

    function openCreateModal(statusId = null) {
        document.getElementById('createTaskModal').classList.remove('hidden');
        document.getElementById('createTaskForm').reset();
        
        if(statusId) {
            document.getElementById('status_id').value = statusId;
        }

        // Initialize Choices.js if not already
        if (!createAssigneesChoice) {
            createAssigneesChoice = new Choices('#assignees', { removeItemButton: true, placeholderValue: 'Pilih Assignee...' });
        }
        if (!createLabelsChoice) {
            createLabelsChoice = new Choices('#labels', { removeItemButton: true, placeholderValue: 'Pilih Label...' });
        }
    }

    function closeCreateModal() {
        document.getElementById('createTaskModal').classList.add('hidden');
    }

    function submitCreateTask() {
        const form = document.getElementById('createTaskForm');
        const formData = new FormData(form);
        const btn = document.getElementById('btnCreateTask');
        
        // Show Loading
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-2"></i>Menyimpan...';

        fetch('{{ route('tasks.store') }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Berhasil', data.message, 'success').then(() => {
                    location.reload(); // Simple reload for now
                });
                closeCreateModal();
            } else {
                Swal.fire('Error', data.message || 'Gagal membuat task', 'error');
                // Reset Button
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Terjadi kesalahan jaringan', 'error');
            // Reset Button
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }
</script>
