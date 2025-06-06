{{-- <!DOCTYPE html>
<html>
<head>
    <title>CS Stemmer</title>
    <script>
        function validateForm(event) {
            const wordInput = document.querySelector('input[name="word"]');
            const word = wordInput.value.trim();
            const errorDiv = document.getElementById('error');

            // Clear previous error
            errorDiv.textContent = '';

            // Regex: reject numbers, multiple words, or special characters
            const invalidPattern = /[^a-zA-Z]/;

            if (word.length < 4) {
                errorDiv.textContent = "Minimal word length is four characters.";
                event.preventDefault();
                return false;
            }

            if (invalidPattern.test(word)) {
                errorDiv.textContent = "Only one word with alphabetic characters is allowed.";
                event.preventDefault();
                return false;
            }

            return true;
        }
    </script>
</head>
<body>
    <h1>Confix Stripping Stemmer</h1>

    <div id="error" style="color: red;">
        @error('word') {{ $message }} @enderror
    </div>

    <form method="POST" action="{{ route('stem.process') }}" onsubmit="return validateForm(event)">
        @csrf
        <input type="text" name="word" placeholder="Enter a word" value="{{ old('word') }}">
        <button type="submit">Stem</button>
    </form>

    @if (isset($root))
        <p><strong>Original:</strong> {{ $original }}</p>
        <p><strong>Root:</strong> {{ $root }}</p>
        <p style="color: green;"><strong>Status:</strong> {{ $status }}</p>
    @endif
</body>
</html> --}}

@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-center align-items-center vh-100 bg-light">
    <div class="card shadow-sm" style="width: 35rem;">
        <div class="card-header text-center text-light" style="background-color: #556B2F;">
            <h2>Cari Kata Dasar</h2>
        </div>
        <div class="card-body">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('stem.process') }}">
                @csrf
                <div class="mb-3">
                    <input 
                        type="text" 
                        name="word" 
                        class="form-control" 
                        placeholder="Contoh: menyelesaikan" 
                        required 
                        pattern="^[a-zA-Z\-]+$"
                        title="Masukkan hanya satu kata, huruf saja tanpa angka atau spasi"
                        value="{{ isset($root) ? '' : old('word', $original ?? '') }}">
                </div>
                <div class="text-center">
                    <button type="submit" class="btn text-white w-25" style="background-color: #a89e2d;">Proses</button>
                </div>                
            </form>

            @isset($root)
                <hr>
                <div class="mt-3 text-center">
                    <p class="mb-1">Kata Kunci:</p>
                    <h5 class="text-success">{{ $original }}</h5>
                </div>
                <div class="mt-3 text-center">
                    <p class="mb-1">Kata Asal:</p>
                    <h5 class="text-success">{{ $root }}</h5>
                </div>
                <div class="mt-3 text-center">
                    <p class="mb-1">Pesan</p>
                    <h5 class="text-success">{{ $status }}</h5>
                </div>
            @endisset
        </div>
    </div>
</div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const resetBtn = document.querySelector('button[type="reset"]');
        const inputField = document.querySelector('input[name="word"]');

        resetBtn.addEventListener('click', function () {
            inputField.value = '';
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const resetBtn = document.querySelector('button[type="reset"]');
        const inputField = document.querySelector('input[name="word"]');

        resetBtn.addEventListener('click', function () {
            inputField.value = '';
        });
    });
</script>

