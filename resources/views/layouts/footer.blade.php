<footer class="py-4 text-center text-light" style="background-color: #131D4F;">
    <div class="container">
        <p class="mb-2 small text-wrap" style="color: #EFE4D2;">
            &copy; {{ date('Y') }} Aplikasi Stemming Bahasa Indonesia
        </p>
        <p class="mb-0 small text-wrap" style="color: #EFE4D2;">
            <a href="https://github.com/novipeye/ecs-stem-indonesian" class="text-decoration-none me-2" style="color: #EFE4D2;" target="_blank">
                GitHub
            </a>
            <span class="d-none d-sm-inline">&middot;</span>
            <a href="{{ route('stem.form') }}" class="text-decoration-none mx-2" style="color: #EFE4D2;">
                Beranda
            </a>
            <span class="d-none d-sm-inline">&middot;</span>
            <a href="{{ route('bulk.form') }}" class="text-decoration-none ms-2" style="color: #EFE4D2;">
                Bulk
            </a>
        </p>
    </div>
</footer>
