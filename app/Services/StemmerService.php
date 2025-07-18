<?php

namespace App\Services;

class StemmerService
{
    protected array $corpus = [];

    private array $manualExceptions = [];

    public function __construct()
    {
        $this->loadCorpus();
    }

    private function loadManualExceptions(): void
    {
        if (empty($this->manualExceptions)) {
            $path = storage_path('app/word_exception.txt');

            if (file_exists($path)) {
                $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($lines as $line) {
                    if (str_contains($line, '=')) {
                        [$key, $value] = explode('=', trim($line), 2);
                        $this->manualExceptions[trim($key)] = trim($value);
                    }
                }
            }
        }
    }

    public function stem(string $word): array
    {
        $this->loadManualExceptions();
        $originalWord = strtolower($word);

        if (isset($this->manualExceptions[$originalWord])) {
            return [
                'root' => $this->manualExceptions[$originalWord],
                'status' => 'Kata ditemukan dalam file eksepsi'
            ];
         }

        $word = $originalWord;

        // Pengecekan pertama ke dalam corpus, jika ditemukan maka proses stem berhenti
        if ($this->isInCorpus($word)) {
            return [
                'root' => $word,
                'status' => 'Kata sudah merupakan kata dasar'
            ];
        }        
        
        // Penanganan repetitive word seperti: berlari-larian
        $word = $this->stemWithReduplication($word);
        if ($this->isInCorpus($word)) {
            return [
                'root' => $word,
                'status' => 'Kata merupakan repetitive word, ditangani secara khusus'
            ];
        } 

        // 1. Hapus Possesive Pronoun Particle (kah, lah, tah, pun)
        $word = $this->removeParticleSuffix($word);
        if ($this->isInCorpus($word)) {
            return [
                'root' => $word,
                'status' => 'Kata ditemukan setelah remove Particle (kah, lah, tah, pun) (tahap 1)'
            ];
        } 

        // 2. Hapus Inflectional Possesive Pronoun (ku, mu, nya)
        $word = $this->removePossessivePronounSuffix($word);
        if ($this->isInCorpus($word)) {
            return [
                'root' => $word,
                'status' => 'Kata ditemukan setelah remove Inflectional Possesive Pronoun (ku, mu, nya) (tahap 2)'
            ];
        } 
        
        // 3. Hapus Derivational Suffix (-i, -kan, -an)
        $word = $this->removeDerivationalSuffix($word);

        if ($this->isInCorpus($word)) {
            return [
                'root' => $word,
                'status' => 'Kata ditemukan setelah remove Derivational Suffix (-i,  -kan, -an) (tahap 3)'
            ];
        } 

        // 4. Remove prefix Ke
        $word = $this->removeKePrefix($word);

        if ($this->isInCorpus($word)) {
            return [
                'root' => $word,
                'status' => 'Kata ditemukan setelah remove prefix Ke (tahap 4)'
            ];
        }

        // 5. Remove prefix Pe
        $word = $this->removePePrefix($word);

        if ($this->isInCorpus($word)) {
            return [
                'root' => $word,
                'status' => 'Kata ditemukan setelah remove prefix Pe (tahap 4)'
            ];
        }

        // 6. Remove prefix Me
        $word = $this->removeMePrefix($word);

        if ($this->isInCorpus($word)) {
            return [
                'root' => $word,
                'status' => 'Kata ditemukan setelah remove prefix Me (tahap 4)'
            ];
        }

        // Implementasi special rule, khususnya mem lalu per
        $prefixesRemoved = 4;
        while ($prefixesRemoved < 7) {
            $newWord = $this->removeAnyPrefix($word);
            if ($newWord === $word) break;
            $word = $newWord;
            $prefixesRemoved++;

            if ($this->isInCorpus($word)) {
                return [
                    'root' => $word,
                    'status' => "Kata ditemukan setelah penghapusan prefix aturan khusus ke-$prefixesRemoved"
                ];
            }
        }

        if($word = $originalWord){
            $word = $this->removeInfix($word);
            return [
                'root' => $word,
                'status' => 'Kata sisipan, ditangani secara khusus'
            ];
        }

        return [
            'root' => $word,
            'status' => 'Tidak ditemukan di corpus',
        ];
    }

