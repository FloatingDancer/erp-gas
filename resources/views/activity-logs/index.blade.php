@extends('layouts.app')
@section('content')
<style>
.page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; }
.page-title { font-size:22px; font-weight:700; color:#0f172a; margin:0; }
.page-subtitle { font-size:13px; color:#64748b; margin:2px 0 0; }
.card-clean { background:white; border-radius:16px; border:none; box-shadow:0 1px 4px rgba(0,0,0,0.06); }
.tbl-wrap { overflow-x:auto; }
table.modern-table { width:100%; border-collapse:collapse; }
table.modern-table thead tr { background:#f8fafc; border-bottom:2px solid #e2e8f0; }
table.modern-table thead th { padding:12px 16px; font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.6px; color:#64748b; white-space:nowrap; }
table.modern-table tbody tr { border-bottom:1px solid #f1f5f9; transition:background 0.12s; }
table.modern-table tbody tr:hover { background:#f8fafc; }
table.modern-table tbody td { padding:13px 16px; font-size:13.5px; color:#374151; vertical-align:middle; }
.badge-pill { display:inline-block; padding:3px 10px; border-radius:20px; font-size:11.5px; font-weight:600; }
.badge-green { background:#dcfce7; color:#15803d; }
.badge-yellow { background:#fef9c3; color:#854d0e; }
.badge-red { background:#fee2e2; color:#b91c1c; }
.badge-blue { background:#dbeafe; color:#1d4ed8; }
.empty-state { text-align:center; padding:60px 20px; color:#94a3b8; }
.empty-state .empty-icon { font-size:48px; margin-bottom:12px; }
.empty-state p { font-size:14px; margin:0; }
.pagination-wrap { padding: 16px; display: flex; justify-content: center; }
</style>

<div class="page-header">
    <div>
        <h1 class="page-title"><i data-lucide="clipboard-list" style="width:22px;height:22px;vertical-align:middle;margin-top:-4px;margin-right:4px;"></i> Log Aktivitas</h1>
        <p class="page-subtitle">Daftar lengkap riwayat aktivitas sistem</p>
    </div>
    <a href="{{ route('dashboard') }}" style="display:inline-flex; align-items:center; gap:6px; background:#f1f5f9; color:#374151; border:none; padding:9px 18px; border-radius:10px; font-size:13.5px; font-weight:600; text-decoration:none; cursor:pointer; transition:background 0.15s;"><i data-lucide="arrow-left" style="width:15px;height:15px;margin-right:2px;"></i> Kembali</a>
</div>

<div class="card-clean">
    <div class="tbl-wrap">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>Waktu Aktivitas</th>
                    <th>Aksi</th>
                    <th>Detail Keterangan</th>
                    <th>Dilakukan Oleh</th>
                </tr>
            </thead>
            <tbody>
            @forelse($logs as $log)
                <tr>
                    <td style="color:#64748b; white-space:nowrap;">
                        {{ $log->created_at->format('H:i:s · d M Y') }}
                        <span style="font-size:11px; color:#94a3b8; margin-left:6px;">({{ $log->created_at->diffForHumans() }})</span>
                    </td>
                    <td>
                        @if($log->action === 'Create')
                            <span class="badge-pill badge-green"><i data-lucide="plus-circle" style="width:12px;height:12px;margin-right:3px;vertical-align:middle;margin-top:-2px;"></i> Create</span>
                        @elseif($log->action === 'Update')
                            <span class="badge-pill badge-yellow"><i data-lucide="edit-3" style="width:12px;height:12px;margin-right:3px;vertical-align:middle;margin-top:-2px;"></i> Update</span>
                        @elseif($log->action === 'Delete')
                            <span class="badge-pill badge-red"><i data-lucide="minus-circle" style="width:12px;height:12px;margin-right:3px;vertical-align:middle;margin-top:-2px;"></i> Delete</span>
                        @else
                            <span class="badge-pill badge-blue"><i data-lucide="info" style="width:12px;height:12px;margin-right:3px;vertical-align:middle;margin-top:-2px;"></i> {{ $log->action }}</span>
                        @endif
                    </td>
                    <td style="font-weight:500; color:#374151; max-width: 500px; word-break: break-word;">
                        @if(strlen($log->description) > 150)
                            <span id="desc-short-{{ $log->id }}">{{ Str::limit($log->description, 150, '...') }}</span>
                            <span id="desc-full-{{ $log->id }}" style="display: none;">{{ $log->description }}</span>
                            <button type="button" onclick="toggleDesc({{ $log->id }})" id="btn-toggle-{{ $log->id }}" style="background: none; border: none; color: #2563eb; font-size: 11.5px; font-weight: 600; padding: 0 4px; cursor: pointer; display: inline-block;">See More</button>
                        @else
                            {{ $log->description }}
                        @endif
                    </td>
                    <td style="color:#64748b;">
                        <span style="font-weight:600; color:#0f172a;">{{ $log->user->name ?? 'System' }}</span>
                        <div style="font-size:11px; color:#94a3b8;">{{ $log->user->email ?? '' }}</div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">
                        <div class="empty-state">
                            <div class="empty-icon" style="display:flex;justify-content:center;margin-bottom:12px;"><i data-lucide="clipboard-list" style="width:48px;height:48px;stroke-width:1.5;color:#94a3b8;"></i></div>
                            <p>Belum ada riwayat aktivitas</p>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    
    @if($logs->hasPages())
        <div class="pagination-wrap">
            {{ $logs->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

<script>
    function toggleDesc(id) {
        const shortSpan = document.getElementById('desc-short-' + id);
        const fullSpan = document.getElementById('desc-full-' + id);
        const btn = document.getElementById('btn-toggle-' + id);
        
        if (fullSpan.style.display === 'none') {
            fullSpan.style.display = 'inline';
            shortSpan.style.display = 'none';
            btn.innerText = 'Show Less';
        } else {
            fullSpan.style.display = 'none';
            shortSpan.style.display = 'inline';
            btn.innerText = 'See More';
        }
    }
</script>
@endsection
