<?php namespace Search\Tokenizer;


class SnowballTokenizer implements Tokenizer {

    use TokenizeTrait;

    private $language;

    private $tokens;
    private $stemms;
    private $stopwords;

    function __construct($language = 'english', $stopwords = []) {
        $this->language = $language;
        $this->stopwords = $stopwords;
    }

    /**
     * Tokenize the given string
     * @param $string String to tokenize
     * @return array Array of tokens
     */
    public function tokenize($string) {
        $this->tokens = $this->toTokens($string);
        $this->stemms = $this->stem($this->tokens);
        return $this->encode($this->stemms);
    }

    public function getWord($stem) {
        $allWords = [];
        foreach ($this->stemms as $key => $token) {
            $allWords[$token][] = $this->tokens[$key];
        }

        $stemToWord = [];
        foreach ($this->stemms as $key => $token) {
            $counts = array_count_values($allWords[$token]);
            arsort($counts);
            $stemToWord[$token] = array_keys($counts)[0];
        }

        return $stemToWord[$stem];
    }

    private function stem($tokens) {
        $stem = 'stem_' . $this->language;

        return array_map(function($token) use ($stem) {
            if (in_array($token, $this->stopwords)) {
                return $token;
            }
            return $stem($token);

        }, $tokens);
    }
}