    private function removeParticleSuffix(string $word): string
    {
        $original = $word;
        $particles = ['kah', 'lah', 'tah', 'pun'];

        foreach ($particles as $suffix) {
            if (str_ends_with($word, $suffix)) {
                return preg_replace('/' . $suffix . '$/', '', $word);
            }
        }
        return $original;
    }

    private function removePossessivePronounSuffix(string $word): string
    {
        $original = $word;
        $suffixes = ['ku', 'mu', 'nya'];
        foreach ($suffixes as $suffix) {
            if (str_ends_with($word, $suffix)) {
                // 'nya' tidak dihapus jika kata memiliki awalan ber
                if ($suffix === 'nya' && str_starts_with($word, 'ber')) {
                    return $original;
                }
                return preg_replace('/' . $suffix . '$/', '', $word);
            }
        }
        return $original;
    }

    private function removeDerivationalSuffix(string $word): string
    {
        $original = $word;

        // Rule: -i
        if (str_ends_with($word, 'i')) {
            if (
                (str_starts_with($word, 'men') && substr($word, 3, 1) !== 'g') || str_starts_with($word, 'meng') || str_starts_with($word, 'mem') || 
                str_starts_with($word, 'ber') 
                || str_starts_with($word, 'di') || str_starts_with($word, 'pe') || str_starts_with($word, 'pem')
            ) {
                return $word;
            }
            return substr($word, 0, -1);
        }

        // Rule: -kan
        if (str_ends_with($word, 'kan')) {
            return substr($word, 0, -3);
        }

        // Rule: -an, with ber- prefix
        if (str_starts_with($word, 'ber') && str_ends_with($word, 'an')) {
            return $word;
        }

        // Rule: -an
        if (str_ends_with($word, 'an')) {
            return substr($word, 0, -2);
        }
        
        return $original;
    }

    private function removeKePrefix(string $word): string
    {
        $original = $word;

        // Awalan 2 huruf
        if (str_starts_with($word, 'ke')) {
            return preg_replace('/^ke/', '', $word);
        } elseif (str_starts_with($word, 'se')) {
            return preg_replace('/^se/', '', $word);
        } elseif (str_starts_with($word, 'di')) {
            return preg_replace('/^di/', '', $word);
        }

        // Awalan 'ber' dengan vokal setelahnya
        if (str_starts_with($word, 'ber') && in_array($word[3] ?? '', ['a','i','u','e','o'])) {
            return preg_replace('/^ber/', '', $word);
        }

        // Awalan 'ber' dengan pengecualian dan bukan 'er' setelah indeks 5
        if (str_starts_with($word, 'ber')) {
            $nextChar = $word[3] ?? '';
            $nextTwo = substr($word, 5, 2);
            if (!in_array($nextChar, ['r','a','i','u','e','o']) && $nextTwo !== 'er') {
                return preg_replace('/^ber/', '', $word);
            }
        }

        // Pengecualian kata 'belajar' (hapus 'bel')
        if ($word === 'belajar') {
            return preg_replace('/^bel/', '', $word);
        }

        // Awalan 'be' dengan pengecualian huruf berikutnya
        if (str_starts_with($word, 'be')) {
            $nextChar = $word[2] ?? '';
            if (!in_array($nextChar, ['r','l','a','i','u','e','o'])) {
                return preg_replace('/^be/', '', $word);
            }
        }

        // Awalan 'ter' dengan vokal setelahnya
        if (str_starts_with($word, 'ter') && in_array($word[3] ?? '', ['a','i','u','e','o'])) {
            return preg_replace('/^ter/', '', $word);
        }

        // Awalan 'ter' dengan pengecualian dan bukan 'er' setelah indeks 5
        if (str_starts_with($word, 'ter')) {
            $nextChar = $word[3] ?? '';
            $nextTwo = substr($word, 5, 2);
            if (!in_array($nextChar, ['r','a','i','u','e','o']) && $nextTwo !== 'er') {
                return preg_replace('/^ter/', '', $word);
            }
        }

        // Awalan 'te' dengan pengecualian huruf berikutnya
        if (str_starts_with($word, 'te')) {
            $nextChar = $word[2] ?? '';
            if (!in_array($nextChar, ['r','l','a','i','u','e','o'])) {
                return preg_replace('/^te/', '', $word);
            }
        }
        return $original;
    }

