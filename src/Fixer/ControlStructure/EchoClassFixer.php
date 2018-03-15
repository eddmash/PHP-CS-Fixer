<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 3/15/18
 * Time: 2:46 PM
 */

namespace PhpCsFixer\Fixer\ControlStructure;


use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

class EchoClassFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound(
            [
                T_CATCH,
                T_IF,
                T_ELSEIF,
                T_WHILE,
                T_FOR,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 1;
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        for ($index = count($tokens) - 1; 0 <= $index; --$index) {
            $token = $tokens[$index];

            if (!$token->isGivenKind(
                [
                    T_CATCH,
                    T_IF,
                    T_ELSEIF,
                    T_WHILE,
                    T_FOR,
                ]
            )) {
                continue;
            }

            $openBraceIndex = $tokens->getNextTokenOfKind($index, ["{"]);
            $openBrace = $tokens[$openBraceIndex];
            if (!$openBrace->equals("{")) {
                continue;
            }

            $nextIndex = $tokens->getNextMeaningfulToken($openBraceIndex);
            $nextItem = $tokens[$nextIndex];
            if ($nextItem->equals("}")) {
                $tokens->clearRange($openBraceIndex + 1, $nextIndex - 1);
                $tokens->insertAt($nextIndex, [new Token([T_ECHO, "echo '';\n"])]);
            }

        }
    }

    /**
     * Returns the definition of the fixer.
     *
     * @return FixerDefinitionInterface
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Replace control structure alternative syntax to use braces.',
            [
                new CodeSample(
                    "<?php\nif(true):echo 't';else:echo 'f';endif;\n"
                ),
            ]
        );
    }

    private function findParenthesisEnd(Tokens $tokens, $structureTokenIndex)
    {
        $nextIndex = $tokens->getNextMeaningfulToken($structureTokenIndex);
        $nextToken = $tokens[$nextIndex];

        // return if next token is not opening parenthesis
        if (!$nextToken->equals('(')) {
            return $structureTokenIndex;
        }

        return $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $nextIndex);
    }
}