<?php declare(strict_types=1);

namespace Symplify\PHPStanExtensions\Rules\Methods;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\Error;
use PhpParser\Node\NullableType;
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

            $typeName = $this->getParamType($param);
            $class = $this->broker->getClass($typeName);
            $preferredInterface = $this->selectPreferredInterface(array_keys($class->getInterfaces()));

            if ($preferredInterface === null) {
                continue;
            }

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

        $typeName = $this->getParamType($paramNode);

        return $this->broker->hasClass($typeName) === false;
    }

    private function getParamType(Param $paramNode): string
    {
        if ($paramNode->type instanceof NullableType) {
            return (string) $paramNode->type->type;
        }

        return (string) $paramNode->type;
    }

    /**
     * @param string[] $interfaces
     */
    private function selectPreferredInterface(array $interfaces): ?string
    {
        if ($interfaces === []) {
            return null;
        }

        foreach ($interfaces as $interface) {
            if (Strings::startsWith($interface, 'Psr\\')) {
                return $interface;
            }
        }

        $interface = array_pop($interfaces);

        // excluded interfaces
        if (in_array($interface, ['Iterator', 'PHPStan\PhpDocParser\Ast\Node'], true)) {
            return null;
        }

        return $interface;
    }
}
