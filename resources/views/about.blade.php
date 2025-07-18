@extends('layouts.app')

@section('title', 'Tentang Aplikasi Stemming Bahasa Indonesia')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Tentang Aplikasi Stemming Bahasa Indonesia</h1>

    <p>
        Aplikasi ini dibangun berdasarkan pada <strong>Algoritma Enhanced Confix Stripping</strong> 
        untuk mereduksi kata imbuhan menjadi kata dasarnya. 
        Ada dua fitur utama pada aplikasi ini:
        <ol>
            <li>Stemming untuk satu buah kata imbuhan</li>
            <li>Bulk-stemming, untuk stemming teks dalam berkas dengan format docx dan pdf</li>
        </ol>
    </p>

    <p>
        Langkah-langkah pada bulk stemming:
    </p>
    <ol>
        <li>Pengguna memasukkan berkas (docx atau pdf)</li>
        <li>Pre-processing untuk teks di dalam berkas (HTML tags, angka, symbols, regex, dan stopwords).</li>
        <li>Tokenisasi setiap huruf lalu dilakukan stemming</li>
        <li>Jika hasil stemming cocok dengan kata dalam kamus maka hasil stemming dinyatakan <strong>valid</strong>.</li>
        <li>Jika tidak, maka <strong>tidak valid</strong>.</li>
    </ol>

    <p>
        Aplikasi ini dapat digunakan untuk:
        <ol>
            <li>Pra-pemrosesan pada Natural Language Processing, aplikasi ini dilengkapi dengan fitur bulk-stemming dan export ke dalam format .csv</li>
            <li>Edukasi bahasa Indonesia dengan tampilan UI yang sederhana dan mudah digunakan</li>
        </ol>

    </p>
</div>
@endsection
