<!-- Detail Task Modal -->
<div id="detailTaskModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeDetailModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                <!-- Header -->
                <div class="flex flex-col sm:flex-row justify-between items-start border-b border-gray-100 pb-6 mb-6 gap-4">
                    <div class="space-y-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <span id="detail_status_badge" class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider">Status</span>
                            <span id="detail_priority_badge" class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider">Priority</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 tracking-tight" id="detail_title">Task Title</h3>
                    </div>
                    @can('tasks.update')
                    <button onclick="switchToEditMode()" class="flex items-center gap-2 px-3 py-1.5 rounded-lg border border-gray-200 text-gray-500 hover:text-blue-600 hover:bg-blue-50 transition-all text-xs font-semibold">
                        <i class="fa-solid fa-pen-to-square"></i>
                        <span>Edit Tugas</span>
                    </button>
                    @endcan
                </div>

                <div class="flex flex-col lg:flex-row gap-8">
                    <!-- Main Content (Description & Activity) -->
                    <div class="flex-grow space-y-8 order-2 lg:order-1">
                        <div>
                            <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-4">Deskripsi</h4>
                            <div id="detail_description" class="text-[13px] text-gray-600 leading-relaxed whitespace-pre-wrap selection:bg-purple-100"></div>
                        </div>

                        <div>
                            <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-6">Aktivitas & Riwayat</h4>
                            <div class="flow-root">
                                <ul role="list" class="-mb-8" id="detail_activities">
                                    <!-- Log Activity Items injected via JS -->
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar (Meta Info) - Stacks on bottom on mobile, side on desktop -->
                    <div class="w-full lg:w-56 space-y-6 order-1 lg:order-2 shrink-0 border-b lg:border-b-0 lg:border-l border-gray-100 pb-6 lg:pb-0 lg:pl-6">
                        @can('tasks.assign')
                        <div>
                            <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Assignees</h4>
                            <div id="detail_assignees" class="flex flex-wrap gap-1.5"></div>
                        </div>
                        @endcan
                        @can('task-labels.manage')
                        <div>
                            <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Labels</h4>
                            <div id="detail_labels" class="flex flex-wrap gap-1.5"></div>
                        </div>
                        @endcan
                        <div>
                            <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Deadline</h4>
                            <p id="detail_deadline" class="text-[12px] font-semibold text-gray-700"></p>
                        </div>
                        <div>
                            <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Dibuat Oleh</h4>
                            <p id="detail_creator" class="text-[12px] font-medium text-gray-500"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="closeDetailModal()" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentDetailTaskId = null;

    function openDetailModal(taskId) {
        currentDetailTaskId = taskId;
        document.getElementById('detailTaskModal').classList.remove('hidden');

        // Loading state
        document.getElementById('detail_title').innerText = 'Loading...';

        fetch(`/tasks/${taskId}`)
            .then(res => res.json())
            .then(task => {
                // Header
                document.getElementById('detail_title').innerText = task.title;
                document.getElementById('detail_description').innerText = task.description || 'Tidak ada deskripsi.';
                
                // Status Badge
                const statusBadge = document.getElementById('detail_status_badge');
                statusBadge.innerText = task.status.name;
                statusBadge.style.backgroundColor = task.status.color + '20'; // 20% opacity
                statusBadge.style.color = task.status.color;
                
                // Priority Badge
                const priorityBadge = document.getElementById('detail_priority_badge');
                priorityBadge.innerText = task.priority_label;
                // Assign classes based on priority (simplified)
                priorityBadge.className = `px-2 py-0.5 rounded-full text-xs font-semibold bg-${task.priority_color}-100 text-${task.priority_color}-700`;

                // Assignees
                const assigneesContainer = document.getElementById('detail_assignees');
                assigneesContainer.innerHTML = '';
                if(task.assignees.length > 0) {
                    task.assignees.forEach(u => {
                        assigneesContainer.innerHTML += `
                            <div class="flex items-center gap-1.5 bg-gray-100 rounded-full px-2 py-1">
                                <span class="bg-purple-200 text-purple-700 text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center">${u.name.substring(0,2).toUpperCase()}</span>
                                <span class="text-xs text-gray-700">${u.name}</span>
                            </div>`;
                    });
                } else {
                    assigneesContainer.innerHTML = '<span class="text-xs text-gray-500 italic">Belum ada</span>';
                }

                // Labels
                const labelsContainer = document.getElementById('detail_labels');
                labelsContainer.innerHTML = '';
                if(task.labels.length > 0) {
                    task.labels.forEach(l => {
                        labelsContainer.innerHTML += `
                            <span class="text-xs px-2 py-1 rounded-md text-white" style="background-color: ${l.color}">${l.name}</span>`;
                    });
                } else {
                    labelsContainer.innerHTML = '<span class="text-xs text-gray-500 italic">-</span>';
                }

                // Meta
                document.getElementById('detail_deadline').innerText = task.deadline ? new Date(task.deadline).toLocaleString() : '-';
                document.getElementById('detail_creator').innerText = task.creator.name;

                // Activities
                const activitiesContainer = document.getElementById('detail_activities');
                activitiesContainer.innerHTML = '';
                
                if (task.activities && task.activities.length > 0) {
                    task.activities.forEach((activity, index) => {
                        const isLast = index === task.activities.length - 1;
                        const date = new Date(activity.created_at).toLocaleString();
                        
                        activitiesContainer.innerHTML += `
                            <li>
                                <div class="relative pb-4">
                                    ${!isLast ? '<span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>' : ''}
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center ring-8 ring-white">
                                                <i class="fa-solid fa-clock-rotate-left text-gray-500 text-xs"></i>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-500">${activity.description}</p>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                <time datetime="${activity.created_at}">${date}</time>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        `;
                    });
                } else {
                    activitiesContainer.innerHTML = '<li class="text-sm text-gray-500 italic">Belum ada aktivitas.</li>';
                }
            });
    }

    function closeDetailModal() {
        document.getElementById('detailTaskModal').classList.add('hidden');
    }

    function switchToEditMode() {
        closeDetailModal();
        if(currentDetailTaskId) {
            openEditModal(currentDetailTaskId);
        }
    }
</script>
