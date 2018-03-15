<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 3/15/18
 * Time: 2:24 PM
 */

namespace PhpCsFixer\Fixer\ControlStructure;


use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

class LogicalOperatorFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound(
            [
                T_LOGICAL_AND,
                T_LOGICAL_OR,
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

            if ($token->isGivenKind(T_LOGICAL_OR)) {
                $tokens->clearAt($index);
                $tokens->insertAt($index, new Token([T_BOOLEAN_OR, "||"]));
            }
            if ($token->isGivenKind(T_LOGICAL_AND)) {
                $tokens->clearAt($index);
                $tokens->insertAt($index, new Token([T_BOOLEAN_AND, "&&"]));
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
}