    private function removePePrefix(string $word): string
    {
        $original = $word;

        if (str_starts_with($word, 'peny') && in_array($word[4] ?? '', ['a','i','u','e','o'])) {
            // 'peny' diganti 's'
            return preg_replace('/^peny/', 's', $word);
        }

        if (str_starts_with($word, 'peng')) {
            $fifthChar = $word[4] ?? '';
            if (in_array($fifthChar, ['g','h','q'])) {
                // 'peng' dihapus
                return preg_replace('/^peng/', '', $word);
            } elseif (in_array($fifthChar, ['o','e','u'])) {
                // 'peng' diganti 'k'
                return preg_replace('/^peng/', 'k', $word);
            } elseif (in_array($fifthChar, ['a','i'])) {
                // 'peng' dihapus
                return preg_replace('/^peng/', '', $word);
            }
        }

        if (str_starts_with($word, 'pem')) {
            $fourthChar = $word[3] ?? '';
            $fifthChar = $word[4] ?? '';

            if (in_array($fourthChar, ['b','f','v'])) {
                // 'pem' dihapus
                return preg_replace('/^pem/', '', $word);
            } elseif ($fourthChar == 'r' && in_array($fifthChar, ['a','i','u','e','o'])) {
                // 'pem' diganti 'p'
                return preg_replace('/^pem/', 'p', $word);
            } elseif (in_array($fourthChar, ['a','i','u','e','o'])) {
                // 'pem' diganti 'p'
                return preg_replace('/^pem/', 'p', $word);
            }
        }

        if (str_starts_with($word, 'pen')) {
            $fourthChar = $word[3] ?? '';
            if (in_array($fourthChar, ['c','d','j','z'])) {
                // 'pen' dihapus
                return preg_replace('/^pen/', '', $word);
            } elseif (in_array($fourthChar, ['a','i','u','e','o'])) {
                // 'pen' diganti 't'
                return preg_replace('/^pen/', 't', $word);
            }
        }

        if (str_starts_with($word, 'pel')) {
            if ($word === 'pelajar') {
                return preg_replace('/^pel/', '', $word);
            }
            return preg_replace('/^pe/', '', $word);
        }

        if (str_starts_with($word, 'per')) {
            $fourthChar = $word[3] ?? '';
            if (in_array($fourthChar, ['a','i','u','e','o','p','b','t','s'])) {
                // 'per' dihapus
                return preg_replace('/^per/', '', $word);
            }
        }

        if (str_starts_with($word, 'pe')) {
            $thirdChar = $word[2] ?? '';
            $fourthChar = $word[3] ?? '';
            if (in_array($thirdChar, ['w','y']) && in_array($fourthChar, ['a','i','u','e','o'])) {
                // 'pe' dihapus
                return preg_replace('/^pe/', '', $word);
            }
        }

        if (str_starts_with($word, 'pe')) {
            return preg_replace('/^pe/', '', $word);
        }

        return $original;
    }

    private function removePerPrefix(string $word): string
    {
        $original = $word;

        if (str_starts_with($word, 'per')) {
            $fourthChar = $word[3] ?? '';
            if (in_array($fourthChar, ['a','i','u','e','o','p','b','t','s','j', 'c'])) {
                // 'per' dihapus
                return preg_replace('/^per/', '', $word);
            }
        }
        return $original;
    }

