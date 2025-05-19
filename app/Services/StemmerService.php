<?php

namespace App\Services;

class StemmerService
{
    protected array $corpus = [];

    public function __construct()
    {
        $this->loadCorpus(); // ini akan mengisi $this->corpus
    }

    public function stem(string $word): array
    {
        $originalWord = strtolower($word);
        $word = $originalWord;

        if ($this->isInCorpus($word)) {
            echo "Ditemukan di corpus: $word\n";
            return [
                'root' => $word,
                'status' => 'Kata sudah merupakan kata dasar'
            ];
        }          

        // 1. Remove Possesive Pronoun Particle
        $word = $this->removeParticleSuffix($word);

        if ($this->isInCorpus($word)) {
            echo "Ditemukan di corpus: $word\n";
            return [
                'root' => $word,
                'status' => 'Kata ditemukan setelah remove Particle (tahap 1)'
            ];
        } 

        // 2. Remove PPP2 (ku, mu, nya)
        $word = $this->removePossessivePronounSuffix($word);
        
        // 3. Remove -i, -kan, -an
        $word = $this->removeDerivationalSuffix($word);

        // 4. Remove prefix Ke
        $word = $this->removeKePrefix($word);

        // 5. Remove prefix Ke
        $word = $this->removePePrefix($word);

        // 6. Remove prefix Me
        $word = $this->removeMePrefix($word);

        if (!$this->isInCorpus($word)) {
            return [
                'root' => $originalWord,
                'status' => 'Not found in corpus, returning original word'
            ];
        }

        return [
            'root' => $word,
            'status' => 'Berhasil diproses.',
        ];
    }

    private function removeParticleSuffix(string $word): string
    {
        $original = $word;

        if (str_ends_with($word, 'kah')) {
            $word = preg_replace('/kah$/', '', $word);
        } elseif (str_ends_with($word, 'lah')) {
            $word = preg_replace('/lah$/', '', $word);
        } elseif (str_ends_with($word, 'tah')) {
            $word = preg_replace('/tah$/', '', $word);
        } elseif (str_ends_with($word, 'pun')) {
            $word = preg_replace('/pun$/', '', $word);
        }
        return $word ?: $original;
    }

    private function removePossessivePronounSuffix(string $word): string
    {
        $original = $word;

        if (str_ends_with($word, 'ku')) {
            $word = preg_replace('/ku$/', '', $word);
        } elseif (str_ends_with($word, 'mu')) {
            $word = preg_replace('/mu$/', '', $word);
        } elseif (str_ends_with($word, 'nya')) {
            // Jika kata diawali 'ber', jangan hapus 'nya'
            if (str_starts_with($word, 'ber')) {
                return $word;
            }
            $word = preg_replace('/nya$/', '', $word);
        }
        return $word ?: $original;
    }

    private function removeDerivationalSuffix(string $word): string
    {
        $original = $word;

        // Rule: -i
        if (str_ends_with($word, 'i')) {
            if (
                (str_starts_with($word, 'men') && substr($word, 3, 1) !== 'g') ||
                str_starts_with($word, 'ber') ||
                str_starts_with($word, 'di')
            ) {
                return $word;
            }
            return substr($word, 0, -1);
        }

        // Rule: -kan
        if (str_ends_with($word, 'kan')) {
            return substr($word, 0, -3);
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

        return $original;
    }

    private function removeMePrefix(string $word): string
    {
        $original = $word;

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
            if (in_array($fifthChar, ['g', 'h', 'q', 'u', 'i'])) {
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
