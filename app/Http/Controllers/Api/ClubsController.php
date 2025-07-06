<?php

namespace App\Http\Controllers\Api;

use App\Club;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ClubsController extends Controller
{
    public function __construct()
    {
        $this->middleware('authenticate.access_token')->only(['search']);
    }

    /**
     * Search for clubs by name with fuzzy matching
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $searchTerm = $request->input('name');
        
        if (empty($searchTerm)) {
            return response()->json([
                'success' => false,
                'message' => 'Search term is required'
            ], 400);
        }

        // Load all clubs with their teams into memory
        $clubs = Club::with('teams')->where('visible', true)->get();

        // Remove Clube de Testes A and Clube de Testes B
        $clubs = $clubs->where('name', '!=', 'Clube de Testes A')->where('name', '!=', 'Clube de Testes B');

        $results = [];
        
        foreach ($clubs as $club) {
            $score = $this->calculateMatchScore($club->name, $searchTerm);
            
            if ($score > 0) {
                $teams = $club->teams->where('visible', true)->map(function ($team) {
                    return [
                        'id' => $team->id,
                        'name' => $team->name,
                    ];
                })->values();
                
                $results[] = [
                    'id' => $club->id,
                    'name' => $club->name,
                    'teams' => $teams,
                    'match_score' => $score,
                ];
            }
        }
        
        // Sort by match score (highest first)
        usort($results, function ($a, $b) {
            return $b['match_score'] <=> $a['match_score'];
        });
        
        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    }

    /**
     * Calculate match score between club name and search term
     *
     * @param string $clubName
     * @param string $searchTerm
     * @return float
     */
    private function calculateMatchScore($clubName, $searchTerm)
    {
        $clubName = trim($clubName);
        $searchTerm = trim($searchTerm);
        
        // Normalize strings for better matching (remove accents, convert to lowercase)
        $normalizedClub = $this->normalizeString($clubName);
        $normalizedSearch = $this->normalizeString($searchTerm);
        
        // 1. Exact match (highest score)
        if ($normalizedClub === $normalizedSearch) {
            return 1.0;
        }
        
        // 2. Case-insensitive exact match
        if (strtolower($clubName) === strtolower($searchTerm)) {
            return 0.95;
        }
        
        // 3. Club name is contained within search term (e.g., "Silva" in "Nucleo Desportivo da Silva")
        if (strpos($normalizedSearch, $normalizedClub) !== false) {
            $ratio = strlen($normalizedClub) / strlen($normalizedSearch);
            return 0.9 * $ratio; // Higher score for longer matches relative to search term
        }
        
        // 4. Search term is contained within club name
        if (strpos($normalizedClub, $normalizedSearch) !== false) {
            $ratio = strlen($normalizedSearch) / strlen($normalizedClub);
            return 0.8 * $ratio;
        }
        
        // 5. Word-based matching
        $clubWords = $this->getWords($normalizedClub);
        $searchWords = $this->getWords($normalizedSearch);
        
        $commonWords = array_intersect($clubWords, $searchWords);
        if (!empty($commonWords)) {
            $wordScore = count($commonWords) / max(count($clubWords), count($searchWords));
            return 0.7 * $wordScore;
        }
        
        // 6. Partial word matching (words that start the same way)
        $partialScore = $this->calculatePartialWordScore($clubWords, $searchWords);
        if ($partialScore > 0) {
            return 0.6 * $partialScore;
        }
        
        // 7. Character similarity for typos
        $similarity = $this->calculateCharacterSimilarity($normalizedClub, $normalizedSearch);
        if ($similarity > 0.6) { // Only return if similarity is decent
            return 0.4 * $similarity;
        }
        
        return 0; // No match
    }

    /**
     * Normalize string by removing accents and converting to lowercase
     *
     * @param string $string
     * @return string
     */
    private function normalizeString($string)
    {
        $normalizeChars = [
            'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A',
            'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
            'Ï'=>'I', 'Ñ'=>'N', 'Ń'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
            'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',
            'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',
            'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ń'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
            'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f',
            'ă'=>'a', 'î'=>'i', 'â'=>'a', 'ș'=>'s', 'ț'=>'t', 'Ă'=>'A', 'Î'=>'I', 'Â'=>'A', 'Ș'=>'S', 'Ț'=>'T',
        ];
        
        return strtolower(strtr($string, $normalizeChars));
    }

    /**
     * Split string into words, removing common Portuguese stop words
     *
     * @param string $string
     * @return array
     */
    private function getWords($string)
    {
        $words = preg_split('/\s+/', $string);
        $words = array_map('trim', $words);
        $words = array_filter($words);
        
        // Remove common Portuguese stop words
        $stopWords = ['da', 'de', 'do', 'das', 'dos', 'e', 'em', 'na', 'no', 'nas', 'nos', 'o', 'a', 'os', 'as'];
        $words = array_diff($words, $stopWords);
        
        return array_values($words);
    }

    /**
     * Calculate partial word matching score
     *
     * @param array $clubWords
     * @param array $searchWords
     * @return float
     */
    private function calculatePartialWordScore($clubWords, $searchWords)
    {
        $matches = 0;
        $maxWords = max(count($clubWords), count($searchWords));
        
        if ($maxWords === 0) {
            return 0;
        }
        
        foreach ($clubWords as $clubWord) {
            foreach ($searchWords as $searchWord) {
                // Check if words start the same way (at least 3 characters)
                if (strlen($clubWord) >= 3 && strlen($searchWord) >= 3) {
                    if (substr($clubWord, 0, 3) === substr($searchWord, 0, 3)) {
                        $matches++;
                        break;
                    }
                }
            }
        }
        
        return $matches / $maxWords;
    }

    /**
     * Calculate character-level similarity using a simple algorithm
     *
     * @param string $str1
     * @param string $str2
     * @return float
     */
    private function calculateCharacterSimilarity($str1, $str2)
    {
        $len1 = strlen($str1);
        $len2 = strlen($str2);
        
        if ($len1 === 0 || $len2 === 0) {
            return 0;
        }
        
        // Use a simple character overlap approach
        $commonChars = 0;
        $str1Chars = str_split($str1);
        $str2Chars = str_split($str2);
        
        foreach ($str1Chars as $char) {
            if (($key = array_search($char, $str2Chars)) !== false) {
                $commonChars++;
                unset($str2Chars[$key]); // Remove matched character to avoid double counting
            }
        }
        
        return $commonChars / max($len1, $len2);
    }
} 