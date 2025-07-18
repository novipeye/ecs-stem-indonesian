@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center">
    <div class="card shadow-sm w-100" style="max-width: 720px;">
        <div class="card-body p-4">
            <h2>Dokumen (Docx atau Pdf)</h2>
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <form action="{{ route('bulk.process') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="file" class="form-label">Unggah Dokumen:</label>
                    <input type="file" name="file" class="form-control" accept=".pdf,.docx" required>
                </div>
                <button type="submit" class="btn btn-primary">Proses</button>
            </form>
        </div>
    </div>
</div>
@endsection
