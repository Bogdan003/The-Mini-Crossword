<?php

class Crosswords extends ArrayObject
{
    CONST CELL = 0;
    CONST WORD = 1;
    CONST PUZZLE = 2;
    CONST HELP_TEXT = 3;
    CONST TIMER = 4;

    private $jsonData;
    public $maxRows;
    public $maxColumns;
    public $crosswords;

    public function __construct($jsonData)
    {
        parent::__construct($jsonData, ArrayObject::ARRAY_AS_PROPS);

        $this->jsonData = $jsonData;
    }

    public function setTable():array
    {
        $directions = array_column($this->jsonData, 'direction');
        $tableDimensions = array_count_values($directions);

        $this->maxRows = $tableDimensions['across'];
        $this->maxColumns = $tableDimensions['down'];

        for ($row=0; $row < $this->maxRows; $row++) {
            $this->crosswords[] = str_repeat(' ', $this->maxColumns);
        }

        return [
            'rows'      => $tableDimensions['across'],
            'columns'   => $tableDimensions['down']
        ];
    }

    public function fillCrosswords():array
    {
        $acrossWords = [];
        $downWords = [];
        foreach ($this->jsonData as $word) {
            if ($word['direction'] == 'across') {
                $acrossWords[] = $word['answer'];
            } else {
                $downWords[] = $word['answer'];
            }
        }

        $this->fillCrosswordsRecursively($acrossWords, $downWords);

        return $this->crosswords;
    }

    private function fillCrosswordsRecursively(array $acrossWords, array $downWords):void
    {
        // fill ACROSS full words
        foreach ($acrossWords as $key => $word) {

            $wordLength = strlen($word);

            if ($wordLength == $this->maxColumns) {
                $this->crosswords[$key] = $word;
            } else {

                if ($this->crosswords[$key] == str_repeat(' ', $this->maxColumns)) continue;

                for ($i=0; $i < $this->maxRows; $i++) {
                    $testingWord = $this->readString('across', $i);

                    if ($this->isMatching($testingWord, $word, 'across')) {
                        $this->fillWord($testingWord, $word, 'across', $i);
                    }
                }
            }
        }

        // fill DOWN full words
        foreach ($downWords as $key => $word) {
            $wordLength = strlen($word);

            if ($wordLength == $this->maxRows) {
                for ($i=0; $i < $this->maxColumns; $i++) {
                    $testingWord = $this->readString('down', $i);

                    if ($this->isMatching($testingWord, $word, 'down')) {
                        $this->fillWord($testingWord, $word, 'down', $i);
                    }
                }

            }
            else {
                for ($i=0; $i < $this->maxColumns; $i++) {
                    $testingWord = $this->readString('down', $i);

                    if ($this->isMatching($testingWord, $word, 'down')) {
                        $this->fillWord($testingWord, $word, 'down', $i);
                    }
                }
            }
        }

        // repeat untill is the crosswords is completed
        foreach ($this->crosswords as $word) {
            if (strpos($word, ' ') !== false ) {
                $this->fillCrosswordsRecursively($acrossWords, $downWords);
            }
        }
    }

    private function readString(string $direction, int $position):string
    {
        $composedWord = '';
        if ($direction == 'down') {
            foreach ($this->crosswords as $word) {
                $composedWord .= $word[$position];
            }
        } else {
            $composedWord = $this->crosswords[$position];
        }

        return $composedWord;
    }

    private function isMatching(string $testingString, string $answer, string $direction):bool
    {
        $answerArr = str_split($answer);
        $testingStringArr = str_split($testingString);

        $matchedLetters = array_intersect($testingStringArr, $answerArr);
        $matchedLetterKey = count($matchedLetters) == 1 ? array_search(reset($matchedLetters), $matchedLetters) : null;

        $pattern = '';
        $trimmedArr = str_split(trim($testingString, ' -'));
        foreach($trimmedArr as $letter) {
            $pattern .= $letter == ' ' ? '.*' : $letter;
        }
        $pattern = "~$pattern~i";
        $lettersAreOrdered = preg_match($pattern, $answer);

        $diff_letters = array_diff($testingStringArr, $answerArr, [' ', '-']);

        $response = count($diff_letters) == 0 && $lettersAreOrdered !== 0 &&
            (
                (trim($testingString, ' -') == $answer) ||
                ($direction == 'across' && strlen($answer) == $this->maxColumns && count($matchedLetters) >= 2) ||
                ($direction == 'across' && ($this->maxColumns - strlen($answer) <= 1) && !empty($matchedLetterKey) && $matchedLetters[$matchedLetterKey] == $answerArr[$matchedLetterKey]) ||
                ($direction == 'down' && strlen($answer) == $this->maxRows && count($matchedLetters) >= 2) ||
                ($direction == 'down' && ($this->maxRows - strlen($answer) <= 1) && !empty($matchedLetterKey) && $matchedLetters[$matchedLetterKey] == $answerArr[$matchedLetterKey]) ||
                (count($matchedLetters) >= 2 && (reset($matchedLetters) == reset($answerArr))) ||
                (count($matchedLetters) >= 2 && (end($matchedLetters) == end($answerArr))) ||
                (count($matchedLetters) >= (strlen($answer) / 2))
            );

        return $response;
    }

