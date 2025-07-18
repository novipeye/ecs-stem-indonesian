<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpWord\IOFactory as WordReader;
use Smalot\PdfParser\Parser as PdfParser;
use Illuminate\Support\Facades\Session;
use App\Exports\BulkExport;
use Maatwebsite\Excel\Facades\Excel;

class StemmingController extends Controller
{
    public function showBulkForm()
    {
        return view('stemming.bulk');
    }

    public function processBulk(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf,docx|max:2048',
        ]);

        $corpus = $this->loadCorpus();
        $file = $request->file('file');
        $text = '';

        if ($file->getClientOriginalExtension() === 'pdf') {
            $parser = new PdfParser();
            $pdf = $parser->parseFile($file->getPathname());
            $text = $pdf->getText();
        } elseif ($file->getClientOriginalExtension() === 'docx') {
            $phpWord = WordReader::load($file->getPathname(), 'Word2007');
            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if (method_exists($element, 'getText')) {
                        $text .= $element->getText() . ' ';
                    }
                }
            }
        }

        $words = $this->preProcessing($text);
        $results = [];

        $validCount = 0;
        $invalidCount = 0;

        foreach ($words as $word) {
            $word = trim($word);
            if ($word === '') continue;

            $stemmedResult = app('App\Services\StemmerService')->stem($word);
            $stemmed = is_array($stemmedResult) ? ($stemmedResult['root'] ?? implode(' ', $stemmedResult)) : $stemmedResult;

            $isValid = in_array($stemmed, $corpus);

            if ($isValid) {
                $validCount++;
            } else {
                $invalidCount++;
            }

            $results[] = [
                'original' => $word,
                'stemmed' => $stemmed,
                'status' => $isValid ? 'Valid' : 'Not Valid',
            ];
        }
        Session::put('stemming_results', $results);
        return view('stemming.result', compact('results', 'validCount', 'invalidCount'));
    }

    public function exportCSV()
    {
        $results = Session::get('stemming_results', []);
        if (empty($results)) {
            return redirect()->route('bulk.form')->with('error', 'No data to export.');
        }
        $csv = "Original,Stemmed,Status\n";

        foreach ($results as $row) {
            $original = $row['original'];
            $stemmed = is_array($row['stemmed']) ? implode(' ', $row['stemmed']) : $row['stemmed'];
            $status = $row['status'] ?? 'Unknown';

            $csv .= "{$original},{$stemmed},{$status}\n";
        }
        return Excel::download(new BulkExport($results), 'stemming_results.xlsx');
    }

    private function loadCorpus(): array
    {
        $path = storage_path('app/corpus.txt');
        if (!file_exists($path)) {
            return [];
        }
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        return array_map(function ($line) {
            return trim(preg_replace('/\s*\([^)]+\)/', '', $line));
        }, $lines);
    }

    private function preProcessing(string $text): array
    {
        $cleanText = strip_tags($text);
        $cleanText = strtolower($cleanText);
        $cleanText = preg_replace('/\d+/', '', $cleanText);
        $cleanText = preg_replace('/[^\p{L}\s]/u', '', $cleanText);
        $cleanText = preg_replace('/\s+/', ' ', $cleanText);
        $cleanText = trim($cleanText);
        $words = explode(' ', $cleanText);
        $stopwords = $this->loadStopwords();
        $filtered = array_filter($words, function ($word) use ($stopwords) {
            return !in_array($word, $stopwords);
        });
        return array_values($filtered);
    }

    private function loadStopwords(): array
    {
        $path = storage_path('app/stopwords.txt');
        if (!file_exists($path)) {
            return [];
        }
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        return array_map('trim', $lines);
    }
}
