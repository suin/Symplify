<?php declare(strict_types=1);

namespace Symplify\PHPStanExtensions\Rules\Methods;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\Error;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Broker\Broker;
use PHPStan\Rules\Rule;
use function Safe\sprintf;

final class AbstractionOverImplementationRule implements Rule
{
    /**
     * @var Broker
     */
    private $broker;

    public function __construct(Broker $broker)
    {
        $this->broker = $broker;
    }

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     * @return string[] errors
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (empty($node->params)) {
            return [];
        }

        $errors = [];
        foreach ($node->params as $param) {
            if ($this->shouldSkipParamNode($param)) {
                continue;
            }

            $typeName = $param->type->toString();
            $class = $this->broker->getClass($typeName);
            if ($class->getInterfaces() === []) {
                continue;
            }

            $preferredInterface = $this->selectPreferredInterface(array_keys($class->getInterfaces()));

            if ($param->var instanceof Error) {
                continue;
            }

            $message = sprintf(
                'You should use interface "%s" instead of "%s" class as a typehint for "$%s" argument',
                $preferredInterface,
                $typeName,
                (string) $param->var->name
            );

            $errors[] = $message;
        }

        return $errors;
    }

    private function shouldSkipParamNode(Param $paramNode): bool
    {
        if (empty($paramNode->type)) {
            return true;
        }

        $typeName = $paramNode->type->toString();

        return $this->broker->hasClass($typeName) === false;
    }

    /**
     * @param string[] $interfaces
     */
    private function selectPreferredInterface(array $interfaces): string
    {
        foreach ($interfaces as $interface) {
            if (Strings::startsWith($interface, 'Psr\\')) {
                return $interface;
            }
        }

        return array_pop($interfaces);
    }
}
