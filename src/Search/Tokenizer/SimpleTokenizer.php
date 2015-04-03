<?php namespace Search\Tokenizer;

/**
 * Class SimpleTokenizer
 * @package Search
 */
class SimpleTokenizer implements Tokenizer
{

    use TokenizeTrait;

    /**
     * Tokenize the given string
     * Splits the string on space and punctuations
     * @param String $string String to tokenize
     * @return array string tokens
     */
    public function tokenize($string)
    {
        return $this->encode($this->toTokens($string));
    }
}

