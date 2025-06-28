<?php

declare(strict_types=1);

namespace IBroStudio\Git\Enums;

use IBroStudio\Git\Attributes\CommitLabel;
use ReflectionEnum;
use ReflectionException;

enum CommitTypeEnum: string
{
    #[CommitLabel('⚠ BREAKING CHANGES')]
    case BREAKING_CHANGES = 'breaking_changes';

    #[CommitLabel('Features')]
    case FEAT = 'feat';

    #[CommitLabel('Bug Fixes')]
    case FIX = 'fix';

    #[CommitLabel('Builds')]
    case BUILD = 'build';

    #[CommitLabel('Chores')]
    case CHORE = 'chore';

    #[CommitLabel('CI')]
    case CI = 'ci';

    #[CommitLabel('Documentation')]
    case DOCS = 'docs';

    #[CommitLabel('Performances')]
    case PERF = 'perf';

    #[CommitLabel('Refactoring')]
    case REFACTOR = 'refactor';

    #[CommitLabel('Reverting')]
    case REVERT = 'revert';

    #[CommitLabel('Styles')]
    case STYLE = 'style';

    #[CommitLabel('Tests')]
    case TEST = 'test';

    case UNLISTED = 'unlisted';

    public static function values(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }

    public function label(): ?string
    {
        return $this->getLabelAttribute()?->text;
    }

    private function getLabelAttribute(): ?CommitLabel
    {
        return once(function () {
            try {
                $reflection = new ReflectionEnum($this);
                $attributes = $reflection->getCase($this->name)->getAttributes(CommitLabel::class);
            } catch (ReflectionException) {
                return null;
            }

            return ($attributes[0] ?? null)?->newInstance();
        });
    }
}
