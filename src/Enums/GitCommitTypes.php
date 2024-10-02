<?php

namespace IBroStudio\Git\Enums;

use IBroStudio\Git\Attributes\Label;

enum GitCommitTypes: string
{
    #[Label('⚠ BREAKING CHANGES')]
    case BREAKING_CHANGES = 'breaking_changes';

    #[Label('Features')]
    case FEAT = 'feat';

    #[Label('Bug Fixes')]
    case FIX = 'fix';

    #[Label('Builds')]
    case BUILD = 'build';

    #[Label('Chores')]
    case CHORE = 'chore';

    #[Label('CI')]
    case CI = 'ci';

    #[Label('Documentation')]
    case DOCS = 'docs';

    #[Label('Performances')]
    case PERF = 'perf';

    #[Label('Refactoring')]
    case REFACTOR = 'refactor';

    #[Label('Reverting')]
    case REVERT = 'revert';

    #[Label('Styles')]
    case STYLE = 'style';

    #[Label('Tests')]
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

    private function getLabelAttribute(): ?Label
    {
        return once(function () {
            try {
                $reflection = new \ReflectionEnum($this);
                $attributes = $reflection->getCase($this->name)->getAttributes(Label::class);
            } catch (\ReflectionException) {
                return null;
            }

            return ($attributes[0] ?? null)?->newInstance();
        });
    }
}
