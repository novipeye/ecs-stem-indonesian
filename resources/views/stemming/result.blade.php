@extends('layouts.app')

@section('title', 'Hasil Stemming')

@section('content')
<div class="container d-flex justify-content-center">
    <div class="card shadow-sm w-100" style="max-width: 720px;">
        <div class="card-body p-4">
            <h4 class="mb-4 text-center" style="color: #254D70;">Hasil Stemming</h4>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="fw-semibold" style="color: #954C2E;">Jumlah Kata: {{ count($results) }}</span>
                <div class="mb-3">
                    <strong>Ringkasan:</strong><br>
                    Kata Valid: {{ $validCount }}<br>
                    Kata Tidak Valid: {{ $invalidCount }}
                </div>
                <a href="{{ route('bulk.export') }}" class="btn btn-primary">
                    Unduh ke CSV
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Kata Asli</th>
                            <th>Hasil Stemming</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $row)
                            <tr>
                                <td>{{ (string) $row['original'] }}</td>
                                <td style="color: #131D4F;"><strong>{{ (string) $row['stemmed'] }}</strong></td>
                                <td>
                                    @if($row['status'] === 'Valid')
                                        <span style="color: #254D70;">Valid</span>
                                    @else
                                        <span style="color: #954C2E;">Not Valid</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="text-end mt-4">
                <a href="{{ route('bulk.form') }}" class="btn btn-outline-secondary">
                    Unggah Berkas Baru
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
