<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand fw-bold text-light text-wrap" href="{{ route('stem.form') }}">
            <span class="d-block d-lg-inline fs-6 fs-md-5 fs-lg-4">
                Aplikasi Stemming Bahasa Indonesia
            </span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }} text-wrap" href="{{ route('about') }}">
                        <span class="d-block d-lg-inline fs-6 fs-md-6 fs-lg-6">
                            Tentang Aplikasi
                        </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('stem.form') ? 'active' : '' }} text-wrap" href="{{ route('stem.form') }}">
                        <span class="d-block d-lg-inline fs-6 fs-md-6 fs-lg-6">
                            Kata Tunggal
                        </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('bulk.form') ? 'active' : '' }} text-wrap" href="{{ route('bulk.form') }}">
                        <span class="d-block d-lg-inline fs-6 fs-md-6 fs-lg-6">
                            Banyak Kata
                        </span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
