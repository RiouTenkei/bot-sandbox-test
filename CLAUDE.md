# bot-sandbox-test

A small Symfony 7.4 / PHP 8.4 JSON API that Mantle Factory bots work on. It has no database: the catalogue is fixed, in memory.

## Layout

```
src/Module/<Domain>/
    Domain/          value objects
    Repository/      data access
    Http/Controller/ final, attribute-routed controllers under /api, returning JsonResponse
    Http/View/       the only place objects become arrays: static one() + many()
tests/Module/<Domain>/   WebTestCase functional tests
```

## Rules

- `declare(strict_types=1);` in every PHP file.
- Money is integer minor units; JSON shows `{amount, currency}`.
- Every change comes with tests. Run `composer test` and get it green before you finish.
- Don't weaken or delete existing tests to make them pass.
- Commit messages: Conventional Commits (`feat: …`, `fix: …`). Never add `Co-Authored-By`, `Claude-Session` or "Generated with" lines.
