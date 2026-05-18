@extends('layouts.app')
@section('title', 'Profil Saya - Perpus Sandikta')
@section('page-title', 'Profil Saya')

@section('content')
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card-modern animate-fadeInUp delay-1">
                <div style="height:120px;background:var(--primary-gradient);position:relative">
                    <div style="position:absolute;bottom:-40px;left:32px">
                        <div
                            style="width:100px;height:100px;border-radius:20px;background:#fff;display:flex;align-items:center;justify-content:center;border:4px solid #fff;box-shadow:var(--shadow-lg);overflow:hidden">
                            <img src="{{ $user->avatar_url }}" id="avatarPreview"
                                style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <button type="button" class="btn btn-sm btn-primary-modern"
                            style="position:absolute;bottom:-5px;right:-5px;padding:5px;border-radius:50%;width:32px;height:32px;display:flex;align-items:center;justify-content:center;box-shadow:var(--shadow-md)"
                            onclick="document.getElementById('avatarInput').click()">
                            <i class="bi bi-camera-fill"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body" style="padding:56px 32px 32px">
                    <form action="{{ route('profile.update-avatar') }}" method="POST" enctype="multipart/form-data"
                        id="avatarForm" class="mb-4">
                        @csrf
                        <input type="file" name="avatar" id="avatarInput" hidden accept="image/*"
                            onchange="previewAvatar(this)">
                        <div id="avatarActions" style="display: none;" class="mt-2 animate-fadeInUp">
                            <button type="submit" class="btn btn-sm btn-success-modern"><i
                                    class="bi bi-check-lg me-1"></i>Simpan Foto</button>
                            <button type="button" class="btn btn-sm btn-outline-modern" onclick="resetAvatar()"><i
                                    class="bi bi-x-lg me-1"></i>Batal</button>
                        </div>
                        @if($user->avatar)
                            <div id="deleteAvatarBtn" class="mt-2">
                                <button type="button" class="btn btn-sm btn-link text-danger p-0"
                                    onclick="confirmDeleteAvatar()"><i class="bi bi-trash me-1"></i>Hapus Foto</button>
                            </div>
                            <form action="{{ route('profile.delete-avatar') }}" method="POST" id="deleteAvatarForm"
                                class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endif
                    </form>
                    <h4 style="font-weight:800">{{ $user->name }}</h4>
                    <span class="badge-modern badge-info mb-3" style="text-transform:capitalize">{{ $user->role }}</span>

                    <div class="mt-3">
                        @if($user->nis)
                            <div class="d-flex align-items-center gap-3 py-2 border-bottom">
                                <i class="bi bi-hash text-primary"></i>
                                <div><small class="text-muted">NIS</small>
                                    <div style="font-weight:600">{{ $user->nis }}</div>
                                </div>
                            </div>
                        @endif
                        @if($user->email)
                            <div class="d-flex align-items-center gap-3 py-2 border-bottom">
                                <i class="bi bi-envelope text-primary"></i>
                                <div><small class="text-muted">Email</small>
                                    <div style="font-weight:600">{{ $user->email }}</div>
                                </div>
                            </div>
                        @endif
                        @if($user->kelas)
                            <div class="d-flex align-items-center gap-3 py-2 border-bottom">
                                <i class="bi bi-mortarboard text-primary"></i>
                                <div><small class="text-muted">Kelas</small>
                                    <div style="font-weight:600">{{ $user->kelas }}</div>
                                </div>
                            </div>
                        @endif
                        @if($user->tanggal_lahir)
                            <div class="d-flex align-items-center gap-3 py-2 border-bottom">
                                <i class="bi bi-calendar text-primary"></i>
                                <div><small class="text-muted">Tanggal Lahir</small>
                                    <div style="font-weight:600">{{ $user->tanggal_lahir->translatedFormat('d F Y') }}</div>
                                </div>
                            </div>
                        @endif
                        <div class="d-flex align-items-center gap-3 py-2">
                            <i class="bi bi-clock text-primary"></i>
                            <div><small class="text-muted">Login Terakhir</small>
                                <div style="font-weight:600">{{ $user->last_login_at?->format('d/m/Y H:i') ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card-modern animate-fadeInUp delay-2">
                <div class="card-header">
                    <h6><i class="bi bi-key me-2 text-warning"></i>Ubah Password</h6>
                </div>
                <div class="card-body" style="padding:32px">
                    @if($errors->any())
                        <div class="alert alert-danger"
                            style="border-radius:12px;border:none;background:#fee2e2;color:#991b1b;font-size:13px">
                            <i class="bi bi-exclamation-circle me-2"></i>{{ $errors->first() }}
                        </div>
                    @endif
                    <form method="POST" action="{{ route('profile.change-password') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" style="font-weight:600;font-size:13px">Password Saat Ini</label>
                            <input type="password" name="current_password" class="form-control form-control-modern"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" style="font-weight:600;font-size:13px">Password Baru</label>
                            <input type="password" name="password" class="form-control form-control-modern" required>
                            <small class="text-muted">Minimal 8 karakter</small>
                        </div>
                        <div class="mb-4">
                            <label class="form-label" style="font-weight:600;font-size:13px">Konfirmasi Password
                                Baru</label>
                            <input type="password" name="password_confirmation" class="form-control form-control-modern"
                                required>
                        </div>
                        <button type="submit" class="btn btn-primary-modern"><i class="bi bi-check-lg me-1"></i>Ubah
                            Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const preview = document.getElementById('avatarPreview');
                    preview.src = e.target.result;

                    document.getElementById('avatarActions').style.display = 'flex';
                    document.getElementById('avatarActions').style.gap = '8px';
                    if (document.getElementById('deleteAvatarBtn')) {
                        document.getElementById('deleteAvatarBtn').style.display = 'none';
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function resetAvatar() {
            const input = document.getElementById('avatarInput');
            const preview = document.getElementById('avatarPreview');
            const originalAvatar = "{{ $user->avatar_url }}";

            input.value = '';
            preview.src = originalAvatar;

            document.getElementById('avatarActions').style.display = 'none';
            if (document.getElementById('deleteAvatarBtn')) {
                document.getElementById('deleteAvatarBtn').style.display = 'block';
            }
        }

        function confirmDeleteAvatar() {
            Swal.fire({
                title: 'Hapus foto profil?',
                text: "Foto akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteAvatarForm').submit();
                }
            })
        }
    </script>
@endpush