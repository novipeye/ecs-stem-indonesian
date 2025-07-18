@extends('layouts.app')

@section('title', 'Kata Tunggal')

@section('content')
<div class="d-flex justify-content-center align-items-center" style="min-height: 50vh;">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6">
                <div class="card shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h4 class="mb-4 text-center" style="color: #254D70;">Cari Kata Dasar</h4>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0 small">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Form --}}
                        <form method="POST" action="{{ route('stem.process') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="word" class="form-label">Masukkan Kata</label>
                                <input type="text" name="word" id="word" class="form-control"
                                    placeholder="misal: menyelesaikan"
                                    pattern="^[a-zA-Z]+$" required
                                    value="{{ isset($root) ? '' : old('word') }}">
                            </div>

                            <div class="row">
                                <div class="d-flex flex-column flex-md-row justify-content-center gap-3">
                                <button type="submit" class="btn btn-primary">Proses</button>
                                <button type="reset" class="btn btn-outline-secondary">Reset</button>
                            </div>

                            </div>
                        </form>

                        {{-- Result --}}
                        @isset($root)
                            <hr>
                            <div class="mt-3 small">
                                <p><strong>Kata Masukkan:</strong> {{ $original }}</p>
                                <p><strong>Kata Dasar:</strong> <span style="color: #954C2E;">{{ $root }}</span></p>
                                <p><strong>Status:</strong> {{ $status }}</p>
                            </div>
                        @endisset
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
