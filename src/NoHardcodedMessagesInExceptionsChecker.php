<?php

declare(strict_types=1);

namespace SavinMikhail\TranslatableExceptionsPlugin;

use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use Psalm\CodeLocation;
use Psalm\Issue\InvalidArgument;
use Psalm\IssueBuffer;
use Psalm\Plugin\EventHandler\AfterClassLikeAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterClassLikeAnalysisEvent;

use function is_array;
use function is_string;

final readonly class NoHardcodedMessagesInExceptionsChecker implements AfterClassLikeAnalysisInterface
{
    public static function afterStatementAnalysis(AfterClassLikeAnalysisEvent $event): void
    {
        $class_stmt = $event->getStmt();

        // Traverse class methods and their bodies
        foreach ($class_stmt->getMethods() as $method) {
            if ($method->getStmts() === null) {
                continue;
            }
            self::checkMethodStatements($method->getStmts(), $event);
        }
    }

    private static function checkMethodStatements(array $stmts, AfterClassLikeAnalysisEvent $event): void
    {
        foreach ($stmts as $stmt) {
            if ($stmt instanceof New_
                && $stmt->class instanceof Name
                && isset($stmt->args[0])
                && $stmt->args[0]->value instanceof String_
                && str_ends_with($stmt->class->toString(), 'Exception')
            ) {
                $code_location = new CodeLocation(
                    $event->getStatementsSource(),
                    $stmt->args[0]->value,
                );
                IssueBuffer::maybeAdd(
                    new InvalidArgument(
                        'Avoid hardcoding exception messages, use a translation mechanism instead.',
                        $code_location,
                    ),
                );
            }

            // Recursively check child nodes for nested statements
            if (is_string($stmt)) {
                return;
            }
            foreach ($stmt->getSubNodeNames() as $sub_node_name) {
                $sub_node = $stmt->{$sub_node_name};
                if (is_array($sub_node)) {
                    self::checkMethodStatements($sub_node, $event);
                } elseif ($sub_node instanceof Node) {
                    self::checkMethodStatements([$sub_node], $event);
                }
            }
        }
    }
}