    private function fillWord(string $matchedWord, string $answer, string $direction, int $position):void
    {
        $answerArr = str_split($answer);
        $matchedWordArr = str_split($matchedWord);
        $matched_letters = array_intersect($matchedWordArr, $answerArr);
        $isMatchingFirstLetter = reset($matched_letters) == reset($answerArr);
        $isMatchingLastLetter = end($matched_letters) == end($answerArr);

        foreach ($matchedWordArr as $key => $letter) {
            if ($direction == 'down' && $letter == ' ') {
                if (strlen($answer) == $this->maxRows) {
                    $this->crosswords[$key][$position] = $answer[$key];
                }

                if (strlen($answer) == strlen(trim($matchedWord, ' -'))) {
                    $this->crosswords[$key][$position] = '-';
                }

                if ($isMatchingFirstLetter && strlen(ltrim($matchedWord, ' -')) == strlen($answer)) {

                    $first_letter_key = array_search(reset($answerArr), $matchedWordArr);
                    $last_letter_key = $first_letter_key + strlen($answer) + 1;

                    if ($key < $first_letter_key || $key > $last_letter_key) {
                        $this->crosswords[$key][$position] = '-';
                    }

                    if ($key >= $first_letter_key && $key <= $last_letter_key) {
                        if (isset($answerArr[$key - $first_letter_key])) {
                            $this->crosswords[$key][$position] = $answerArr[$key - $first_letter_key];
                        }
                    }
                }

                if ($isMatchingLastLetter && strlen(rtrim($matchedWord, ' -')) == strlen($answer)) {

                    $last_letter_key = array_search(end($answerArr), $matchedWordArr);
                    $first_letter_key = $last_letter_key - strlen($answer) + 1;

                    if ($key < $first_letter_key || $key > $last_letter_key) {
                        $this->crosswords[$key][$position] = '-';
                    }

                    if ($key >= $first_letter_key && $key <= $last_letter_key) {
                        $this->crosswords[$key][$position] = $answerArr[$key];
                    }
                }

                if (count($matched_letters) == 1 && $this->maxRows - count($answerArr) <= 1) {
                    $indexAnswer = array_search(reset($matched_letters), $answerArr);

                    if ($key >= key($matched_letters) - $indexAnswer && $key < count($answerArr)) {
                        $this->crosswords[$key][$position] = $answerArr[$key];
                    }
                }
            }

            if ($direction == 'across' && $letter == ' ') {
                if (strlen($answer) == $this->maxColumns) {
                    $this->crosswords[$position][$key] = $answer[$key];
                }

                if (strlen($answer) == strlen(trim($matchedWord, ' -'))) {
                    $this->crosswords[$position][$key] = '-';
                }

                if ($isMatchingFirstLetter) {

                    $first_letter_key = array_search(reset($answerArr), $matchedWordArr);
                    $last_letter_key = $first_letter_key + strlen($answer) + 1;

                    if ($key < $first_letter_key || $key > $last_letter_key) {
                        $this->crosswords[$position][$key] = '-';
                    }

                    if ($key >= $first_letter_key && $key <= $last_letter_key) {
                        if (isset($answerArr[$key - $first_letter_key])) {
                            $this->crosswords[$position][$key] = $answerArr[$key - $first_letter_key];
                        }

                    }
                }

                if ($isMatchingLastLetter) {

                    $last_letter_key = array_search(end($answerArr), $matchedWordArr);
                    $first_letter_key = $last_letter_key - strlen($answer) + 1;

                    if ($key < $first_letter_key || $key > $last_letter_key) {
                        $this->crosswords[$position][$key] = '-';
                    }

                    if ($key >= $first_letter_key && $key <= $last_letter_key) {
                        if (isset($answerArr[$key - $first_letter_key])) {
                            $this->crosswords[$position][$key] = $answerArr[$key - $first_letter_key];
                        }
                    }
                }

                if (count($matched_letters) == 1 && $this->maxRows - count($answerArr) <= 1) {
                    $indexAnswer = array_search(reset($matched_letters), $answerArr);

                    if ($key >= key($matched_letters) - $indexAnswer && $key < count($answerArr)) {
                        $this->crosswords[$position][$key] = $answerArr[$key];
                    }
                }
            }
        }
    }

    public function reveal(int $revealType, string $direction = null, int $position = null):mixed
    {
        //TODO
    }

    public function check(int $checkType, string $direction = null, int $position = null):mixed
    {
        //TODO
    }

    public function rebus(string $text, string $direction, int $position)
    {
        //TODO
    }

    public function clear(int $clearType, string $direction = null, int $position = null):mixed
    {
        //TODO
    }

    public function reset():void
    {
        //TODO
    }
}