    private function removeMePrefix(string $word): string
    {
        $original = $word;

        // if (str_starts_with($word, 'memper')) {
        //     return preg_replace('/^memper/', '', $word);
        // }

        if (str_starts_with($word, 'mem')) {
            $fourthChar = $word[3] ?? '';
            if (in_array($fourthChar, ['a', 'i', 'u', 'e', 'o'])) {
                // 'mem' diganti 'p'
                return preg_replace('/^mem/', 'p', $word);
            } elseif (in_array($fourthChar, ['b', 'v', 'f', 'p'])) {
                // 'mem' dihapus
                return preg_replace('/^mem/', '', $word);
            }
        }

        if (str_starts_with($word, 'meny') && in_array($word[4] ?? '', ['a', 'i', 'u', 'e', 'o'])) {
            // 'meny' diganti 's'
            return preg_replace('/^meny/', 's', $word);
        }

        if (str_starts_with($word, 'meng')) {
            $fifthChar = $word[4] ?? '';
            if (in_array($fifthChar, ['g', 'h', 'q', 'u', 'i', 'k'])) {
                // 'meng' dihapus
                return preg_replace('/^meng/', '', $word);
            } elseif (in_array($fifthChar, ['o', 'e'])) {
                // 'meng' diganti 'k'
                return preg_replace('/^meng/', 'k', $word);
            } elseif ($fifthChar === 'a') {
                // 'meng' dihapus
                return preg_replace('/^meng/', '', $word);
            }
        }

        if (str_starts_with($word, 'men')) {
            $fourthChar = $word[3] ?? '';
            if (in_array($fourthChar, ['c', 'd', 'j', 'z'])) {
                // 'men' dihapus
                return preg_replace('/^men/', '', $word);
            } elseif (in_array($fourthChar, ['a', 'i', 'u', 'e', 'o'])) {
                // 'men' diganti 't'
                return preg_replace('/^men/', 't', $word);
            }
        }

        if (str_starts_with($word, 'me')) {
            $thirdChar = $word[2] ?? '';
            if (in_array($thirdChar, ['l', 'r', 'w', 'y'])) {
                // 'me' dihapus
                return preg_replace('/^me/', '', $word);
            }
        }
        
        return $original;
    }

    // Acommodate repetitive words, eg: berlari-larian
    public function stemWithReduplication(string $kata): string
    {
        if (strpos($kata, '-') !== false) {
            $parts = explode('-', $kata);
            $roots = [];

            foreach ($parts as $part) {
                $stemResult = $this->stem($part); // returns ['root' => ..., 'status' => ...]
                $roots[] = $stemResult['root'];
            }

            $uniqueRoots = array_unique($roots);

            // Return the first stemmed form that exists in the corpus
            foreach ($uniqueRoots as $root) {
                if ($this->isInCorpus($root)) {
                    return $root;
                }
            }

            // Fallback: return the first unique root
            return $uniqueRoots[0];
        }
        return $kata;
    }

    private function removeAnyPrefix(string $word): string
    {
        $original = $word;

        $prefixRemovers = [
            'removeMePrefix',
            'removeDiPrefix',
            'removeKePrefix',
            'removeSePrefix',
            'removeBePrefix',
            'removePerPrefix',
            'removeTePrefix'
        ];

        foreach ($prefixRemovers as $method) {
            if (method_exists($this, $method)) {
                $newWord = $this->$method($word);
                if ($newWord !== $word) {
                    return $newWord;
                }
            }
        }
        return $original;
    }

    function removeInfix(string $word): string
    {
        // Define infixes
        $infixes = ['el', 'er', 'em'];

        // Check if word length is at least 4 (to safely check position 2-3)
        if (strlen($word) >= 4) {
            $infixCandidate = substr($word, 1, 2); // position 2nd and 3rd (0-based index)

            if (in_array($infixCandidate, $infixes)) {
                // Remove the infix (characters at position 1 and 2)
                return substr($word, 0, 1) . substr($word, 3);
            }
        }

        // Return original word if no infix match
        return $word;
    }

    protected function isInCorpus($word)
    {
        return isset($this->corpus[$word]);
    } 

    protected function loadCorpus()
    {
        $path = 'D:\\xampp\\htdocs\\cs-stem-indonesian\\storage\\app\\corpus.txt';

        if (!file_exists($path)) {
            die("File corpus.txt tidak ditemukan di $path\n");
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $kata = trim(explode('(', $line)[0]);
            $kata = strtolower($kata);
            $this->corpus[$kata] = true;
        }
    }

}
