# bot-sandbox-test

The demo repo that [Mantle Factory](https://github.com/RiouTenkei/dev.mantle-factory) bots work on. A small Symfony 7.4 JSON API with real tests and no secrets.

```bash
composer install
composer test
```

Endpoints: `GET /api/items`, `GET /api/items/{id}`.

`main` is protected: changes only via PR, one human approval, CI green, no bypass. Bots open PRs; only a person can merge.
