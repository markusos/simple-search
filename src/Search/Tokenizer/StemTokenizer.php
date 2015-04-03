<?php namespace Search\Tokenizer;

abstract class StemTokenizer implements Tokenizer
{

    use TokenizeTrait;

    /**
     * Tokenize the given string
     * @param $string String to tokenize
     * @return array Array of tokens
     */
    public function tokenize($string)
    {
        $this->tokens = $this->toTokens($string);
        $this->stemms = $this->stem($this->tokens);
        return $this->encode($this->stemms);
    }

    /**
     * Stem all tokens in the input array
     * @param $tokens array of tokens to stem
     * @return array Array of the stemmed tokens
     */
    abstract protected function stem(array $tokens);

    /**
     * Get the original word from a stem
     * @param String $stem to find original word for.
     * @return String the original word
     */
    public function getWord($stem)
    {
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
}