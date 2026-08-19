@extends('layouts.link')

@section('content')
    <!-- Added Google Fonts for Fahkwang -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fahkwang:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --soma-cream: #FFF7E9;
            --soma-beige: #BE9676;
            --soma-taupe: #8D7E71;
            --text-dark: #3A332C;
            --text-light: #F8F5F2;
        }

        /* --- Global Font Settings --- */
        body, h1, h2, h3, h4, h5, h6, p, span, a, div, button, input, textarea, label {
            font-family: 'Fahkwang', sans-serif !important;
        }

        .profile-page {
            background: radial-gradient(circle at top right, #ffffff, var(--soma-cream));
            min-height: 100vh;
            padding: 60px 0;
            font-family: 'Fahkwang', sans-serif !important;
        }

        .profile-header {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(255, 247, 233, 0.4));
            backdrop-filter: blur(10px);
            border-radius: 30px;
            padding: 50px 40px;
            text-align: center;
            margin-bottom: 40px;
            border: 1px solid rgba(190, 150, 118, 0.2);
            box-shadow: 0 20px 40px rgba(141, 126, 113, 0.05);
        }

        /* Avatar Styles with integrated Theme Edit Button */
        .profile-avatar-wrapper {
            position: relative;
            width: 110px;
            height: 110px;
            margin: 0 auto 25px;
            cursor: pointer;
        }

        .profile-avatar {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--soma-beige), var(--soma-taupe));
            color: var(--text-light);
            font-size: 32px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 25px rgba(182, 150, 118, 0.2);
            border: 4px solid #fff;
            overflow: hidden;
            object-fit: cover;
        }

        .avatar-edit-btn {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: var(--soma-beige);
            color: #ffffff;
            border: 3px solid #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            transition: background-color 0.2s ease, transform 0.2s ease;
        }

        .profile-avatar-wrapper:hover .avatar-edit-btn {
            background-color: var(--soma-taupe);
            transform: scale(1.05);
        }

        /* Preview Image Style in Modal */
        #avatar-preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--soma-beige);
            box-shadow: 0 5px 15px rgba(182, 150, 118, 0.2);
        }

        .profile-card {
            background: #ffffff;
            border-radius: 28px;
            padding: 40px;
            border: 1px solid rgba(190, 150, 118, 0.15);
            box-shadow: 0 15px 35px rgba(141, 126, 113, 0.04);
            height: 100%;
        }

        .profile-name {
            color: var(--text-dark);
            font-weight: 700;
            margin-bottom: 10px;
        }

        .profile-email {
            color: var(--soma-taupe);
            font-size: 0.95rem;
        }

        .section-title {
            font-size: 12px;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--soma-beige);
            font-weight: 800;
            margin-bottom: 35px;
            display: flex;
            align-items: center;
        }

        .section-title::after {
            content: '';
            flex-grow: 1;
            margin-left: 15px;
            height: 1px;
            background: linear-gradient(to right, rgba(190, 150, 118, 0.25), transparent);
        }

        .practice-badge {
            display: inline-block;
            background: rgba(190, 150, 118, 0.1);
            color: var(--soma-taupe);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin: 2px;
            border: 1px solid rgba(190, 150, 118, 0.2);
        }

        .info-box {
            padding: 15px;
            border-radius: 20px;
            background: #fdfaf7;
            border: 1px solid rgba(190, 150, 118, 0.1);
            transition: all 0.3s ease;
        }

        .info-box:hover {
            background: #fff;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.03);
        }

        .premium-input {
            border-radius: 16px;
            border: 1px solid rgba(190, 150, 118, 0.25);
            padding: 14px 18px;
            min-height: 54px;
            font-size: 15px;
            background-color: rgba(255, 247, 233, 0.15);
        }

        .premium-input:focus {
            border-color: var(--soma-beige);
            box-shadow: 0 0 0 0.25rem rgba(190, 150, 118, 0.15);
            outline: 0;
        }

        .btn-soma {
            background: var(--soma-taupe);
            color: white;
            border-radius: 50px;
            padding: 14px 32px;
            font-weight: 700;
            text-transform: uppercase;
            border: none;
            transition: all 0.2s;
        }

        .btn-soma:hover {
            background: var(--soma-beige);
            color: white;
        }

        .btn-outline-soma {
            border: 2px solid var(--soma-beige);
            color: var(--soma-beige);
            background: transparent;
            border-radius: 50px;
            padding: 13px 32px;
            font-weight: 700;
            text-transform: uppercase;
            transition: all 0.2s;
        }

        .btn-outline-soma:hover {
            background: var(--soma-beige);
            color: white;
        }

        .modal-content-soma {
            border-radius: 24px;
            border: 1px solid rgba(190, 150, 118, 0.2);
            background: #fff;
        }
    </style>

    <div class="profile-page">
        <div class="container">
            {{-- Global Notifications --}}
            @if(session('success-avatar'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    {{ session('success-avatar') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="profile-header">
                {{-- Updated Avatar Element with Theme-colored Pencil Button Overlay --}}
                <div class="profile-avatar-wrapper" data-bs-toggle="modal" data-bs-target="#avatarModal"
                    title="Change Profile Picture">
                    @if(auth()->user()->avatar)
                        <img src="{{ asset('uploads/' . auth()->user()->avatar) }}" class="profile-avatar" alt="Avatar">
                    @else
                        <div class="profile-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}</div>
                    @endif
                    <div class="avatar-edit-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            viewBox="0 0 16 16">
                            <path
                                d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z" />
                        </svg>
                    </div>
                </div>
                <h3 class="profile-name">{{ auth()->user()->name }}</h3>
                <p class="profile-email">phone - {{ auth()->user()->phone }}</p>
            </div>

            <div class="row g-4">
                <div class="col-12">
                    <div class="profile-card">
                        <div class="section-title">Your Practice Profile</div>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="info-box"><label class="form-label" style="font-size: 10px;">Level</label>
                                    <div class="fw-bold" style="color: var(--text-dark);">
                                        {{ $user->starting_level ?? 'Not set' }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box"><label class="form-label" style="font-size: 10px;">Practices</label>
                                    <div>@forelse($user->included_practices ?? [] as $p)<span
                                    class="practice-badge">{{ $p }}</span>@empty<span
                                            class="text-muted small">None</span>@endforelse</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box"><label class="form-label" style="font-size: 10px;">Times</label>
                                    <div>@forelse($user->preferred_times ?? [] as $t)<span
                                    class="practice-badge">{{ $t }}</span>@empty<span
                                            class="text-muted small">None</span>@endforelse</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box"><label class="form-label"
                                        style="font-size: 10px;">Considerations</label>
                                    <div>@forelse($user->considerations ?? [] as $c)<span
                                    class="practice-badge">{{ $c }}</span>@empty<span
                                            class="text-muted small">None</span>@endforelse</div>
                                </div>
                            </div>
                             <div class="col-md-3">
                                <div class="info-box"><label class="form-label"
                                        style="font-size: 10px;">Know Where</label>
                                    <div><span
                                    class="practice-badge">{{ $user->know_where ?? 'Not set' }}</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="profile-card">
                        <div class="section-title">Profile Information</div>
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        <form action="{{ route('update.profile') }}" method="POST">
                            @csrf
                            <div class="mb-4"><label class="form-label"
                                    style="color: var(--text-dark); font-weight:600;">Full Name</label><input name="name"
                                    type="text" class="form-control premium-input" value="{{ auth()->user()->name }}"></div>
                            <div class="mb-4"><label class="form-label"
                                    style="color: var(--text-dark); font-weight:600;">Phone Number</label><input
                                    name="phone" type="text" class="form-control premium-input"
                                    value="{{ auth()->user()->phone }}">
                            </div>
                            <button type="submit" class="btn btn-soma w-100">Save Changes</button>
                        </form>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="profile-card">
                        <div class="section-title">Change Password</div>
                        @if(session('success-password'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success-password') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($errors->hasBag('password') && $errors->getBag('password')->isNotEmpty())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    @foreach($errors->password->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        <form action="{{ route('update.password') }}" method="POST">
                            @csrf
                            <div class="mb-3"><label class="form-label"
                                    style="color: var(--text-dark); font-weight:600;">Current Password</label><input
                                    name="current_password" type="password" class="form-control premium-input"
                                    placeholder="••••••••"></div>
                            <div class="mb-3"><label class="form-label"
                                    style="color: var(--text-dark); font-weight:600;">New Password</label><input
                                    name="new_password" type="password" class="form-control premium-input"
                                    placeholder="Minimum 8 characters">
                            </div>
                            <div class="mb-4"><label class="form-label"
                                    style="color: var(--text-dark); font-weight:600;">Confirm New Password</label><input
                                    name="new_password_confirmation" type="password" class="form-control premium-input"
                                    placeholder="Repeat new password"></div>
                            <button type="submit" class="btn btn-outline-soma w-100">Update Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bootstrap Modal for Updating Profile Picture --}}
    <div class="modal fade" id="avatarModal" tabindex="-1" aria-labelledby="avatarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-content-soma p-3">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="avatarModalLabel" style="color: var(--text-dark);">Update Profile
                        Picture</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('update.avatar') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body text-center">
                        {{-- Live Preview Target Area --}}
                        <div class="mb-4 d-flex justify-content-center">
                            @if(auth()->user()->avatar)
                                <img id="avatar-preview" src="{{ asset('uploads/' . auth()->user()->avatar) }}" alt="Preview">
                            @else
                                <div id="avatar-preview-fallback" class="profile-avatar"
                                    style="width: 120px; height: 120px; font-size: 38px;">
                                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                                </div>
                                <img id="avatar-preview" src="#" alt="Preview" class="d-none">
                            @endif
                        </div>

                        <div class="mb-3 text-start">
                            <label for="avatar-input" class="form-label"
                                style="font-size: 13px; font-weight: 600; color: var(--soma-taupe);">Choose an image</label>
                            <input class="form-control premium-input" type="file" id="avatar-input" name="avatar"
                                accept="image/*" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-0 gap-2">
                        <button type="button" class="btn btn-light rounded-pill px-4"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-soma">Upload Image</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Live Image Preview Script Logic --}}
    <script>
        document.getElementById('avatar-input').addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const previewImg = document.getElementById('avatar-preview');
                    const previewFallback = document.getElementById('avatar-preview-fallback');

                    if (previewImg) {
                        previewImg.src = e.target.result;
                        previewImg.classList.remove('d-none');
                    }
                    if (previewFallback) {
                        previewFallback.classList.add('d-none');
                    }
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
@endsection